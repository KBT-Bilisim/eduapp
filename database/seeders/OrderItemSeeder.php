<?php

namespace Database\Seeders;

use App\Models\OrderItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $orderItems = [
            [
                'order_id' => 1, // orders tablosunda 1 numaralı kayıt olmalı
                'line_number' => 1,
                'description' => 'AC/DC ŞARJ HİZMETİ',
                'product_name' => 'AC/DC ŞARJ HİZMETİ',
                'product_model' => null,
                'quantity' => 10,
                'unit_code' => 'KWH',
                'unit_price' => 5.00,  // örnek: 1 KWH = 5 TL
                'line_total' => 50.00, // 10 * 5
                'tax_rate' => 20,
                'tax_amount' => 10.00, // 50 * 0.20
                'tax_type' => 'KDV',
                'tax_code' => '0015',
            ],
            [
                'order_id' => 1,
                'line_number' => 2,
                'description' => 'AC/DC ŞARJ HİZMETİ',
                'product_name' => 'AC/DC ŞARJ HİZMETİ',
                'product_model' => null,
                'quantity' => 20,
                'unit_code' => 'KWH',
                'unit_price' => 5.00,   // aynı birim fiyat
                'line_total' => 100.00, // 20 * 5
                'tax_rate' => 20,
                'tax_amount' => 20.00,  // 100 * 0.20
                'tax_type' => 'KDV',
                'tax_code' => '0015',
            ],
            // Daha fazla sipariş kalemi ekleyebilirsiniz
        ];

        foreach ($orderItems as $item) {
            OrderItem::create($item);
        }




        
    }
}
