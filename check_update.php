<?php
$repo = "dmrcz/ods.dmrcz-dash-2026-v2";
$url = "https://api.github.com";

$options = [
    'http' => [
        'method' => 'GET',
        'header' => "User-Agent: PHP-Update-Checker\r\n"
    ]
];

$context = stream_context_create($options);
$response = file_get_contents($url, false, $context);

if ($response) {
    $data = json_decode($response, true);
    $remote_sha = $data['sha'];
    
    // Načtení lokálního hashe (pokud existuje)
    $local_sha = file_exists('version.txt') ? trim(file_get_contents('version.txt')) : '';

    if ($remote_sha !== $local_sha) {
        echo "Aktualizace je k dispozici! (Hash: $remote_sha)\n";
        // Zde můžete spustit 'git pull' nebo stáhnout zip
    } else {
        echo "Aplikace je aktuální.\n";
    }
}
?>
