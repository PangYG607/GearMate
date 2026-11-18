<?php
// Auth helpers share the existing PHP/MySQL configuration; no hard-coded accounts.
require_once __DIR__ . '/functions.php';
function bootAuth(): void {
    if (session_status() === PHP_SESSION_ACTIVE) return;
    if (!headers_sent()) {
        session_set_cookie_params([
            'httponly' => true, 'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
            'samesite' => 'Lax', 'path' => '/'
        ]);
    }
    session_start();
}
function csrfToken(): string {
    bootAuth();
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function verifyCsrf(string $token): bool {
    bootAuth();
    return $token !== '' && hash_equals($_SESSION['csrf'] ?? '', $token);
}
function currentUser(): ?array {
    bootAuth();
    return !empty($_SESSION['account_id']) ? ['id' => (int)$_SESSION['account_id'], 'name' => (string)$_SESSION['account_name']] : null;
}
function safeReturnTo(?string $path): string {
    // Only allow local page paths, not redirect links to other domains.
    if (!$path || !preg_match('/^(index|results|catalogue|account|questionnaire|analysis|builder|battle|academy|progress|preferences|setup_card)\.php(?:\?[a-zA-Z0-9_=&%+.-]*)?$/', $path)) return 'account.php';
    return $path;
}
function userDatabase(): ?PDO {
    try { return getDbConnection(); } catch (PDOException $e) { return null; }
}

// Guest access is intentionally session-only. Never use guest ID 0 in SQL queries.
function currentActor(): ?array {
    $account=currentUser();
    if ($account) return $account+['guest'=>false];
    bootAuth();
    if (!empty($_SESSION['guest_enabled'])) return ['id'=>0,'name'=>(string)($_SESSION['guest_name']??'Guest Player'),'guest'=>true];
    return null;
}
function isGuest(): bool { $a=currentActor(); return $a!==null && !empty($a['guest']); }
