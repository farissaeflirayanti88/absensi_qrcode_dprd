<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Insert data users - sama persis dengan SQL dump
        DB::table('users')->insert([
            'id' => 1,
            'name' => 'Admin Utama',
            'email' => 'admin@example.com',
            'username' => 'admin1',
            'password' => '0192023a7bbd73250516f069df18b500', // password: admin123
            'role' => 'superadmin',
            'created_at' => '2025-10-15 08:41:42',
            'updated_at' => '2025-10-15 08:41:42',
        ]);

        // Insert data events
        DB::table('events')->insert([
            'id' => 1,
            'created_by' => 1,
            'event_name' => 'Reses DPRD Kota Batam - Wilayah Sagulung',
            'location' => 'Kantor Kelurahan Sagulung',
            'event_date' => '2025-10-20',
            'description' => 'Pertemuan anggota DPRD bersama masyarakat wilayah Sagulung.',
            'qr_code_hash' => 'QR_20251020_SAGULUNG',
            'is_active' => 1,
            'created_at' => '2025-10-15 08:41:42',
            'updated_at' => '2025-10-15 08:41:42',
        ]);

        // Insert data participants
        DB::table('participants')->insert([
            [
                'id' => 1,
                'name' => 'Budi Santoso',
                'address' => 'Perumahan Muka Kuning Asri Blok A3 No.5',
                'phone' => '081234567890',
                'created_at' => '2025-10-15 08:58:02',
            ],
            [
                'id' => 2,
                'name' => 'Siti Rahmawati',
                'address' => 'Kampung Bagan, Sagulung',
                'phone' => '081398765432',
                'created_at' => '2025-10-15 08:58:02',
            ]
        ]);

        // Insert data attendance
        DB::table('attendance')->insert([
            [
                'id' => 1,
                'event_id' => 1,
                'participant_id' => 1,
                'unique_code' => null,
                'attendance_time' => '2025-10-15 15:41:42',
                'notes' => 'Hadir tepat waktu',
                'created_at' => '2025-10-15 08:41:42',
            ],
            [
                'id' => 2,
                'event_id' => 1,
                'participant_id' => 2,
                'unique_code' => null,
                'attendance_time' => '2025-10-15 15:41:42',
                'notes' => 'Datang bersama rombongan',
                'created_at' => '2025-10-15 08:41:42',
            ]
        ]);

        // Insert data reports
        DB::table('reports')->insert([
            'id' => 1,
            'event_id' => 1,
            'report_name' => 'Laporan_Reses_Sagulung.xlsx',
            'report_type' => 'excel',
            'file_path' => '/uploads/reports/Laporan_Reses_Sagulung.xlsx',
            'generated_by' => 1,
            'generated_at' => '2025-10-15 08:41:43',
        ]);

        // Insert data activity_logs
        DB::table('activity_logs')->insert([
            [
                'id' => 1,
                'user_id' => 1,
                'activity' => 'Admin Utama membuat acara Reses DPRD Kota Batam - Sagulung',
                'created_at' => '2025-10-15 08:41:43',
            ],
            [
                'id' => 2,
                'user_id' => 1,
                'activity' => 'Admin Utama mengunggah laporan hasil reses.',
                'created_at' => '2025-10-15 08:41:43',
            ]
        ]);

        // Insert data system_settings
        DB::table('system_settings')->insert([
            [
                'id' => 1,
                'setting_name' => 'qr_expiration_hours',
                'setting_value' => '24',
                'updated_by' => 1,
                'created_at' => '2025-10-15 08:41:43',
                'updated_at' => '2025-10-15 08:41:43',
            ],
            [
                'id' => 2,
                'setting_name' => 'max_participants',
                'setting_value' => '500',
                'updated_by' => 1,
                'created_at' => '2025-10-15 08:41:43',
                'updated_at' => '2025-10-15 08:41:43',
            ],
            [
                'id' => 3,
                'setting_name' => 'report_format',
                'setting_value' => 'excel',
                'updated_by' => 1,
                'created_at' => '2025-10-15 08:41:43',
                'updated_at' => '2025-10-15 08:41:43',
            ]
        ]);
    }
}