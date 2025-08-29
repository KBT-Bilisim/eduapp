# Todo Modülü Geliştirme Dokümantasyonu

Bu dokümantasyon, Laravel projesi için kapsamlı bir Todo modülünün nasıl geliştirildiğini adım adım açıklamaktadır. Modül, Vuexy teması kullanılarak modern ve responsive bir arayüz ile geliştirilmiştir.

## 📋 İçindekiler

1. [Migration Oluşturma](#migration-oluşturma)
2. [Model Oluşturma](#model-oluşturma)
3. [Controller Oluşturma](#controller-oluşturma)
4. [API Controller Oluşturma](#api-controller-oluşturma)
5. [Routes Tanımlama](#routes-tanımlama)
6. [Seeder Oluşturma](#seeder-oluşturma)
7. [View Oluşturma](#view-oluşturma)
8. [JavaScript ve AJAX İşlemleri](#javascript-ve-ajax-işlemleri)
9. [Kullanım](#kullanım)

---

## 1. Migration Oluşturma

### Adım 1: Migration Dosyası Oluşturma

```bash
php artisan make:model Todo -m
```

Bu komut hem `Todo` modelini hem de migration dosyasını oluşturur.

### Adım 2: Migration Yapısını Tanımlama

Migration dosyası (`database/migrations/xxxx_create_todos_table.php`):

```php
public function up(): void
{
    Schema::create('todos', function (Blueprint $table) {
        $table->id();
        $table->string('title');                                    // Todo başlığı
        $table->text('description')->nullable();                    // Açıklama (opsiyonel)
        $table->enum('priority', ['low', 'medium', 'high'])->default('medium'); // Öncelik seviyesi
        $table->enum('status', ['pending', 'in_progress', 'completed'])->default('pending'); // Durum
        $table->date('due_date')->nullable();                       // Son tarih (opsiyonel)
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Kullanıcı ilişkisi
        $table->timestamps();                                       // created_at, updated_at
    });
}
```

### Adım 3: Migration'ı Çalıştırma

```bash
php artisan migrate
```

---

## 2. Model Oluşturma

### Todo Model (`app/Models/Todo.php`)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Todo extends Model
{
    // Toplu atanabilir alanlar
    protected $fillable = [
        'title',
        'description',
        'priority',
        'status',
        'due_date',
        'user_id'
    ];

    // Veri tipi dönüşümleri
    protected $casts = [
        'due_date' => 'date',
    ];

    /**
     * User ile ilişki (Her todo bir kullanıcıya ait)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Öncelik seviyesi için CSS class
     */
    public function getPriorityBadgeAttribute(): string
    {
        return match($this->priority) {
            'low' => 'bg-label-success',
            'medium' => 'bg-label-warning',
            'high' => 'bg-label-danger',
            default => 'bg-label-secondary'
        };
    }

    /**
     * Durum için CSS class
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-label-secondary',
            'in_progress' => 'bg-label-primary',
            'completed' => 'bg-label-success',
            default => 'bg-label-secondary'
        };
    }

    /**
     * Öncelik seviyesi Türkçe metni
     */
    public function getPriorityTextAttribute(): string
    {
        return match($this->priority) {
            'low' => 'Düşük',
            'medium' => 'Orta',
            'high' => 'Yüksek',
            default => 'Orta'
        };
    }

    /**
     * Durum Türkçe metni
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Bekliyor',
            'in_progress' => 'Devam Ediyor',
            'completed' => 'Tamamlandı',
            default => 'Bekliyor'
        };
    }
}
```

### User Model'e Todo İlişkisi Ekleme

```php
/**
 * User'ın todo'ları (Bir kullanıcının birçok todo'su olabilir)
 */
public function todos(): HasMany
{
    return $this->hasMany(Todo::class);
}
```

---

## 3. Controller Oluşturma

### Adım 1: Controller Oluşturma

```bash
php artisan make:controller TodoController
```

### Adım 2: Controller Metotları (`app/Http/Controllers/TodoController.php`)

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Todo;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    /**
     * Todo listesini görüntüle
     */
    public function index()
    {
        $todos = Todo::with('user')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('todos.index', compact('todos'));
    }

    /**
     * Yeni todo oluştur
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
        ]);

        Todo::create([
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => $request->status,
            'due_date' => $request->due_date,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('todos.index')->with('success', 'Todo başarıyla eklendi.');
    }

    /**
     * Belirli todo'yu görüntüle (AJAX için JSON)
     */
    public function show($id)
    {
        $todo = Todo::with('user')->findOrFail($id);
        return response()->json($todo);
    }

    /**
     * Todo'yu güncelle
     */
    public function update(Request $request, $id)
    {
        $todo = Todo::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
        ]);

        $todo->update([
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => $request->status,
            'due_date' => $request->due_date,
        ]);

        return redirect()->route('todos.index')->with('success', 'Todo başarıyla güncellendi.');
    }

    /**
     * Todo'yu sil
     */
    public function destroy($id)
    {
        $todo = Todo::findOrFail($id);
        $todo->delete();

        return response()->json(['success' => 'Todo başarıyla silindi.']);
    }
}
```

---

## 4. API Controller Oluşturma

### API Klasörü ve Controller (`app/Http/Controllers/Api/TodoApiController.php`)

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Todo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class TodoApiController extends Controller
{
    /**
     * Tüm todo'ları listele
     */
    public function index(): JsonResponse
    {
        $todos = Todo::with('user')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $todos,
            'message' => 'Todos retrieved successfully'
        ]);
    }

    /**
     * Yeni todo oluştur
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
        ]);

        $todo = Todo::create([
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => $request->status,
            'due_date' => $request->due_date,
            'user_id' => Auth::id(),
        ]);

        $todo->load('user');

        return response()->json([
            'success' => true,
            'data' => $todo,
            'message' => 'Todo created successfully'
        ], 201);
    }

    /**
     * Todo durumunu güncelle (Hızlı durum değişimi için)
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $todo = Todo::find($id);
        
        if (!$todo) {
            return response()->json([
                'success' => false,
                'message' => 'Todo not found'
            ], 404);
        }

        $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $todo->update(['status' => $request->status]);
        $todo->load('user');

        return response()->json([
            'success' => true,
            'data' => $todo,
            'message' => 'Todo status updated successfully'
        ]);
    }
}
```

---

## 5. Routes Tanımlama

### Web Routes (`routes/web.php`)

```php
Route::middleware('auth')->group(function () {
    // Todo Routes
    Route::get('/todos', [App\Http\Controllers\TodoController::class, 'index'])->name('todos.index');
    Route::post('/todos', [App\Http\Controllers\TodoController::class, 'store'])->name('todos.store');
    Route::get('/todos/{id}', [App\Http\Controllers\TodoController::class, 'show'])->name('todos.show');
    Route::put('/todos/{id}', [App\Http\Controllers\TodoController::class, 'update'])->name('todos.update');
    Route::delete('/todos/{id}', [App\Http\Controllers\TodoController::class, 'destroy'])->name('todos.destroy');
});
```

### API Routes (`routes/api.php`)

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TodoApiController;

// Todo API Routes
Route::middleware('auth')->group(function () {
    Route::apiResource('todos', TodoApiController::class);
    Route::patch('todos/{id}/status', [TodoApiController::class, 'updateStatus'])->name('todos.update-status');
});
```

---

## 6. Seeder Oluşturma

### Adım 1: Seeder Oluşturma

```bash
php artisan make:seeder TodoSeeder
```

### Adım 2: Seeder İçeriği (`database/seeders/TodoSeeder.php`)

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Todo;
use App\Models\User;

class TodoSeeder extends Seeder
{
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
            // ... diğer örnek veriler
        ];

        foreach ($todos as $todo) {
            Todo::create($todo);
        }
    }
}
```

### Adım 3: Seeder'ı Çalıştırma

```bash
php artisan db:seed --class=TodoSeeder
```

---

## 7. View Oluşturma

### Vuexy Teması Kullanarak Modern Arayüz

Todo listesi sayfası (`resources/views/todos/index.blade.php`) Vuexy temasının özelliklerini kullanır:

- **Responsive DataTable**: Mobil uyumlu tablo
- **Offcanvas Forms**: Yan panelden form açma
- **Bootstrap Components**: Modern form elemanları
- **Badge System**: Durum ve öncelik gösterimi
- **Card Layout**: İstatistik kartları

### Önemli Vuexy Bileşenleri:

```blade
@extends('layouts.master')  <!-- Ana layout -->

@push('styles')
    <!-- Gerekli CSS dosyaları -->
    <link rel="stylesheet" href="/vuexy/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
@endpush

@push('scripts')
    <!-- Gerekli JavaScript dosyaları -->
    <script src="/vuexy/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js"></script>
@endpush
```

---

## 8. JavaScript ve AJAX İşlemleri

### DataTable Initialization

```javascript
var dt_todos = $('#todos-table').DataTable({
    responsive: true,
    order: [[1, 'desc']],
    language: {
        search: 'Ara:',
        lengthMenu: '_MENU_ kayıt göster',
        info: '_TOTAL_ kayıttan _START_ - _END_ arası gösteriliyor',
        // ... diğer Türkçe dil ayarları
    }
});
```

### AJAX Form Submissions

#### Todo Ekleme:
```javascript
$('#addTodoForm').on('submit', function(e) {
    e.preventDefault();
    
    var formData = $(this).serialize();
    
    $.ajax({
        url: '{{ route("todos.store") }}',
        method: 'POST',
        data: formData,
        success: function(response) {
            location.reload();
        },
        error: function(xhr) {
            // Validation errors handling
        }
    });
});
```

#### Durum Değişimi:
```javascript
$(document).on('change', '.status-select', function() {
    var todoId = $(this).data('todo-id');
    var newStatus = $(this).val();
    
    $.ajax({
        url: '/api/todos/' + todoId + '/status',
        method: 'PATCH',
        data: {
            status: newStatus,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            console.log('Status updated successfully');
        }
    });
});
```

#### Todo Silme:
```javascript
$(document).on('click', '.delete-todo-btn', function() {
    var todoId = $(this).data('todo-id');
    
    if (confirm('Bu todo\'yu silmek istediğinizden emin misiniz?')) {
        $.ajax({
            url: '/todos/' + todoId,
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                location.reload();
            }
        });
    }
});
```

---

## 9. Kullanım

### Modülü Kullanmaya Başlama:

1. **Todo sayfasına erişin**: `/todos` URL'ini ziyaret edin
2. **Yeni Todo ekleyin**: "Yeni Todo" butonuna tıklayın
3. **Todo düzenleyin**: Tablodaki düzenle ikonuna tıklayın
4. **Durum değiştirin**: Tablodaki dropdown'dan durum seçin
5. **Todo silin**: Silme ikonuna tıklayın

### API Endpoints:

- `GET /api/todos` - Tüm todo'ları listele
- `POST /api/todos` - Yeni todo oluştur
- `GET /api/todos/{id}` - Belirli todo'yu getir
- `PUT /api/todos/{id}` - Todo'yu güncelle
- `DELETE /api/todos/{id}` - Todo'yu sil
- `PATCH /api/todos/{id}/status` - Todo durumunu güncelle

### Özellikler:

- ✅ CRUD işlemleri (Create, Read, Update, Delete)
- ✅ AJAX tabanlı işlemler
- ✅ Responsive tasarım
- ✅ Vuexy teması entegrasyonu
- ✅ Validation ve hata kontrolü
- ✅ Türkçe dil desteği
- ✅ API endpoints
- ✅ İstatistik kartları
- ✅ Hızlı durum değişimi
- ✅ Modern arayüz

---

## 🎯 Sonuç

Bu Todo modülü, Laravel'in en iyi uygulamalarını takip ederek geliştirilmiş, tam özellikli bir modüldür. Vuexy temasının modern tasarım prensipleriyle birleştirilerek kullanıcı dostu bir deneyim sunar. AJAX tabanlı işlemler sayesinde sayfa yenilenmeden tüm işlemler gerçekleştirilebilir.

Modül, gelecekte genişletilebilir bir yapıya sahiptir ve diğer projelerinizde de kolayca kullanılabilir.
