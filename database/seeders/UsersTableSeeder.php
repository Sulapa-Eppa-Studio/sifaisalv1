<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\KPA;
use App\Models\PPK;
use App\Models\SPM;
use App\Models\ServiceProvider;
use App\Models\Treasurer;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Define roles and their corresponding models
        $roles = [
            'KPA' => KPA::class,
            'PPK' => PPK::class,
            'SPM' => SPM::class,
            'ServiceProvider' => ServiceProvider::class,
            'Treasurer' => Treasurer::class,
        ];

        // roles key
        $role_keys = [
            'kpa',
            'ppk',
            'spm',
            'penyedia_jasa',
            'bendahara',
        ];

        $count_role = 0;

        foreach ($roles as $role => $model) {
            // Generate 5 users for each role
            for ($i = 1; $i <= rand(1, 50); $i++) {
                $name = "$role User $i";
                $email = strtolower($role) . $i . '@mail.io';

                // Create the user
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'username'  =>  strtolower($role) . $i,
                    'role' => $role_keys[$count_role],
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                    'remember_token' => Str::random(10),
                ]);

                // Create role-specific data
                switch ($role) {
                    case 'KPA':
                        $model::create([
                            'user_id'   => $user->id,
                            'full_name' => $name,
                            'nip'       => 'NIP' . str_pad($i, 8, '0', STR_PAD_LEFT),
                            'position'  => 'Kepala ' . $i,
                        ]);
                        break;

                    case 'PPK':
                        $model::create([
                            'user_id'          => $user->id,
                            'full_name'        => $name,
                            'nip'              => str_pad($i, 8, '0', STR_PAD_LEFT),
                            'position'         => 'Pejabat ' . $i,
                            'working_package'  => 'Paket ' . $i,
                        ]);
                        break;

                    case 'SPM':
                        $model::create([
                            'user_id'      => $user->id,
                            'full_name'    => $name,
                            'nip'          => str_pad($i, 8, '0', STR_PAD_LEFT),
                            'position'     => 'Staff ' . $i,
                            'working_unit' => 'Unit ' . $i,
                        ]);
                        break;

                    case 'ServiceProvider':
                        $model::create([
                            'user_id'             => $user->id,
                            'full_name'           => $name,
                            'registration_number' => str_pad($i, 6, '0', STR_PAD_LEFT),
                            'npwp'                => str_pad($i, 15, '0', STR_PAD_LEFT),
                            'address'             => 'Alamat ' . $i,
                            'account_number'      =>  str_pad($i, 10, '0', STR_PAD_LEFT),
                        ]);
                        break;

                    case 'Treasurer':
                        $model::create([
                            'user_id'      => $user->id,
                            'full_name'    => $name,
                            'nip'          => str_pad($i, 8, '0', STR_PAD_LEFT),
                            'position'     => 'Bendahara ' . $i,
                            'working_unit' => 'Unit ' . $i,
                        ]);
                        break;
                }
            }
            $count_role++;
        }
    }
}
