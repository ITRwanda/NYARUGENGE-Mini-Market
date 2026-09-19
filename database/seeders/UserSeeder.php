<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        User::create([
            'name'     => 'Admin Nyarugenge',
            'email'    => 'admin@market.rw',
            'phone'    => '+250788000001',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // Market Admins
        User::create([
            'name'     => 'Jean-Pierre Habimana',
            'email'    => 'jp.habimana@market.rw',
            'phone'    => '+250788000002',
            'password' => Hash::make('password'),
            'role'     => 'market_admin',
        ]);

        User::create([
            'name'     => 'Claudine Uwimana',
            'email'    => 'c.uwimana@market.rw',
            'phone'    => '+250788000003',
            'password' => Hash::make('password'),
            'role'     => 'market_admin',
        ]);

        // Inspectors
        User::create([
            'name'     => 'Patrick Nzeyimana',
            'email'    => 'p.nzeyimana@market.rw',
            'phone'    => '+250788000010',
            'password' => Hash::make('password'),
            'role'     => 'inspector',
        ]);

        User::create([
            'name'     => 'Angelique Mukamana',
            'email'    => 'a.mukamana@market.rw',
            'phone'    => '+250788000011',
            'password' => Hash::make('password'),
            'role'     => 'inspector',
        ]);

        User::create([
            'name'     => 'Innocent Bizimana',
            'email'    => 'i.bizimana@market.rw',
            'phone'    => '+250788000012',
            'password' => Hash::make('password'),
            'role'     => 'inspector',
        ]);

        // Vendors
        $vendors = [
            ['Esperance Nyirabeza', 'e.nyirabeza@market.rw', '+250788100001'],
            ['Theophile Ndayishimiye', 't.ndayishimiye@market.rw', '+250788100002'],
            ['Marie Claire Uwase', 'm.uwase@market.rw', '+250788100003'],
            ['Emmanuel Hakizimana', 'e.hakizimana@market.rw', '+250788100004'],
            ['Violette Mukansanga', 'v.mukansanga@market.rw', '+250788100005'],
            ['Joseph Nkurunziza', 'j.nkurunziza@market.rw', '+250788100006'],
            ['Beatrice Ingabire', 'b.ingabire@market.rw', '+250788100007'],
            ['Alexis Niyomugabo', 'a.niyomugabo@market.rw', '+250788100008'],
            ['Chantal Mukamurenzi', 'c.mukamurenzi@market.rw', '+250788100009'],
            ['Leon Uwimana', 'l.uwimana@market.rw', '+250788100010'],
            ['Solange Nyiramana', 's.nyiramana@market.rw', '+250788100011'],
            ['Claude Bizumuremyi', 'c.bizumuremyi@market.rw', '+250788100012'],
        ];

        foreach ($vendors as [$name, $email, $phone]) {
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
