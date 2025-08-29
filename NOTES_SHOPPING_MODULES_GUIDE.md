# Laravel Projesi - Notlar ve Alışveriş Listesi Modülleri Geliştirme Rehberi

Bu dokümantasyon, Laravel projesi için **Notlar (Notes)** ve **Alışveriş Listesi (Shopping List)** modüllerinin nasıl geliştirildiğini adım adım açıklamaktadır.

## 📋 İçindekiler

1. [Proje Hazırlığı ve Temel Bilgiler](#proje-hazırlığı-ve-temel-bilgiler)
2. [Notlar (Notes) Modülü](#notlar-notes-modülü)
3. [Alışveriş Listesi (Shopping List) Modülü](#alışveriş-listesi-shopping-list-modülü)
4. [Bonus: AJAX Kullanımı](#bonus-ajax-kullanımı)
5. [Test ve Debug](#test-ve-debug)

---

## 📚 Proje Hazırlığı ve Temel Bilgiler

### Laravel Artisan Komutları (Temel)

```bash
# Model ve Migration oluşturma
php artisan make:model ModelAdı -m

# Controller oluşturma
php artisan make:controller ControllerAdı

# Migration çalıştırma
php artisan migrate

# Seeder oluşturma
php artisan make:seeder SeederAdı

# Seeder çalıştırma
php artisan db:seed --class=SeederAdı
```

### Temel Laravel Yapısı

- **Models**: `app/Models/` - Veritabanı tabloları ile etkileşim
- **Controllers**: `app/Http/Controllers/` - İş mantığı ve HTTP istekleri
- **Migrations**: `database/migrations/` - Veritabanı yapısı
- **Views**: `resources/views/` - Arayüz dosyaları
- **Routes**: `routes/web.php` - URL yönlendirmeleri

---

## 📝 Notlar (Notes) Modülü

### 🎯 Modül Özellikleri

- ✅ Not başlığı ve içerik ekleme/düzenleme
- ✅ Etiket (tag) sistemi
- ✅ Arama ve filtreleme
- ✅ Tarih filtresi
- ✅ CRUD işlemleri (Create, Read, Update, Delete)
- 🎁 **Bonus**: AJAX ile sayfa yenilenmeden işlemler

### 1️⃣ Adım 1: Model ve Migration Oluşturma

#### Notes Modeli ve Migration'ı oluşturun:

```bash
php artisan make:model Note -m
```

#### Tags Modeli ve Migration'ı oluşturun:

```bash
php artisan make:model Tag -m
```

#### Note-Tag ilişki tablosu için migration:

```bash
php artisan make:migration create_note_tag_table
```

### 2️⃣ Adım 2: Migration Dosyalarını Düzenleme

#### Notes Migration (`database/migrations/xxxx_create_notes_table.php`):

```php
public function up(): void
{
    Schema::create('notes', function (Blueprint $table) {
        $table->id();
        $table->string('title');                                    // Not başlığı
        $table->longText('content');                               // Not içeriği (uzun metin)
        $table->boolean('is_favorite')->default(false);           // Favori işaretleme
        $table->enum('priority', ['low', 'medium', 'high'])->default('medium'); // Öncelik
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Kullanıcı ilişkisi
        $table->timestamps();                                      // created_at, updated_at
    });
}
```

#### Tags Migration (`database/migrations/xxxx_create_tags_table.php`):

```php
public function up(): void
{
    Schema::create('tags', function (Blueprint $table) {
        $table->id();
        $table->string('name')->unique();                         // Etiket adı (benzersiz)
        $table->string('color')->default('#007bff');              // Etiket rengi
        $table->timestamps();
    });
}
```

#### Note-Tag İlişki Migration (`database/migrations/xxxx_create_note_tag_table.php`):

```php
public function up(): void
{
    Schema::create('note_tag', function (Blueprint $table) {
        $table->id();
        $table->foreignId('note_id')->constrained()->onDelete('cascade');
        $table->foreignId('tag_id')->constrained()->onDelete('cascade');
        $table->timestamps();
        
        // Aynı not-etiket çifti tekrar oluşmasın
        $table->unique(['note_id', 'tag_id']);
    });
}
```

### 3️⃣ Adım 3: Model İlişkilerini Tanımlama

#### Note Model (`app/Models/Note.php`):

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Note extends Model
{
    protected $fillable = [
        'title',
        'content',
        'is_favorite',
        'priority',
        'user_id'
    ];

    protected $casts = [
        'is_favorite' => 'boolean',
    ];

    // Kullanıcı ilişkisi
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Etiketler ile çoktan çoğa ilişki
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    // Öncelik badge'i için yardımcı metot
    public function getPriorityBadgeAttribute(): string
    {
        return match($this->priority) {
            'low' => 'bg-label-success',
            'medium' => 'bg-label-warning',
            'high' => 'bg-label-danger',
            default => 'bg-label-secondary'
        };
    }
}
```

#### Tag Model (`app/Models/Tag.php`):

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = [
        'name',
        'color'
    ];

    // Notlar ile çoktan çoğa ilişki
    public function notes(): BelongsToMany
    {
        return $this->belongsToMany(Note::class);
    }
}
```

### 4️⃣ Adım 4: Controller Oluşturma

```bash
php artisan make:controller NoteController
```

#### Note Controller (`app/Http/Controllers/NoteController.php`):

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $query = Note::with(['user', 'tags'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc');

        // Arama filtresi
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Tarih filtresi
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Etiket filtresi
        if ($request->filled('tag')) {
            $query->whereHas('tags', function($q) use ($request) {
                $q->where('tags.id', $request->tag);
            });
        }

        $notes = $query->paginate(10);
        $tags = Tag::all();
        
        return view('notes.index', compact('notes', 'tags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id'
        ]);

        $note = Note::create([
            'title' => $request->title,
            'content' => $request->content,
            'priority' => $request->priority,
            'user_id' => Auth::id(),
        ]);

        // Etiketleri ekle
        if ($request->has('tags')) {
            $note->tags()->attach($request->tags);
        }

        return redirect()->route('notes.index')->with('success', 'Not başarıyla eklendi.');
    }

    public function show($id)
    {
        $note = Note::with(['user', 'tags'])->findOrFail($id);
        return response()->json($note);
    }

    public function update(Request $request, $id)
    {
        $note = Note::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'priority' => 'required|in:low,medium,high',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id'
        ]);

        $note->update([
            'title' => $request->title,
            'content' => $request->content,
            'priority' => $request->priority,
        ]);

        // Etiketleri güncelle
        if ($request->has('tags')) {
            $note->tags()->sync($request->tags);
        }

        return redirect()->route('notes.index')->with('success', 'Not başarıyla güncellendi.');
    }

    public function destroy($id)
    {
        $note = Note::findOrFail($id);
        $note->delete();

        return response()->json(['success' => 'Not başarıyla silindi.']);
    }
}
```

### 5️⃣ Adım 5: Routes Tanımlama

#### Web Routes (`routes/web.php`):

```php
Route::middleware('auth')->group(function () {
    // Notes Routes
    Route::get('/notes', [App\Http\Controllers\NoteController::class, 'index'])->name('notes.index');
    Route::post('/notes', [App\Http\Controllers\NoteController::class, 'store'])->name('notes.store');
    Route::get('/notes/{id}', [App\Http\Controllers\NoteController::class, 'show'])->name('notes.show');
    Route::put('/notes/{id}', [App\Http\Controllers\NoteController::class, 'update'])->name('notes.update');
    Route::delete('/notes/{id}', [App\Http\Controllers\NoteController::class, 'destroy'])->name('notes.destroy');
});
```

### 6️⃣ Adım 6: View Oluşturma

#### Ana Sayfa (`resources/views/notes/index.blade.php`):

```blade
@extends('layouts.master')

@section('title', 'Notlarım - KBTS ARJ Sistem')

@section('content')
<div class="container-fluid">
    <!-- Başlık ve Yeni Not Butonu -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Notlarım</h4>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addNoteModal">
                    <i class="ti ti-plus"></i> Yeni Not
                </button>
            </div>
        </div>
    </div>

    <!-- Filtre Kartı -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('notes.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Arama</label>
                        <input type="text" name="search" class="form-control" 
                               value="{{ request('search') }}" placeholder="Başlık veya içerik ara...">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Başlangıç Tarihi</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Bitiş Tarihi</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Etiket</label>
                        <select name="tag" class="form-select">
                            <option value="">Tüm etiketler</option>
                            @foreach($tags as $tag)
                                <option value="{{ $tag->id }}" {{ request('tag') == $tag->id ? 'selected' : '' }}>
                                    {{ $tag->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary">Filtrele</button>
                            <a href="{{ route('notes.index') }}" class="btn btn-outline-secondary">Temizle</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Notlar Listesi -->
    <div class="row">
        @foreach($notes as $note)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">{{ Str::limit($note->title, 30) }}</h6>
                    <span class="badge {{ $note->priority_badge }}">
                        {{ ucfirst($note->priority) }}
                    </span>
                </div>
                <div class="card-body">
                    <p class="card-text">{{ Str::limit(strip_tags($note->content), 100) }}</p>
                    
                    <!-- Etiketler -->
                    @if($note->tags->count() > 0)
                    <div class="mb-3">
                        @foreach($note->tags as $tag)
                        <span class="badge me-1" style="background-color: {{ $tag->color }}">
                            {{ $tag->name }}
                        </span>
                        @endforeach
                    </div>
                    @endif
                    
                    <small class="text-muted">{{ $note->created_at->format('d.m.Y H:i') }}</small>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <button class="btn btn-sm btn-outline-primary edit-note-btn" 
                            data-note-id="{{ $note->id }}"
                            data-bs-toggle="modal" 
                            data-bs-target="#editNoteModal">
                        <i class="ti ti-edit"></i> Düzenle
                    </button>
                    <button class="btn btn-sm btn-outline-danger delete-note-btn" 
                            data-note-id="{{ $note->id }}">
                        <i class="ti ti-trash"></i> Sil
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    {{ $notes->appends(request()->query())->links() }}
</div>

<!-- Add Note Modal -->
<div class="modal fade" id="addNoteModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('notes.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Yeni Not Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Başlık</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">İçerik</label>
                        <textarea name="content" class="form-control" rows="6" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label class="form-label">Öncelik</label>
                            <select name="priority" class="form-select">
                                <option value="low">Düşük</option>
                                <option value="medium" selected>Orta</option>
                                <option value="high">Yüksek</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Etiketler</label>
                            <select name="tags[]" class="form-select" multiple>
                                @foreach($tags as $tag)
                                <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" class="btn btn-primary">Kaydet</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Edit note button click
    $('.edit-note-btn').on('click', function() {
        var noteId = $(this).data('note-id');
        
        $.ajax({
            url: '/notes/' + noteId,
            method: 'GET',
            success: function(note) {
                // Modal'ı doldur ve göster
                $('#edit-note-id').val(note.id);
                $('#edit-note-title').val(note.title);
                $('#edit-note-content').val(note.content);
                $('#edit-note-priority').val(note.priority);
            }
        });
    });

    // Delete note
    $('.delete-note-btn').on('click', function() {
        var noteId = $(this).data('note-id');
        
        if (confirm('Bu notu silmek istediğinizden emin misiniz?')) {
            $.ajax({
                url: '/notes/' + noteId,
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
});
</script>
@endpush
```

---

## 🛒 Alışveriş Listesi (Shopping List) Modülü

### 🎯 Modül Özellikleri

- ✅ Ürün ekleme/düzenleme (isim, miktar, kategori)
- ✅ "Alındı/Alınmadı" durumu işaretleme
- ✅ Kategori sistemi (Market, Manav, Teknoloji vb.)
- ✅ Liste filtreleme ve arama
- ✅ CRUD işlemleri
- 🎁 **Bonus**: AJAX ile dinamik işlemler

### 1️⃣ Adım 1: Model ve Migration Oluşturma

#### Shopping Lists için modeller:

```bash
php artisan make:model ShoppingList -m
php artisan make:model ShoppingItem -m
php artisan make:model Category -m
```

### 2️⃣ Adım 2: Migration Dosyalarını Düzenleme

#### Categories Migration (`database/migrations/xxxx_create_categories_table.php`):

```php
public function up(): void
{
    Schema::create('categories', function (Blueprint $table) {
        $table->id();
        $table->string('name');                                    // Kategori adı
        $table->string('icon')->nullable();                       // Font awesome ikonu
        $table->string('color')->default('#007bff');              // Kategori rengi
        $table->timestamps();
    });
}
```

#### Shopping Lists Migration (`database/migrations/xxxx_create_shopping_lists_table.php`):

```php
public function up(): void
{
    Schema::create('shopping_lists', function (Blueprint $table) {
        $table->id();
        $table->string('name');                                    // Liste adı
        $table->text('description')->nullable();                  // Liste açıklaması
        $table->boolean('is_completed')->default(false);          // Liste tamamlandı mı?
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->timestamps();
    });
}
```

#### Shopping Items Migration (`database/migrations/xxxx_create_shopping_items_table.php`):

```php
public function up(): void
{
    Schema::create('shopping_items', function (Blueprint $table) {
        $table->id();
        $table->string('name');                                    // Ürün adı
        $table->integer('quantity')->default(1);                  // Miktar
        $table->string('unit')->default('adet');                  // Birim (adet, kg, litre vb.)
        $table->decimal('estimated_price', 8, 2)->nullable();     // Tahmini fiyat
        $table->boolean('is_purchased')->default(false);          // Alındı mı?
        $table->text('notes')->nullable();                        // Notlar
        $table->foreignId('shopping_list_id')->constrained()->onDelete('cascade');
        $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
        $table->timestamps();
    });
}
```

### 3️⃣ Adım 3: Model İlişkilerini Tanımlama

#### ShoppingList Model (`app/Models/ShoppingList.php`):

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShoppingList extends Model
{
    protected $fillable = [
        'name',
        'description',
        'is_completed',
        'user_id'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ShoppingItem::class);
    }

    // Tamamlanma yüzdesi
    public function getCompletionPercentageAttribute(): int
    {
        $total = $this->items()->count();
        if ($total === 0) return 0;
        
        $purchased = $this->items()->where('is_purchased', true)->count();
        return (int) round(($purchased / $total) * 100);
    }

    // Toplam tahmini tutar
    public function getTotalEstimatedPriceAttribute(): float
    {
        return $this->items()->sum('estimated_price') ?? 0;
    }
}
```

#### ShoppingItem Model (`app/Models/ShoppingItem.php`):

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShoppingItem extends Model
{
    protected $fillable = [
        'name',
        'quantity',
        'unit',
        'estimated_price',
        'is_purchased',
        'notes',
        'shopping_list_id',
        'category_id'
    ];

    protected $casts = [
        'is_purchased' => 'boolean',
        'estimated_price' => 'decimal:2',
    ];

    public function shoppingList(): BelongsTo
    {
        return $this->belongsTo(ShoppingList::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
```

#### Category Model (`app/Models/Category.php`):

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'color'
    ];

    public function shoppingItems(): HasMany
    {
        return $this->hasMany(ShoppingItem::class);
    }
}
```

### 4️⃣ Adım 4: Controller Oluşturma

```bash
php artisan make:controller ShoppingListController
php artisan make:controller ShoppingItemController
```

#### ShoppingList Controller (`app/Http/Controllers/ShoppingListController.php`):

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ShoppingList;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class ShoppingListController extends Controller
{
    public function index()
    {
        $lists = ShoppingList::with(['items', 'user'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('shopping.index', compact('lists'));
    }

    public function show($id)
    {
        $list = ShoppingList::with(['items.category', 'user'])->findOrFail($id);
        $categories = Category::all();
        
        return view('shopping.show', compact('list', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        ShoppingList::create([
            'name' => $request->name,
            'description' => $request->description,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('shopping.index')->with('success', 'Alışveriş listesi oluşturuldu.');
    }

    public function update(Request $request, $id)
    {
        $list = ShoppingList::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $list->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return redirect()->route('shopping.show', $list->id)->with('success', 'Liste güncellendi.');
    }

    public function destroy($id)
    {
        $list = ShoppingList::findOrFail($id);
        $list->delete();

        return redirect()->route('shopping.index')->with('success', 'Liste silindi.');
    }
}
```

### 5️⃣ Adım 5: Seeder Oluşturma

#### Category Seeder oluşturun:

```bash
php artisan make:seeder CategorySeeder
```

#### CategorySeeder (`database/seeders/CategorySeeder.php`):

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Market', 'icon' => 'ti-shopping-cart', 'color' => '#28a745'],
            ['name' => 'Manav', 'icon' => 'ti-apple', 'color' => '#fd7e14'],
            ['name' => 'Teknoloji', 'icon' => 'ti-device-laptop', 'color' => '#6f42c1'],
            ['name' => 'Eczane', 'icon' => 'ti-pill', 'color' => '#dc3545'],
            ['name' => 'Kırtasiye', 'icon' => 'ti-pencil', 'color' => '#ffc107'],
            ['name' => 'Temizlik', 'icon' => 'ti-spray', 'color' => '#20c997'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
```

### 6️⃣ Adım 6: Routes Tanımlama

#### Web Routes (`routes/web.php`):

```php
Route::middleware('auth')->group(function () {
    // Shopping Lists Routes
    Route::get('/shopping', [App\Http\Controllers\ShoppingListController::class, 'index'])->name('shopping.index');
    Route::post('/shopping', [App\Http\Controllers\ShoppingListController::class, 'store'])->name('shopping.store');
    Route::get('/shopping/{id}', [App\Http\Controllers\ShoppingListController::class, 'show'])->name('shopping.show');
    Route::put('/shopping/{id}', [App\Http\Controllers\ShoppingListController::class, 'update'])->name('shopping.update');
    Route::delete('/shopping/{id}', [App\Http\Controllers\ShoppingListController::class, 'destroy'])->name('shopping.destroy');
    
    // Shopping Items Routes  
    Route::post('/shopping/{list}/items', [App\Http\Controllers\ShoppingItemController::class, 'store'])->name('shopping.items.store');
    Route::patch('/shopping/items/{item}/toggle', [App\Http\Controllers\ShoppingItemController::class, 'togglePurchased'])->name('shopping.items.toggle');
    Route::delete('/shopping/items/{item}', [App\Http\Controllers\ShoppingItemController::class, 'destroy'])->name('shopping.items.destroy');
});
```

---

## 🎁 Bonus: AJAX Kullanımı

### AJAX Kullanımının Avantajları:

- ✅ **Sayfa yenilenmez** - Daha hızlı kullanıcı deneyimi
- ✅ **Dinamik içerik** - Anında güncelleme
- ✅ **Daha az veri transferi** - Sadece gerekli veriler alınır
- ✅ **Modern kullanıcı deneyimi** - Responsive ve akıcı

### AJAX Örneği - Ürün Durumu Değiştirme:

```javascript
// Ürün satın alındı/alınmadı durumunu değiştir
$(document).on('change', '.item-checkbox', function() {
    var itemId = $(this).data('item-id');
    var isChecked = $(this).is(':checked');
    
    $.ajax({
        url: '/shopping/items/' + itemId + '/toggle',
        method: 'PATCH',
        data: {
            _token: '{{ csrf_token() }}',
            is_purchased: isChecked
        },
        success: function(response) {
            // İlerleme çubuğunu güncelle
            updateProgressBar();
            
            // Başarı mesajı göster (isteğe bağlı)
            showToast('Ürün durumu güncellendi!', 'success');
        },
        error: function(xhr) {
            // Hata durumunda checkbox'ı eski haline getir
            $(this).prop('checked', !isChecked);
            showToast('Bir hata oluştu!', 'error');
        }
    });
});

// İlerleme çubuğunu güncelle
function updateProgressBar() {
    var totalItems = $('.item-checkbox').length;
    var purchasedItems = $('.item-checkbox:checked').length;
    var percentage = totalItems > 0 ? Math.round((purchasedItems / totalItems) * 100) : 0;
    
    $('.progress-bar').css('width', percentage + '%').text(percentage + '%');
}
```

### AJAX ile Ürün Ekleme:

```javascript
$('#addItemForm').on('submit', function(e) {
    e.preventDefault();
    
    var formData = $(this).serialize();
    
    $.ajax({
        url: $(this).attr('action'),
        method: 'POST',
        data: formData,
        success: function(response) {
            // Modal'ı kapat
            $('#addItemModal').modal('hide');
            
            // Formu temizle
            $('#addItemForm')[0].reset();
            
            // Yeni ürünü listeye ekle (sayfa yenilenmeden)
            appendItemToList(response.item);
            
            // İlerleme çubuğunu güncelle
            updateProgressBar();
            
            showToast('Ürün başarıyla eklendi!', 'success');
        },
        error: function(xhr) {
            if (xhr.status === 422) {
                // Validation hatalarını göster
                var errors = xhr.responseJSON.errors;
                displayValidationErrors(errors);
            }
        }
    });
});
```

---

## 🧪 Test ve Debug

### Migration'ları Çalıştırma:

```bash
# Tüm migration'ları çalıştır
php artisan migrate

# Seeder'ları çalıştır  
php artisan db:seed --class=CategorySeeder

# Eğer hata alırsanız, migration'ları geri alıp tekrar çalıştırın
php artisan migrate:rollback
php artisan migrate
```

### Hata Ayıklama İpuçları:

1. **Migration Hataları**:
   - Foreign key hatalarında tablo sırasına dikkat edin
   - Referans edilen tablolar önce oluşturulmalı

2. **Model İlişki Hataları**:
   - `with()` kullanarak eager loading yapın
   - İlişki metodlarının doğru tanımlandığından emin olun

3. **Controller Hataları**:
   - `dd()` ve `dump()` kullanarak debug yapın
   - Log dosyalarını kontrol edin: `storage/logs/laravel.log`

4. **JavaScript Hataları**:
   - Browser console'u kontrol edin (F12)
   - AJAX isteklerinde CSRF token'ı unutmayın

### Başarı Kriterleri:

- ✅ Migration'lar başarıyla çalışıyor
- ✅ CRUD işlemleri çalışıyor (Create, Read, Update, Delete)
- ✅ Form validation çalışıyor
- ✅ İlişkiler doğru çalışıyor
- ✅ Arama ve filtreleme çalışıyor
- 🎁 **Bonus**: AJAX işlemleri sorunsuz çalışıyor

---

## 📝 Proje Teslimi

### Teslim Edilecek Dosyalar:

1. **Migration dosyaları** - Veritabanı yapısı
2. **Model dosyaları** - İlişkiler ve business logic
3. **Controller dosyaları** - İş mantığı
4. **View dosyaları** - Kullanıcı arayüzü
5. **Routes dosyası** - URL tanımlamaları
6. **Seeder dosyaları** - Örnek veriler

### Ekstra Puanlar İçin:

- 🎁 **AJAX kullanımı** - Sayfa yenilenmeden işlemler
- 🎨 **Modern tasarım** - Bootstrap/Vuexy kullanımı
- 🔍 **Gelişmiş arama** - Çoklu filtreler
- 📱 **Responsive tasarım** - Mobil uyumluluk
- 🧪 **Kod kalitesi** - Temiz ve organize kod

---

## 🚀 Başarılar!

Bu rehberi takip ederek modern ve fonksiyonel **Notlar** ve **Alışveriş Listesi** modülleri geliştirebilirsiniz. AJAX kullanımı zorunlu olmasa da kullanıcı deneyimini büyük ölçüde iyileştirecektir.

**Unutmayın**: Küçük adımlarla ilerleyin, sık sık test edin ve hata aldığınızda log dosyalarını kontrol edin!

İyi kodlamalar! 🎯
