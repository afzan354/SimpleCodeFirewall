# 🛡 Ultimate Simple Security (USS)

Versi: 1.0  
Author: ganteng banget

Ultimate Simple Security adalah sistem keamanan PHP siap pakai yang melindungi website dari:

- SQL Injection
- XSS
- CSRF
- Bot attacker (sqlmap, curl, python-requests)
- Rate Limit (anti brute force / flooding)
- Auto-blacklist IP berbahaya
- File upload filter (PHP shell protection)
- Session hijacking & session fixation

---

# 📌 Cara Menggunakan

## 1. Pasang file

Letakkan file berikut di root project kamu:

- `ultimate_security.php`
- `blacklist.txt` (kosong)
- `hit_data.json` (kosong)

---

## 2. Tambahkan ke setiap file PHP

Tambahkan **di paling atas**, sebelum koneksi database:

```php
<?php include 'ultimate_security.php'; ?>
```

Contoh yang benar:

```php
<?php include 'ultimate_security.php'; ?>

<?php
$conn = mysqli_connect("localhost", "root", "", "db");

// kode lainnya...
?>
```

Jika kamu meletakkan include **setelah query SQL**, maka SQL Injection masih mungkin terjadi.  
Selalu letakkan paling atas.

---

# 🔐 Fitur Keamanan

## 1. Rate Limit / Anti-Flood

- Memblokir IP yang melakukan lebih dari **10 request per detik**.
- IP otomatis masuk ke `blacklist.txt`.

Config dapat diubah:

```php
"rate_limit" => [
    "max_requests" => 10,
    "per_seconds"  => 1,
],
```

---

## 2. IP Blacklist

Daftar IP yang diblokir tersimpan di:

```
blacklist.txt
```

Untuk membuka blokir:  
➡ Hapus IP dari file.

---

## 3. Block User-Agent Berbahaya

Secara otomatis memblokir bot seperti:

- sqlmap
- curl
- python-requests
- wget
- fuzzer

Daftar dapat diubah:

```php
"block_user_agents" => ["sqlmap","curl","python"],
```

---

## 4. Anti XSS

Gunakan fungsi:

```php
echo xss($data["nama"]);
```

---

## 5. Anti SQL Injection

Gunakan fungsi `clean()` untuk input dasar:

```php
$id = clean($_GET["id"]);
```

Tetap disarankan memakai **prepared statement PDO/MySQLi**.

---

## 6. CSRF Protection

Generate token:

```php
<input type="hidden" name="csrf" value="<?php echo generate_csrf(); ?>">
```

Validasi:

```php
if (!validate_csrf($_POST['csrf'])) {
    die("CSRF Attack Detected");
}
```

---

## 7. Session Security

Melindungi dari:

- Session hijacking
- Session fixation
- IP change detection
- User-agent mismatch

Tidak perlu konfigurasi tambahan, aktif otomatis.

---

## 8. Upload File Security

Validasi file upload:

```php
$check = secure_upload($_FILES['file']);

if ($check !== true) {
    die($check); // tampilkan alasan penolakan
}
```

Aman dari:

- PHP shell
- Fake MIME
- SVG XSS
- Upload file berbahaya lainnya

---

# 📌 Log File

### blacklist.txt

Daftar IP yang diblokir.

### hit_data.json

Data hit per IP untuk rate-limit.

---

# 🧪 Testing Keamanan

### 1. Coba serang pakai SQLMap

```bash
sqlmap -u "http://localhost/detail.php?id=1"
```

Hasil:

- IP otomatis diblokir
- SQLMap gagal membaca DB
- Akses halaman langsung “Access Denied”

### 2. Coba spam 20x per detik

Diblokir otomatis.

### 3. Coba upload file .php

Diblokir.

---

# 🏁 Final Notes

Ultimate Simple Security cocok untuk:

- Website PHP sederhana
- App sekolah
- Sistem CRUD
- Website absensi
- Panel admin custom

Tidak cocok untuk aplikasi enterprise besar.

---

Selesai.
