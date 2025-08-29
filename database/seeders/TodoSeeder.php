<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Todo;
use App\Models\User;

class TodoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        
        if ($users->count() === 0) {
            $this->command->info('No users found. Please run UserSeeder first.');
            return;
        }

        $todos = [
            [
                'title' => 'Laravel Projesi Kurulumu',
                'description' => 'Yeni Laravel projesi kurulumu ve temel konfigürasyonların yapılması',
                'priority' => 'high',
                'status' => 'completed',
                'due_date' => now()->subDays(5),
                'user_id' => $users->random()->id
            ],
            [
                'title' => 'Veritabanı Tasarımı',
                'description' => 'Todo modülü için veritabanı tablolarının tasarlanması ve migration dosyalarının oluşturulması',
                'priority' => 'high',
                'status' => 'completed',
                'due_date' => now()->subDays(3),
                'user_id' => $users->random()->id
            ],
            [
                'title' => 'API Endpoint Geliştirimi',
                'description' => 'Todo CRUD işlemleri için RESTful API endpoint\'lerinin geliştirilmesi',
                'priority' => 'medium',
                'status' => 'in_progress',
                'due_date' => now()->addDays(2),
                'user_id' => $users->random()->id
            ],
            [
                'title' => 'Frontend Arayüz Tasarımı',
                'description' => 'Vuexy teması kullanarak todo modülü için modern ve responsive arayüz tasarımı',
                'priority' => 'medium',
                'status' => 'in_progress',
                'due_date' => now()->addDays(3),
                'user_id' => $users->random()->id
            ],
            [
                'title' => 'Ajax İşlemleri',
                'description' => 'Todo ekleme, güncelleme ve silme işlemleri için Ajax entegrasyonu',
                'priority' => 'high',
                'status' => 'pending',
                'due_date' => now()->addDays(5),
                'user_id' => $users->random()->id
            ],
            [
                'title' => 'Validation Kuralları',
                'description' => 'Form validation kurallarının eklenmesi ve hata mesajlarının gösterilmesi',
                'priority' => 'medium',
                'status' => 'pending',
                'due_date' => now()->addDays(4),
                'user_id' => $users->random()->id
            ],
            [
                'title' => 'Test Yazımı',
                'description' => 'Todo modülü için unit ve feature testlerinin yazılması',
                'priority' => 'low',
                'status' => 'pending',
                'due_date' => now()->addDays(7),
                'user_id' => $users->random()->id
            ],
            [
                'title' => 'Dokümantasyon',
                'description' => 'Proje için detaylı dokümantasyonun hazırlanması',
                'priority' => 'low',
                'status' => 'pending',
                'due_date' => now()->addDays(10),
                'user_id' => $users->random()->id
            ]
        ];

        foreach ($todos as $todo) {
            Todo::create($todo);
        }

        $this->command->info('Todo seeder completed successfully!');
    }
}
