<?php
$ip = $_SERVER['REMOTE_ADDR'];
$time = time();

// File untuk hit counting per IP
$hit_file = __DIR__ . '/hit_data.json';

// File blacklist
$blacklist_file = __DIR__ . '/blacklist.txt';

// ---------------------------
// CEK APAKAH IP ADA DI BLACKLIST
// ---------------------------
if (file_exists($blacklist_file)) {
    $blacklist = file($blacklist_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    if (in_array($ip, $blacklist)) {
        die("🚫 Access Denied: Your IP is blacklisted.");
    }
}

// ---------------------------
// CEK FILE HIT DATA
// ---------------------------
if (!file_exists($hit_file)) {
    file_put_contents($hit_file, json_encode([]));
}

$hits = json_decode(file_get_contents($hit_file), true);

// Jika IP belum ada
if (!isset($hits[$ip])) {
    $hits[$ip] = [];
}

// Buang hit lama (>1 detik)
$hits[$ip] = array_filter($hits[$ip], function ($t) use ($time) {
    return $t >= $time - 1;
});

// Tambah hit baru
$hits[$ip][] = $time;

// ---------------------------
// JIKA HIT > 10 PER DETIK → BLOKIR
// ---------------------------
if (count($hits[$ip]) > 10) {

    // Tambahkan IP ke blacklist
    file_put_contents($blacklist_file, $ip . PHP_EOL, FILE_APPEND);

    // Hentikan akses
    die("🚫 Too many requests! Your IP has been blacklisted.");
}

// Simpan update hit
file_put_contents($hit_file, json_encode($hits));
