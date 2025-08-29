<?php

namespace Database\Seeders;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Str;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $customers = [
            [
                'tckn_vkn' => '12345678901',
                'name' => 'Ahmet',
                'family' => 'Yılmaz',
                'tax_office' => null,
                'address' => 'Kadıköy Mah. İstanbul',
                'city' => 'İstanbul',
                'district' => 'Kadıköy',
                'plate' => '34ABC123'
            ],
            
            [
                'tckn_vkn' => '1122334455',
                'name' => 'Delta Elektrik Sanayi',
                'family' => null,
                'tax_office' => 'İzmir Kurumlar VD',
                'address' => 'Bornova Mah. İzmir',
                'city' => 'İzmir',
                'district' => 'Bornova',
                'plate' => '35FIR003'
            ],
            [
                'tckn_vkn' => '5566778899',
                'name' => 'Mavi Lojistik A.Ş.',
                'family' => null,
                'tax_office' => 'Bursa VD',
                'address' => 'Nilüfer Mah. Bursa',
                'city' => 'Bursa',
                'district' => 'Nilüfer',
                'plate' => '16FIR004'
            ],
            [
                'tckn_vkn' => '6677889900',
                'name' => 'Tekno Yazılım Ltd.',
                'family' => null,
                'tax_office' => 'Gebze VD',
                'address' => 'Gebze OSB Kocaeli',
                'city' => 'Kocaeli',
                'district' => 'Gebze',
                'plate' => '41FIR005'
            ],
            [
                'tckn_vkn' => '98765432109',
                'name' => 'Mehmet',
                'family' => 'Kaya',
                'tax_office' => null,
                'address' => 'Çankaya Mah. Ankara',
                'city' => 'Ankara',
                'district' => 'Çankaya',
                'plate' => '06XYZ456'
            ],
            [
                'tckn_vkn' => '1234567890',
                'name' => 'ABC Otomotiv A.Ş.',
                'family' => null,
                'tax_office' => 'Maslak VD',
                'address' => 'Maslak Mah. İstanbul',
                'city' => 'İstanbul',
                'district' => 'Sarıyer',
                'plate' => '34FIR001'
            ],
            [
                'tckn_vkn' => '9876543210',
                'name' => 'XYZ Enerji Ltd. Şti.',
                'family' => null,
                'tax_office' => 'Ankara Kurumlar VD',
                'address' => 'Kızılay Mah. Ankara',
                'city' => 'Ankara',
                'district' => 'Kızılay',
                'plate' => '06FIR002'
            ],
          
        ];


        $customersStatus2 = [ 
            
            // Şarj Kart Kullanıcıları
            
            [
                'tckn_vkn' => '55667788990',
                'name' => 'Elif',
                'family' => 'Şahin',
                'tax_office' => null,
                'address' => 'Atakum Mah. Samsun',
                'city' => 'Samsun',
                'district' => 'Atakum',
                'plate' => '55ELF678'
            ],
            [
                'tckn_vkn' => '66778899001',
                'name' => 'Burak',
                'family' => 'Öztürk',
                'tax_office' => null,
                'address' => 'Tepebaşı Mah. Eskişehir',
                'city' => 'Eskişehir',
                'district' => 'Tepebaşı',
                'plate' => '26BRK123'
            ],
              [
                'tckn_vkn' => '11223344556',
                'name' => 'Zeynep',
                'family' => 'Demir',
                'tax_office' => null,
                'address' => 'Alsancak Mah. İzmir',
                'city' => 'İzmir',
                'district' => 'Konak',
                'plate' => '35ZZZ789'
            ],

            
         

        ];        

        for ($i = 1; $i <= 85; $i++) {

            $customer = $customers[array_rand($customers)];            

            $date = Carbon::now()
                ->subMonths(rand(0, 11))
                ->subDays(rand(0, 28));

            $quantity = rand(5, 50); // kwh
            $unitPrice = rand(4, 8); // TL/kwh
            $lineTotal = $quantity * $unitPrice;
            $taxRate = 20;
            $taxAmount = $lineTotal * ($taxRate / 100);

            // İskonto ihtimali %20
            $allowance = rand(1, 100) <= 20 ? rand(5, 20) : 0;

            $orderId = DB::table("orders")->insertGetId([
                'supplier_id' => 1,
                'local_document_id' => 'DOC-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'invoice_number' => 'SRJ' . str_pad($i, 12, '0', STR_PAD_LEFT),
                'invoice_type' => 'SATIS',
                'profile_id' => 'TICARIFATURA',
                'issue_date' => $date->toDateString(),
                'issue_time' => $date->toTimeString(),
                'currency' => 'TRY',
                'order_number' => 'ORD-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'order_date' => $date->toDateString(),

                'customer_tckn_vkn' => $customer['tckn_vkn'],
                'customer_name' => $customer['name'],
                'customer_family_name' => $customer['family'],
                'customer_tax_office' => $customer['tax_office'],
                'customer_address' => $customer['address'],
                'customer_building_number' => rand(1, 50),
                'customer_city' => $customer['city'],
                'customer_district' => $customer['district'],
                'customer_country' => 'Türkiye',
                'customer_tel' => '05' . rand(100000000, 999999999),
                'customer_email' => Str::slug($customer['name']) . '@example.com',
                'customer_plate' => $customer['plate'],

                'line_extension_amount' => $lineTotal,
                'tax_exclusive_amount' => $lineTotal - $allowance,
                'tax_inclusive_amount' => ($lineTotal - $allowance) + $taxAmount,
                'tax_rate' => $taxRate,
                'tax_total' => $taxAmount,
                'allowance_total_amount' => $allowance,
                'payable_amount' => ($lineTotal - $allowance) + $taxAmount,

                'delivery_type' => 'Electronic',
                'scenario' => 'Automated',
                'notification_email' => 'musteri@example.com',
                'notification_subject' => 'E-Fatura Bildirimi',
                'notification_enabled' => true,
                'status' => 1,
                'invoice_uuid' => \Illuminate\Support\Str::uuid(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('order_items')->insert([
                'order_id' => $orderId,
                'line_number' => 1,
                'description' => 'AC/DC ŞARJ HİZMETİ',
                'product_name' => 'AC/DC ŞARJ HİZMETİ',
                'product_model' => null,
                'quantity' => $quantity,
                'unit_code' => 'KWH',
                'unit_price' => $unitPrice,
                'line_total' => $lineTotal,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'tax_type' => 'KDV',
                'tax_code' => '0015',
            ]);
        }


        for ($i = 1; $i <= 42; $i++) {

            $customer = $customers[array_rand($customers)];            

            $date = Carbon::now()
                ->subMonths(rand(0, 11))
                ->subDays(rand(0, 28));

            $quantity = rand(5, 50); // kwh
            $unitPrice = rand(4, 8); // TL/kwh
            $lineTotal = $quantity * $unitPrice;
            $taxRate = 20;
            $taxAmount = $lineTotal * ($taxRate / 100);

            // İskonto ihtimali %20
            $allowance = rand(1, 100) <= 20 ? rand(5, 20) : 0;

            $orderId = DB::table("orders")->insertGetId([
                'supplier_id' => 1,
                'local_document_id' => 'DOC-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'invoice_number' => null,
                'invoice_type' => 'SATIS',
                'profile_id' => 'TICARIFATURA',
                'issue_date' => null,
                'issue_time' =>null,
                'currency' => 'TRY',
                'order_number' => 'ORD-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'order_date' => $date->toDateString(),

                'customer_tckn_vkn' => $customer['tckn_vkn'],
                'customer_name' => $customer['name'],
                'customer_family_name' => $customer['family'],
                'customer_tax_office' => $customer['tax_office'],
                'customer_address' => $customer['address'],
                'customer_building_number' => rand(1, 50),
                'customer_city' => $customer['city'],
                'customer_district' => $customer['district'],
                'customer_country' => 'Türkiye',
                'customer_tel' => '05' . rand(100000000, 999999999),
                'customer_email' => Str::slug($customer['name']) . '@example.com',
                'customer_plate' => $customer['plate'],

                'line_extension_amount' => $lineTotal,
                'tax_exclusive_amount' => $lineTotal - $allowance,
                'tax_inclusive_amount' => ($lineTotal - $allowance) + $taxAmount,
                'tax_rate' => $taxRate,
                'tax_total' => $taxAmount,
                'allowance_total_amount' => $allowance,
                'payable_amount' => ($lineTotal - $allowance) + $taxAmount,

                'delivery_type' => 'Electronic',
                'scenario' => 'Automated',
                'notification_email' => 'musteri@example.com',
                'notification_subject' => 'E-Fatura Bildirimi',
                'notification_enabled' => true,
                'status' => 0,
                'invoice_uuid' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('order_items')->insert([
                'order_id' => $orderId,
                'line_number' => 1,
                'description' => 'AC/DC ŞARJ HİZMETİ',
                'product_name' => 'AC/DC ŞARJ HİZMETİ',
                'product_model' => null,
                'quantity' => $quantity,
                'unit_code' => 'KWH',
                'unit_price' => $unitPrice,
                'line_total' => $lineTotal,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'tax_type' => 'KDV',
                'tax_code' => '0015',
            ]);
        }

        for ($i = 1; $i <= 42; $i++) {

            $customer = $customersStatus2[array_rand($customersStatus2)];            

            $date = Carbon::now()
                ->subMonths(rand(0, 11))
                ->subDays(rand(0, 28));

            $quantity = rand(5, 50); // kwh
            $unitPrice = rand(4, 8); // TL/kwh
            $lineTotal = $quantity * $unitPrice;
            $taxRate = 20;
            $taxAmount = $lineTotal * ($taxRate / 100);

            // İskonto ihtimali %20
            $allowance = rand(1, 100) <= 20 ? rand(5, 20) : 0;

            $orderId = DB::table("orders")->insertGetId([
                'supplier_id' => 1,
                'local_document_id' => 'DOC-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'invoice_number' => null,
                'invoice_type' => 'SATIS',
                'profile_id' => 'TICARIFATURA',
                'issue_date' => null,
                'issue_time' =>null,
                'currency' => 'TRY',
                'order_number' => 'ORD-' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'order_date' => $date->toDateString(),

                'customer_tckn_vkn' => $customer['tckn_vkn'],
                'customer_name' => $customer['name'],
                'customer_family_name' => $customer['family'],
                'customer_tax_office' => $customer['tax_office'],
                'customer_address' => $customer['address'],
                'customer_building_number' => rand(1, 50),
                'customer_city' => $customer['city'],
                'customer_district' => $customer['district'],
                'customer_country' => 'Türkiye',
                'customer_tel' => '05' . rand(100000000, 999999999),
                'customer_email' => Str::slug($customer['name']) . '@example.com',
                'customer_plate' => $customer['plate'],

                'line_extension_amount' => $lineTotal,
                'tax_exclusive_amount' => $lineTotal - $allowance,
                'tax_inclusive_amount' => ($lineTotal - $allowance) + $taxAmount,
                'tax_rate' => $taxRate,
                'tax_total' => $taxAmount,
                'allowance_total_amount' => $allowance,
                'payable_amount' => ($lineTotal - $allowance) + $taxAmount,

                'delivery_type' => 'Electronic',
                'scenario' => 'Automated',
                'notification_email' => 'musteri@example.com',
                'notification_subject' => 'E-Fatura Bildirimi',
                'notification_enabled' => true,
                'status' => 2,
                'invoice_uuid' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('order_items')->insert([
                'order_id' => $orderId,
                'line_number' => 1,
                'description' => 'AC/DC ŞARJ HİZMETİ',
                'product_name' => 'AC/DC ŞARJ HİZMETİ',
                'product_model' => null,
                'quantity' => $quantity,
                'unit_code' => 'KWH',
                'unit_price' => $unitPrice,
                'line_total' => $lineTotal,
                'tax_rate' => $taxRate,
                'tax_amount' => $taxAmount,
                'tax_type' => 'KDV',
                'tax_code' => '0015',
            ]);
        }
    


    }
}
