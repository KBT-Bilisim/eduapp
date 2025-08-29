<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'vkn' => '1234567890',
                'mersis_no' => '9876543210',
                'ticaret_sicil_no' => 'TS123456',
                'name' => 'ABC Elektronik A.Ş.',
                'tax_office' => 'Kadıköy Vergi Dairesi',
                'address' => 'İstanbul, Türkiye',
                'building_number' => '10',
                'city' => 'İstanbul',
                'district' => 'Kadıköy',
                'country' => 'Türkiye',
                'contact_first_name' => 'Ali',
                'contact_family_name' => 'Veli',
            ],
            [
                'vkn' => '0987654321',
                'mersis_no' => '1234567890',
                'ticaret_sicil_no' => 'TS654321',
                'name' => 'XYZ Ticaret Ltd. Şti.',
                'tax_office' => 'Beşiktaş Vergi Dairesi',
                'address' => 'İzmir, Türkiye',
                'building_number' => '20',
                'city' => 'İzmir',
                'district' => 'Konak',
                'country' => 'Türkiye',
                'contact_first_name' => 'Ayşe',
                'contact_family_name' => 'Fatma',
            ],
            // Daha fazla tedarikçi ekleyebilirsiniz
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
