<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Kpa;
use App\Models\Ppk;
use App\Models\Spm;
use App\Models\ServiceProvider;
use App\Models\Treasurer;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Define roles and their corresponding models
        $roles = [
            'KPA' => Kpa::class,
            'PPK' => Ppk::class,
            'SPM' => Spm::class,
            'ServiceProvider' => ServiceProvider::class,
            'Treasurer' => Treasurer::class,
        ];

        // roles key
        $role_keys = [
            'kpa',
            'ppk',
            'spm',
            'service_provider',
            'treasurer',
        ];

        $count_role = 0;

        foreach ($roles as $role => $model) {
            // Generate 5 users for each role
            for ($i = 1; $i <= rand(1, 50); $i++) {
                $name = "$role User $i";
                $email = strtolower($role) . $i . '@example.com';

                // Create the user
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
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
                            'nip'       => 'NIP' . $i,
                            'position'  => 'Position ' . $i,
                        ]);
                        break;

                    case 'PPK':
                        $model::create([
                            'user_id'          => $user->id,
                            'full_name'        => $name,
                            'nip'              => 'NIP' . $i,
                            'position'         => 'Position ' . $i,
                            'working_package'  => 'Package ' . $i,
                        ]);
                        break;

                    case 'SPM':
                        $model::create([
                            'user_id'      => $user->id,
                            'full_name'    => $name,
                            'nip'          => 'NIP' . $i,
                            'position'     => 'Position ' . $i,
                            'working_unit' => 'Unit ' . $i,
                        ]);
                        break;

                    case 'ServiceProvider':
                        $model::create([
                            'user_id'            => $user->id,
                            'full_name'          => $name,
                            'registration_number' => 'RegNo' . $i,
                            'npwp'               => 'NPWP' . $i,
                            'address'            => 'Address ' . $i,
                            'account_number'     => 'Account' . $i,
                        ]);
                        break;

                    case 'Treasurer':
                        $model::create([
                            'user_id'      => $user->id,
                            'full_name'    => $name,
                            'nip'          => 'NIP' . $i,
                            'position'     => 'Position ' . $i,
                            'working_unit' => 'Unit ' . $i,
                        ]);
                        break;
                }
            }
            $count_role++;
        }
    }
}
