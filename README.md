<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# KBTS ARJ Sistem - Laravel 12 Admin Panel

Bu proje, Laravel 12 ve AdminLTE 3 tabanlı modern bir admin panel ve kullanıcı yönetimi sistemidir. Proje, kullanıcı ekleme/düzenleme/silme, dark mode, responsive tasarım ve kolay genişletilebilir mimari sunar.

## Başlangıç

### Gereksinimler
- PHP >= 8.2
- Composer
- SQLite (veya MySQL/PostgreSQL)
- Node.js & npm (derleme için)

### Kurulum
1. **Projeyi klonlayın:**
   ```bash
   git clone <repo-url>
   cd kbtsarjsistem
   ```
2. **Bağımlılıkları yükleyin:**
   ```bash
   composer install
   npm install && npm run build
   ```
3. **Ortam dosyasını oluşturun:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. **Veritabanı ayarlarını yapın:**
   - `.env` dosyasında `DB_CONNECTION`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` ayarlarını yapın.
   - Varsayılan olarak SQLite kullanılır. `database/database.sqlite` dosyasını oluşturun.
5. **Migrasyonları ve seed'leri çalıştırın:**
   ```bash
   php artisan migrate --seed
   ```
6. **Sunucuyu başlatın:**
   ```bash
   php artisan serve
   ```

## Kullanıcı Girişi
- Varsayılan admin kullanıcı seed ile oluşturulur.
- Giriş için `/login` sayfasını kullanın.

## Yeni Sayfa (Modül) Oluşturma
### 1. Yeni Controller Oluşturma
```bash
php artisan make:controller ExampleController
```
- Controller dosyası `app/Http/Controllers/ExampleController.php` olarak oluşur.
- İçerisine gerekli metotları ekleyin (ör: `index`, `store`, `update`, `destroy`).

### 2. Yeni Model ve Migration (Tablo) Oluşturma
```bash
php artisan make:model Example -m
```
- Model: `app/Models/Example.php`
- Migration: `database/migrations/xxxx_xx_xx_xxxxxx_create_examples_table.php`
- Migration dosyasını düzenleyin, örnek:
  ```php
  Schema::create('examples', function (Blueprint $table) {
      $table->id();
      $table->string('title');
      $table->timestamps();
  });
  ```
- Migrasyonu çalıştırın:
  ```bash
  php artisan migrate
  ```

### 3. Yeni Route Tanımlama
`routes/web.php` dosyasına ekleyin:
```php
Route::resource('examples', ExampleController::class);
```
veya özel route:
```php
Route::get('/examples', [ExampleController::class, 'index'])->name('examples.index');
```

### 4. Yeni View (Sayfa) Oluşturma
`resources/views/examples/index.blade.php` dosyasını oluşturun. `@extends('layouts.master')` ile layout'u kullanın.

### 5. AdminLTE Sidebar'a Menü Eklemek
`resources/views/layouts/sidebar.blade.php` dosyasına yeni menü ekleyin:
```blade
<li class="nav-item">
    <a href="{{ route('examples.index') }}" class="nav-link">
        <i class="nav-icon fas fa-file"></i>
        <p>Örnekler</p>
    </a>
</li>
```

## Geliştirme İpuçları
- Tüm blade dosyaları `layouts/master.blade.php` üzerinden layout alır.
- Sidebar, header ve footer ayrı dosyalara bölünmüştür (`layouts/sidebar.blade.php`, `layouts/header.blade.php`, `layouts/footer.blade.php`).
- Yeni bir tablo eklerken migration ve model oluşturmayı unutmayın.
- Formlarda CSRF koruması için `@csrf` kullanın.
- Validation için controller'da `$request->validate([...])` kullanın.
- Flash mesajlar için `session('success')` veya `session('error')` blade'de gösterilebilir.

## Sık Kullanılan Artisan Komutları
- Controller oluştur: `php artisan make:controller NameController`
- Model + migration oluştur: `php artisan make:model Name -m`
- Migration çalıştır: `php artisan migrate`
- Seeder çalıştır: `php artisan db:seed`
- Sunucu başlat: `php artisan serve`

## Proje Yapısı
```
app/Http/Controllers/      # Controller dosyaları
app/Models/                # Model dosyaları
resources/views/           # Blade view dosyaları
resources/views/layouts/   # Layout parçaları
routes/web.php             # Web route'ları
config/                    # Konfigürasyon dosyaları
database/migrations/       # Migration dosyaları
database/seeders/          # Seeder dosyaları
public/                    # Public assetler
```

## Sürüm
- Laravel 12.x
- AdminLTE 3

## Katkı Sağlama
Pull request gönderebilir veya issue açabilirsiniz.

## Lisans
MIT
