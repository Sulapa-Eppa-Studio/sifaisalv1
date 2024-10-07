<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\SPMRequest;
use App\Models\TermintSppPpk;
use App\Models\TermintSppPpkFile;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->seedData();
    }


    public function seedData()
    {
        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@mail.io',
            'role' => 'admin',
            'username' => 'admin',
        ]);


        // if (config('app.debug')) {
        $this->call(UsersTableSeeder::class);
        $this->call(WorkPackagesSeeder::class);

        //     $this->call(ContractSeeder::class);
        //     $this->call(PaymentRequestSeeder::class);

        //     Document::factory()->count(250)->create();

        //     TermintSppPpk::factory()->count(10)->create();

        //     TermintSppPpkFile::factory()->count(150)->create();

        //     SPMRequest::factory()->count(50)->create();
        // }
    }
}
