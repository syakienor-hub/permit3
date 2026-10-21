<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Multi-site: 1 staff per site (Madiun & Banyuwangi), 1 manager shared
     * untuk semua site (site = null).
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'email' => 'superadmin@inka.co.id',
                'role' => 'superadmin',
                'site' => null,
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Divisi Teknik',
                'username' => 'divisi_teknik',
                'email' => 'divisi@inka.co.id',
                'role' => 'divisi',
                'site' => null,
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Staff HSE Madiun',
                'username' => 'staff_madiun',
                'email' => 'she.inkamdn2025@gmail.com',
                'role' => 'staff',
                'site' => 'Madiun',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Staff HSE Banyuwangi',
                'username' => 'staff_banyuwangi',
                'email' => 'hse.banyuwangi@inka.co.id',
                'role' => 'staff',
                'site' => 'Banyuwangi',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Dimas Ali Akbar',
                'username' => 'manager_hse',
                'email' => 'arsyadaauni.work@gmail.com',
                'role' => 'manager',
                'site' => null,
                'password' => Hash::make('password'),
            ],
        ];

        // Kompatibilitas DB lama: akun staff tunggal (staff_hse) dimigrasikan
        // menjadi staff_madiun agar email unik tidak bertabrakan.
        $legacy = User::where('username', 'staff_hse')->where('role', 'staff')->first();
        if ($legacy && ! User::where('username', 'staff_madiun')->exists()) {
            $legacy->forceFill([
                'username' => 'staff_madiun',
                'name' => 'Staff HSE Madiun',
                'site' => 'Madiun',
            ])->save();
        }

        foreach ($users as $user) {
            User::updateOrCreate(['username' => $user['username']], $user);
        }
    }
}
