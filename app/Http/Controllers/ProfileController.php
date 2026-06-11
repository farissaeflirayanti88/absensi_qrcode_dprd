<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller  // ← Baris 14: { di sini
{
    /**
     * Menampilkan halaman profile
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update data profile
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'username' => 'required|string|max:50|unique:users,username,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        if (class_exists(ActivityLog::class)) {
            ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Memperbarui profil',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return redirect()->route('profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Password saat ini tidak sesuai.');
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        if (class_exists(ActivityLog::class)) {
            ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Mengubah password',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return redirect()->route('profile.edit')->with('success', 'Password berhasil diubah.');
    }

    /**
     * Upload Avatar
     */
    public function updateAvatar(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if ($user->avatar) {
            Storage::disk('public')->delete('avatars/' . $user->avatar);
        }

        $file = $request->file('avatar');
        $filename = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('avatars', $filename, 'public');
        
        $user->update([
            'avatar' => $filename
        ]);

        if (class_exists(ActivityLog::class)) {
            ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Memperbarui foto profil',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        return redirect()->route('profile.edit')->with('success', 'Foto profil berhasil diupload.');
    }

    /**
     * Hapus Avatar
     */
    public function deleteAvatar(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->avatar) {
            return redirect()->route('profile.edit')->with('error', 'Tidak ada foto profil untuk dihapus.');
        }

        Storage::disk('public')->delete('avatars/' . $user->avatar);
        
        $user->update([
            'avatar' => null
        ]);

        if (class_exists(ActivityLog::class)) {
            ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'Menghapus foto profil',
                'ip_address' => $request->ip(),
            ]);
        }

        // 🔥 HAPUS SEMUA SESSION SEBELUMNYA
        session()->forget('success');
        session()->forget('error');
        
        // 🔥 KIRIM FLASH BARU
        session()->flash('success', 'Foto profil berhasil dihapus.');

        return redirect()->route('profile.edit');
    }
    
} // ← INI BARIS 191 - TUTUP KURUNG CLASS