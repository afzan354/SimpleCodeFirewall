<?php

/**
 * Ultimate Simple Security (USS)
 * Version: 1.0
 * Author: ganteng banget
 * Description: Perlindungan lengkap berbasis PHP untuk mencegah SQL Injection, XSS, CSRF,
 *              Rate Limit, Blacklist IP, Block Bot, File Upload Security, Session Security.
 */

session_start();

/* ============================================================
   1. KONFIGURASI
   ============================================================ */
$config = [
    "rate_limit" => [
        "max_requests" => 10,
        "per_seconds"  => 1,
    ],
    "blacklist_file" => __DIR__ . "/blacklist.txt",
    "hit_data_file"  => __DIR__ . "/hit_data.json",
    "block_user_agents" => [
        "sqlmap",
        "curl",
        "python",
        "wget",
        "fuzzer",
        "libwww"
    ],
    "csrf_token_name" => "csrf_token",
];


/* ============================================================
   2. FUNGSI BANTUAN
   ============================================================ */

// Cegah XSS
function xss($data)
{
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Sanitasi input
function clean($text)
{
    return preg_replace('/[\'";`<>]/', '', $text);
}

// Generate token CSRF
function generate_csrf()
{
    global $config;
    if (!isset($_SESSION[$config['csrf_token_name']])) {
        $_SESSION[$config['csrf_token_name']] = bin2hex(random_bytes(32));
    }
    return $_SESSION[$config['csrf_token_name']];
}

// Validasi token CSRF
function validate_csrf($token)
{
    global $config;
    return isset($_SESSION[$config['csrf_token_name']]) &&
        hash_equals($_SESSION[$config['csrf_token_name']], $token);
}


/* ============================================================
   3. BLACKLIST IP
   ============================================================ */

$ip = $_SERVER['REMOTE_ADDR'];

if (file_exists($config['blacklist_file'])) {
    $blacklist = file($config['blacklist_file'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (in_array($ip, $blacklist)) {
        die("🚫 Access Denied (Blacklisted IP)");
    }
}


/* ============================================================
   4. BLOCK USER-AGENT BERBAHAYA
   ============================================================ */

$ua = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');

foreach ($config['block_user_agents'] as $b) {
    if (strpos($ua, $b) !== false) {
        file_put_contents($config['blacklist_file'], $ip . PHP_EOL, FILE_APPEND);
        die("🚫 Access Denied (Bot Blocked)");
    }
}


/* ============================================================
   5. RATE LIMITING
   ============================================================ */

if (!file_exists($config['hit_data_file'])) {
    file_put_contents($config['hit_data_file'], json_encode([]));
}

$hits = json_decode(file_get_contents($config['hit_data_file']), true);
$time = time();

if (!isset($hits[$ip])) $hits[$ip] = [];

// bersihkan hit lama
$hits[$ip] = array_filter($hits[$ip], fn($t) => $t >= $time - $config["rate_limit"]["per_seconds"]);

// tambah hit baru
$hits[$ip][] = $time;

// jika lebih dari batas → blacklist
if (count($hits[$ip]) > $config["rate_limit"]["max_requests"]) {
    file_put_contents($config['blacklist_file'], $ip . PHP_EOL, FILE_APPEND);
    die("🚫 Too Many Requests — IP Blocked");
}

file_put_contents($config['hit_data_file'], json_encode($hits));


/* ============================================================
   6. SESSION SECURITY
   ============================================================ */

if (!isset($_SESSION['session_ip'])) {
    $_SESSION['session_ip'] = $ip;
}
if ($_SESSION['session_ip'] !== $ip) {
    session_destroy();
    die("🚫 Session Hijacking Detected");
}

if (!isset($_SESSION['user_agent'])) {
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
}
if ($_SESSION['user_agent'] !== ($_SERVER['HTTP_USER_AGENT'] ?? '')) {
    session_destroy();
    die("🚫 Session Stealing Detected");
}


/* ============================================================
   7. PROTEKSI UPLOAD FILE (PILIHAN)
   ============================================================ */

function secure_upload($file)
{
    $allowed_ext  = ['jpg', 'png', 'jpeg', 'gif', 'pdf'];
    $allowed_mime = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];

    $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $mime = mime_content_type($file['tmp_name']);

    if (!in_array($ext, $allowed_ext)) return "❌ Invalid file extension!";
    if (!in_array($mime, $allowed_mime)) return "❌ Invalid MIME type!";
    if ($file['size'] > 5 * 1024 * 1024) return "❌ File too large!";

    return true;
}


/* ============================================================
   SISTEM AKTIF — PAGE AMAN DI BAWAH INI
   ============================================================ */
