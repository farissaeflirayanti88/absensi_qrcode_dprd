<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Participant;
use App\Models\ActivityLog;

class ParticipantController extends Controller
{
    public function index(Request $request)
    {
        $query = Participant::withCount('attendances')->latest();
        
        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }
        
        $participants = $query->paginate(20);
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => 'Melihat daftar peserta',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return view('participants.index', compact('participants'));
    }

    public function show(Participant $participant)
    {
        $attendances = $participant->attendances()->with('event')->latest()->paginate(10);
        
        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => 'Melihat detail peserta: ' . $participant->name,
            'ip_address' => request()->ip(),
        ]);

        return view('participants.show', compact('participant', 'attendances'));
    }

    public function edit(Participant $participant)
    {
        return view('participants.edit', compact('participant'));
    }

    public function update(Request $request, Participant $participant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20|unique:participants,phone,' . $participant->id,
            'address' => 'required|string|max:255',
        ]);

        $participant->update($validated);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => 'Memperbarui data peserta: ' . $participant->name,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route('participants.show', $participant)->with('success', 'Data peserta berhasil diperbarui.');
    }

    public function destroy(Participant $participant)
    {
        $name = $participant->name;
        $participant->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => 'Menghapus peserta: ' . $name,
            'ip_address' => request()->ip(),
        ]);

        return redirect()->route('participants.index')->with('success', 'Peserta berhasil dihapus.');
    }
}