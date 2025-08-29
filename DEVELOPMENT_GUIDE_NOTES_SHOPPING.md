# NOTLAR VE ALIŞVERİŞ LİSTESİ MODÜLLERİ GELİŞTİRME REHBERİ

**Proje:** Laravel Eğitim Uygulaması  
**Tarih:** 29 Ağustos 2025  
**Hedef:** Notes ve Shopping List modüllerinin geliştirilmesi  

---

## 📋 PROJE GENEL BİLGİLERİ

### Geliştirilecek Modüller:
1. **NOTLAR (NOTES) MODÜLÜ**
2. **ALIŞVERİŞ LİSTESİ (SHOPPING LIST) MODÜLÜ**

### Kullanılacak Teknolojiler:
- Laravel Framework
- MySQL Veritabanı
- Bootstrap/Vuexy UI
- AJAX (Bonus özellik)

---

## 📝 NOTLAR MODÜLÜ GELİŞTİRME PLANI

### Modül Özellikleri:
- [ ] Not başlığı ve içerik ekleme/düzenleme
- [ ] Etiket (tag) sistemi
- [ ] Arama ve filtreleme (başlık, içerik, tarih)
- [ ] Öncelik seviyesi (düşük, orta, yüksek)
- [ ] CRUD işlemleri
- [ ] **BONUS:** AJAX ile dinamik işlemler

### 1. VERİTABANI KATMANI

#### Oluşturulacak Migration Dosyaları:
- `database/migrations/xxxx_create_notes_table.php`
- `database/migrations/xxxx_create_tags_table.php` 
- `database/migrations/xxxx_create_note_tag_table.php`

#### Çalıştırılacak Artisan Komutları:
```
php artisan make:model Note -m
php artisan make:model Tag -m
php artisan make:migration create_note_tag_table
php artisan migrate
```

#### Notes Tablosu Alanları:
- id (Primary Key)
- title (Başlık)
- content (İçerik - Long Text)
- is_favorite (Boolean)
- priority (Enum: low, medium, high)
- user_id (Foreign Key)
- timestamps

#### Tags Tablosu Alanları:
- id (Primary Key)
- name (Etiket adı - Unique)
- color (Renk kodu)
- timestamps

#### Note_Tag İlişki Tablosu:
- id (Primary Key)
- note_id (Foreign Key)
- tag_id (Foreign Key)
- timestamps
- unique(note_id, tag_id)

### 2. MODEL KATMANI

#### Oluşturulacak Model Dosyaları:
- `app/Models/Note.php`
- `app/Models/Tag.php`

#### Model İlişkileri:
- **Note Model:**
  - belongsTo(User::class)
  - belongsToMany(Tag::class)
  - getPriorityBadgeAttribute() metodu

- **Tag Model:**
  - belongsToMany(Note::class)

### 3. CONTROLLER KATMANI

#### Oluşturulacak Controller:
- `app/Http/Controllers/NoteController.php`

#### Controller Metodları:
- index() - Liste görüntüleme + arama/filtreleme
- store() - Yeni not ekleme
- show() - Tek not görüntüleme (AJAX için)
- update() - Not güncelleme
- destroy() - Not silme

#### Çalıştırılacak Komut:
```
php artisan make:controller NoteController
```

### 4. ROUTES KATMANI

#### Eklenecek Route'lar (web.php):
- GET /notes (Liste)
- POST /notes (Yeni kayıt)
- GET /notes/{id} (Detay)
- PUT /notes/{id} (Güncelleme)
- DELETE /notes/{id} (Silme)

### 5. VIEW KATMANI

#### Oluşturulacak Blade Dosyaları:
- `resources/views/notes/index.blade.php` (Ana liste sayfası)
- `resources/views/notes/partials/add-modal.blade.php` (Ekleme modalı)
- `resources/views/notes/partials/edit-modal.blade.php` (Düzenleme modalı)

