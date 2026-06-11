<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Participant;
use App\Models\Attendance;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ParticipantController extends Controller
{
    /**
     * Menampilkan daftar peserta
     */
    public function index(Request $request)
    {
        $query = Participant::withCount('attendances')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $participants      = $query->paginate(20)->withQueryString();
        $totalParticipants = Participant::count();
        $totalAttendance   = Attendance::count();

        $this->logActivity('Melihat daftar peserta');

        return view('participants.index', compact(
            'participants',
            'totalParticipants',
            'totalAttendance'
        ));
    }

    /**
     * Menampilkan detail peserta
     */
    public function show($id)
    {
        $participant = Participant::withCount('attendances')->findOrFail($id);
        $attendances = $participant->attendances()->with('event')->latest()->paginate(10);

        $this->logActivity('Melihat detail peserta: ' . $participant->name);

        return view('participants.show', compact('participant', 'attendances'));
    }

    /**
     * Menampilkan form edit peserta
     */
    public function edit($id)
    {
        $participant = Participant::findOrFail($id);
        return view('participants.edit', compact('participant'));
    }

    /**
     * Update data peserta
     */
    public function update(Request $request, $id)
    {
        $participant = Participant::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|min:3|max:100',
            'address' => 'nullable|string|max:255',
            'phone'   => [
                'required', 'string', 'regex:/^[0-9]{10,15}$/',
                Rule::unique('participants')->ignore($participant->id)
            ],
        ], [
            'phone.unique'  => 'Nomor telepon sudah digunakan peserta lain.',
            'phone.regex'   => 'Nomor telepon harus 10-15 digit angka.',
            'name.min'      => 'Nama minimal 3 karakter.',
            'name.required' => 'Nama wajib diisi.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $participant->update([
                'name'    => $this->formatName($request->name),
                'address' => trim($request->address),
                'phone'   => preg_replace('/\D/', '', $request->phone),
            ]);
            $this->logActivity('Memperbarui data peserta: ' . $participant->name);
            DB::commit();
            return redirect()->route('participants.show', $participant->id)
                ->with('success', 'Data peserta berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.')
                ->withInput();
        }
    }

    /**
     * Hapus peserta
     * - Kalau punya riwayat absensi -> tolak
     * - Kalau belum pernah absen    -> hapus permanen
     */
    public function destroy($id)
    {
        $participant = Participant::findOrFail($id);

        if ($participant->attendances()->exists()) {
            return redirect()->back()
                ->with('error', 'Peserta "' . $participant->name . '" tidak dapat dihapus karena memiliki riwayat kehadiran.');
        }

        DB::beginTransaction();
        try {
            $name = $participant->name;
            $participant->delete();
            $this->logActivity('Menghapus peserta: ' . $name);
            DB::commit();
            return redirect()->route('participants.index')
                ->with('success', 'Peserta "' . $name . '" berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    private function formatName($name)
    {
        $name = trim($name);
        if (strtoupper($name) === $name && strlen($name) > 3) {
            return Str::title($name);
        }
        return $name;
    }

    private function logActivity($activity)
    {
        if (!class_exists(ActivityLog::class)) return;
        try {
            ActivityLog::create([
                'user_id'    => auth()->id(),
                'activity'   => $activity,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to log activity: ' . $e->getMessage());
        }
    }
}