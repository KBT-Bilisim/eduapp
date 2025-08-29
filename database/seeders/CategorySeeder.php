<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $rows = [
            ['name' => 'Market', 'icon' => 'fa-basket-shopping', 'color' => '#22c55e'],
            ['name' => 'Manav', 'icon' => 'fa-apple-whole', 'color' => '#16a34a'],
            ['name' => 'Teknoloji', 'icon' => 'fa-microchip', 'color' => '#3b82f6'],
            ['name' => 'Eczane', 'icon' => 'fa-briefcase-medical', 'color' => '#ef4444'],
            ['name' => 'Kırtasiye', 'icon' => 'fa-pen-ruler', 'color' => '#a855f7'],
            ['name' => 'Temizlik', 'icon' => 'fa-soap', 'color' => '#06b6d4'],
        ];

        foreach ($rows as &$r) {
            $r['created_at'] = $now;
            $r['updated_at'] = $now;
        }

        Category::insert($rows);
    }
}