#### View Bileşenleri:
- Arama ve filtreleme formu
- Not kartları/liste görünümü
- Pagination
- Modal formlar
- Etiket badge'leri
- Öncelik göstergeleri

---

## 🛒 ALIŞVERİŞ LİSTESİ MODÜLÜ GELİŞTİRME PLANI

### Modül Özellikleri:
- [ ] Ürün ekleme/düzenleme
- [ ] Miktar ve birim belirtme
- [ ] "Alındı/Alınmadı" durumu
- [ ] Kategori sistemi
- [ ] Liste filtreleme
- [ ] İlerleme takibi
- [ ] **BONUS:** AJAX ile durum değiştirme

### 1. VERİTABANI KATMANI

#### Oluşturulacak Migration Dosyaları:
- `database/migrations/xxxx_create_categories_table.php`
- `database/migrations/xxxx_create_shopping_lists_table.php`
- `database/migrations/xxxx_create_shopping_items_table.php`

#### Çalıştırılacak Artisan Komutları:
```
php artisan make:model ShoppingList -m
php artisan make:model ShoppingItem -m
php artisan make:model Category -m
```

#### Categories Tablosu Alanları:
- id (Primary Key)
- name (Kategori adı)
- icon (Font Awesome ikonu)
- color (Renk kodu)
- timestamps

#### Shopping_Lists Tablosu Alanları:
- id (Primary Key)
- name (Liste adı)
- description (Açıklama)
- is_completed (Boolean)
- user_id (Foreign Key)
- timestamps

#### Shopping_Items Tablosu Alanları:
- id (Primary Key)
- name (Ürün adı)
- quantity (Miktar)
- unit (Birim)
- estimated_price (Tahmini fiyat)
- is_purchased (Boolean)
- notes (Notlar)
- shopping_list_id (Foreign Key)
- category_id (Foreign Key)
- timestamps

### 2. MODEL KATMANI

#### Oluşturulacak Model Dosyaları:
- `app/Models/ShoppingList.php`
- `app/Models/ShoppingItem.php`
- `app/Models/Category.php`

#### Model İlişkileri:
- **ShoppingList Model:**
  - belongsTo(User::class)
  - hasMany(ShoppingItem::class)
  - getCompletionPercentageAttribute() metodu
  - getTotalEstimatedPriceAttribute() metodu

- **ShoppingItem Model:**
  - belongsTo(ShoppingList::class)
  - belongsTo(Category::class)

- **Category Model:**
  - hasMany(ShoppingItem::class)

### 3. CONTROLLER KATMANI

#### Oluşturulacak Controller'lar:
- `app/Http/Controllers/ShoppingListController.php`
- `app/Http/Controllers/ShoppingItemController.php`

#### ShoppingListController Metodları:
- index() - Tüm listeler
- show() - Liste detayı
- store() - Yeni liste
- update() - Liste güncelleme
- destroy() - Liste silme

#### ShoppingItemController Metodları:
- store() - Yeni ürün ekleme
- togglePurchased() - Durum değiştirme (AJAX)
- destroy() - Ürün silme

#### Çalıştırılacak Komutlar:
```
php artisan make:controller ShoppingListController
php artisan make:controller ShoppingItemController
```

### 4. SEEDER KATMANI

#### Oluşturulacak Seeder:
- `database/seeders/CategorySeeder.php`

#### Varsayılan Kategoriler:
- Market
- Manav
- Teknoloji
- Eczane
- Kırtasiye
- Temizlik

#### Çalıştırılacak Komutlar:
```
php artisan make:seeder CategorySeeder
php artisan db:seed --class=CategorySeeder
```

### 5. ROUTES KATMANI

#### Eklenecek Route'lar (web.php):
- GET /shopping (Listeler)
- POST /shopping (Yeni liste)
- GET /shopping/{id} (Liste detayı)
- PUT /shopping/{id} (Liste güncelleme)
- DELETE /shopping/{id} (Liste silme)
- POST /shopping/{list}/items (Ürün ekleme)
- PATCH /shopping/items/{item}/toggle (Durum değiştirme)
- DELETE /shopping/items/{item} (Ürün silme)

