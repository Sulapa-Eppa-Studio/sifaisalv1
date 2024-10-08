<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\PPK;
use App\Models\ServiceProvider;
use App\Models\User;
use App\Models\WorkPackage;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContractSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua Work Packages yang ada
        $workPackages = WorkPackage::pluck('name')->toArray();

        // Ambil semua Service Providers
        $serviceProviders = ServiceProvider::all();

        $ppks = PPK::all();


        // Jika tidak ada data, tampilkan pesan error
        if ($ppks->isEmpty() || $serviceProviders->isEmpty() || empty($workPackages)) {
            $this->command->error('Seeder gagal dijalankan. Pastikan tabel users, service_providers, dan work_packages telah terisi.');
            return;
        }

        // Jumlah kontrak yang akan dibuat
        $numberOfContracts = 20;

        for ($i = 1; $i <= $numberOfContracts; $i++) {
            // Generate nomor kontrak dengan format yang diberikan
            $contractNumber = 'HK. ' . str_pad($i, 2, '0', STR_PAD_LEFT) . '.02/Au8.1/' . str_pad($i, 2, '0', STR_PAD_LEFT);

            // Generate nomor CAN dengan format yang diberikan
            $canNumber = 'A/054.' . rand(10000000, 99999999) . '/0/1';

            // Pilih Work Package secara acak
            $workPackage = $workPackages[array_rand($workPackages)];

            // Pilih Service Provider secara acak
            $serviceProvider = $serviceProviders->random();


            $ppk = $ppks->random();

            // Generate data lainnya
            $executionTime  = rand(30, 180); // Masa pelaksanaan antara 30 - 180 hari
            $advancePayment = rand(0, 1); // 0 atau 1
            $paymentStages  = rand(1, 5); // Tahapan pembayaran antara 1 - 5
            $contractDate   = Carbon::now()->subDays(rand(0, 365)); // Tanggal kontrak dalam 1 tahun terakhir

            $workingUnit = 'SNVT Pelaksanaan Jaringan Pemanfaatan Air Pompengan Jeneberang';

            // Buat kontrak
            Contract::create([
                'contract_number' => $contractNumber,
                'contract_date' => $contractDate,
                'can_number' => $canNumber,
                'work_package' => $workPackage,
                'execution_time' => $executionTime,
                'advance_payment' => $advancePayment,
                'payment_stages' => $paymentStages,
                'payment_value' =>  rand(10000000, 99999999),
                'npwp' => $serviceProvider->npwp,
                'bank_name' => $serviceProvider->bank_name,
                'start_date' => Carbon::now()->subDays(rand(0, 365)),
                'end_date' => Carbon::now()->addDays($executionTime),
                'bank_account_number' => $serviceProvider->account_number,
                'working_unit' => $workingUnit,
                'service_provider_id' => $serviceProvider->id,
                'admin_id' => 1,
                'ppk_id' => $ppk->id,

            ]);
        }
    }
}
