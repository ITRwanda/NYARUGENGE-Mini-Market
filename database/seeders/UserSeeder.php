<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin ────────────────────────────────────────────────
        User::create([
            'name'     => 'Admin Nyarugenge',
            'email'    => 'admin@nyarugenge.rw',
            'phone'    => '+250788000001',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // ── Inspectors ───────────────────────────────────────────
        User::create([
            'name'     => 'Patrick Nzeyimana',
            'email'    => 'patrick@nyarugenge.rw',
            'phone'    => '+250788000010',
            'password' => Hash::make('password'),
            'role'     => 'inspector',
        ]);

        User::create([
            'name'     => 'Angelique Mukamana',
            'email'    => 'angelique@nyarugenge.rw',
            'phone'    => '+250788000011',
            'password' => Hash::make('password'),
            'role'     => 'inspector',
        ]);

        // ── Vendors ──────────────────────────────────────────────
        $vendors = [
            ['Esperance Nyirabeza',      'esperance@nyarugenge.rw',  '+250788100001', 'Vegetables & Fruits'],
            ['Theophile Ndayishimiye',   'theophile@nyarugenge.rw',  '+250788100002', 'Meat & Poultry'],
            ['Marie Claire Uwase',       'marie@nyarugenge.rw',      '+250788100003', 'Dairy Products'],
            ['Emmanuel Hakizimana',      'emmanuel@nyarugenge.rw',   '+250788100004', 'Fish & Seafood'],
            ['Violette Mukansanga',      'violette@nyarugenge.rw',   '+250788100005', 'Grains & Cereals'],
            ['Joseph Nkurunziza',        'joseph@nyarugenge.rw',     '+250788100006', 'Spices & Condiments'],
            ['Beatrice Ingabire',        'beatrice@nyarugenge.rw',   '+250788100007', 'Bakery & Pastry'],
            ['Alexis Niyomugabo',        'alexis@nyarugenge.rw',     '+250788100008', 'General Goods'],
        ];

        foreach ($vendors as [$name, $email, $phone, $category]) {
            User::create([
                'name'     => $name,
                'email'    => $email,
                'phone'    => $phone,
                'password' => Hash::make('password'),
                'role'     => 'vendor',
            ]);
        }
    }
}