### 6. VIEW KATMANI

#### Oluşturulacak Blade Dosyaları:
- `resources/views/shopping/index.blade.php` (Tüm listeler)
- `resources/views/shopping/show.blade.php` (Liste detayı)
- `resources/views/shopping/partials/list-card.blade.php` (Liste kartı)
- `resources/views/shopping/partials/item-row.blade.php` (Ürün satırı)
- `resources/views/shopping/partials/add-item-modal.blade.php` (Ürün ekleme modalı)

#### View Bileşenleri:
- Liste kartları (ilerleme çubuğu ile)
- Ürün listeleri (checkbox'lar ile)
- Kategori filtreleri
- İlerleme göstergeleri
- Modal formlar

---

## 🎁 BONUS ÖZELLİKLER (AJAX İMPLEMENTASYONU)

### Uygulanacak AJAX Özellikleri:

#### Notes Modülü:
- [ ] Sayfa yenilenmeden not silme
- [ ] Modal ile not düzenleme
- [ ] Anlık arama (typing sırasında)
- [ ] Etiket ekleme/çıkarma

#### Shopping List Modülü:
- [ ] Ürün durumu değiştirme (checkbox)
- [ ] İlerleme çubuğu güncellemesi
- [ ] Sayfa yenilenmeden ürün ekleme
- [ ] Anlık filtreleme

### Gerekli JavaScript Dosyaları:
- `public/assets/js/notes-ajax.js`
- `public/assets/js/shopping-ajax.js`

### AJAX Endpoint'leri:
- JSON response'lar için API route'ları
- CSRF token yönetimi
- Error handling

---

## 📁 DOSYA YAPISI ÖZETİ

### Oluşturulacak/Değiştirilecek Dosyalar:

```
database/
├── migrations/
│   ├── xxxx_create_notes_table.php
│   ├── xxxx_create_tags_table.php
│   ├── xxxx_create_note_tag_table.php
│   ├── xxxx_create_categories_table.php
│   ├── xxxx_create_shopping_lists_table.php
│   └── xxxx_create_shopping_items_table.php
├── seeders/
│   └── CategorySeeder.php

app/
├── Models/
│   ├── Note.php
│   ├── Tag.php
│   ├── ShoppingList.php
│   ├── ShoppingItem.php
│   └── Category.php
└── Http/Controllers/
    ├── NoteController.php
    ├── ShoppingListController.php
    └── ShoppingItemController.php

resources/views/
├── notes/
│   ├── index.blade.php
│   └── partials/
│       ├── add-modal.blade.php
│       └── edit-modal.blade.php
└── shopping/
    ├── index.blade.php
    ├── show.blade.php
    └── partials/
        ├── list-card.blade.php
        ├── item-row.blade.php
        └── add-item-modal.blade.php

routes/
└── web.php (güncellenecek)

public/assets/js/ (BONUS)
├── notes-ajax.js
└── shopping-ajax.js
```

---

## ✅ GELİŞTİRME ADIMLARI SIRALAmaSI

### ADIM 1: NOTES MODÜLÜ - VERİTABANI
1. Note, Tag modellerini ve migration'larını oluştur
2. Note-Tag ilişki migration'ını oluştur
3. Migration'ları çalıştır

### ADIM 2: NOTES MODÜLÜ - BACKEND
1. Model ilişkilerini tanımla
2. NoteController'ı oluştur ve metodları yaz
3. Routes'ları ekle

### ADIM 3: NOTES MODÜLÜ - FRONTEND
1. Ana liste view'ını oluştur
2. Modal'ları oluştur
3. Form validation'ları ekle

### ADIM 4: SHOPPING MODÜLÜ - VERİTABANI
1. Category, ShoppingList, ShoppingItem modellerini oluştur
2. Migration'ları çalıştır
3. CategorySeeder'ı oluştur ve çalıştır

### ADIM 5: SHOPPING MODÜLÜ - BACKEND
1. Model ilişkilerini tanımla
2. Controller'ları oluştur
3. Routes'ları ekle

### ADIM 6: SHOPPING MODÜLÜ - FRONTEND
1. Liste ve detay view'larını oluştur
2. Ürün ekleme/düzenleme formlarını oluştur
3. İlerleme takibi ekle

### ADIM 7: BONUS - AJAX ÖZELLİKLERİ
1. JavaScript dosyalarını oluştur
2. AJAX endpoint'lerini hazırla
3. Dinamik güncellemeleri implement et

### ADIM 8: TEST VE DOĞRULAMA
1. CRUD işlemlerini test et
2. Filtreleme/arama özelliklerini test et
3. AJAX işlemlerini test et
4. Responsive tasarımı kontrol et

---

## 🔧 TEKNİK GEREKSİNİMLER

### Kullanılacak Laravel Artisan Komutları:
```bash
# Model ve Migration oluşturma
php artisan make:model ModelName -m

# Controller oluşturma  
php artisan make:controller ControllerName

# Migration çalıştırma
php artisan migrate

# Seeder oluşturma ve çalıştırma
php artisan make:seeder SeederName
php artisan db:seed --class=SeederName

# Cache temizleme (gerekirse)
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Validation Kuralları:
- **Notes:** title (required, max:255), content (required), priority (required, in:low,medium,high)
- **Shopping Lists:** name (required, max:255), description (nullable)
- **Shopping Items:** name (required, max:255), quantity (integer, min:1), category_id (exists:categories,id)

### UI/UX Gereksinimleri:
- Bootstrap 5 / Vuexy teması kullanımı
- Responsive tasarım (mobil uyumlu)
- Modal'lar ile kullanıcı dostu formlar
- Toast/Alert mesajları
- Loading göstergeleri (AJAX için)

---

## 📊 BAŞARI KRİTERLERİ

### Temel Gereksinimler (Zorunlu):
- [ ] Migration'lar hatasız çalışıyor
- [ ] CRUD işlemleri çalışıyor
- [ ] Form validation çalışıyor
- [ ] Model ilişkileri doğru
- [ ] Arama/filtreleme çalışıyor
- [ ] UI tasarımı tutarlı

### Bonus Puanlar:
- [ ] AJAX kullanımı
- [ ] Modern ve şık tasarım
- [ ] Gelişmiş filtreleme
- [ ] Responsive tasarım
- [ ] Temiz ve organize kod
- [ ] Error handling

### Test Senaryoları:
1. Not ekleme/düzenleme/silme
2. Etiket ekleme ve not'lara atama
3. Arama ve filtreleme
4. Alışveriş listesi oluşturma
5. Ürün ekleme ve durum değiştirme
6. Kategori filtreleme
7. İlerleme takibi

---

## 📞 DESTEK VE DOKÜMANTASYON

### Faydalı Laravel Dokümantasyon Bölümleri:
- Eloquent Relationships
- Migration Guide
- Validation
- Controllers
- Blade Templates

### Hata Durumunda Kontrol Edilecekler:
- `storage/logs/laravel.log` dosyası
- Browser console (F12) 
- Network tab (AJAX istekleri için)
- Database bağlantısı

### Kod Kalitesi İçin:
- Meaningful değişken isimleri
- Comment'lar önemli yerlerde
- DRY principle (Don't Repeat Yourself)
- Single Responsibility Principle

---

**Son Güncelleme:** 29 Ağustos 2025  
**Tahmini Tamamlanma Süresi:** 2-3 gün  
**Zorluk Seviyesi:** Orta  

Bu rehberi takip ederek başarılı bir şekilde Notes ve Shopping List modüllerini geliştirebilirsiniz. Her adımda test etmeyi unutmayın!
