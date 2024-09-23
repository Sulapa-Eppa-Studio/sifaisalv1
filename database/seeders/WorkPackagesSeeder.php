<?php

namespace Database\Seeders;

use App\Models\WorkPackage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkPackagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $workPackages = [
            'Rehabilitasi D.I. Sanrego, Kab. Bone',
            'Pembangunan Bendungan Karalloe, Kab. Gowa',
            'Peningkatan Jaringan Irigasi D.I. Bili-Bili, Kab. Gowa',
            'Pengendalian Banjir Sungai Jeneberang, Kota Makassar',
            'Pembangunan Embung Tanjung, Kab. Wajo',
            'Rehabilitasi Irigasi D.I. Sadang, Kab. Pinrang',
            'Pembangunan Jaringan Air Baku Mamminasata, Prov. Sulawesi Selatan',
            'Peningkatan Kapasitas Waduk Passeloreng, Kab. Wajo',
            'Pembangunan Bendungan Pamukkulu, Kab. Takalar',
            'Pengembangan Sistem Penyediaan Air Minum, Kota Parepare',
            'Rehabilitasi D.I. Tallo, Kota Makassar',
            'Pembangunan Bendungan Sungai Karama, Kab. Mamuju',
            'Peningkatan Jaringan Irigasi D.I. Batubassi, Kab. Bulukumba',
            'Pengendalian Banjir Sungai Walanae, Kab. Soppeng',
            'Pembangunan Embung Tapong, Kab. Enrekang',
            'Rehabilitasi Irigasi D.I. Benteng, Kab. Selayar',
            'Pembangunan Jaringan Air Baku Malili, Kab. Luwu Timur',
            'Peningkatan Kapasitas Waduk Bili-Bili, Kab. Gowa',
            'Pembangunan Bendungan Ladongi, Kab. Kolaka Timur',
            'Pengembangan Sistem Penyediaan Air Minum, Kota Palopo',
            'Rehabilitasi D.I. Kalaena, Kab. Luwu Timur',
            'Pembangunan Embung Tompobulu, Kab. Maros',
            'Pengendalian Banjir Sungai Saddang, Kab. Pinrang',
            'Peningkatan Jaringan Irigasi D.I. Maloso, Kab. Barru',
            'Pembangunan Bendungan Ameroro, Kab. Konawe',
        ];

        foreach ($workPackages as $name) {
            WorkPackage::create([
                'name' => $name,
            ]);
        }
    }
}
