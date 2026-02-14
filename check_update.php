<?php
$repo = "dmrcz/ods.dmrcz-dash-2026-v2";
$local_hash = "VAS_LOKALNI_COMMIT_HASH"; // Získat např. přes: git rev-parse HEAD

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.github.com");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, "PHP-Update-Checker");

$response = json_decode(curl_exec($ch), true);
curl_close($ch);

$remote_hash = $response['sha'] ?? null;

if ($remote_hash && $remote_hash !== $local_hash) {
    echo "Aktualizace je k dispozici! (Remote: $remote_hash)";
} else {
    echo "Verze je aktuální.";
}
