<?php
$databasePath = __DIR__ . '/../database/rewards.db';
$pdo = new PDO('sqlite:' . $databasePath);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$shouldSeed = $pdo->query(
    'SELECT 1 FROM sqlite_master WHERE type = \'table\' AND name = \'GIFTCARD\''
)->fetchColumn() === false;

foreach (['schema.sql', 'seed.sql'] as $sqlFile) {
    if ($sqlFile === 'seed.sql' && !$shouldSeed) {
        continue;
    }

    $sqlPath = __DIR__ . '/../database/' . $sqlFile;
    $sql = file_get_contents($sqlPath);
    if ($sql === false) {
        throw new RuntimeException('Unable to read database file: ' . $sqlFile);
    }


    if ($sqlFile === 'schema.sql') {
        $pdo->exec($sql);
        $columns = $pdo->query('PRAGMA table_info("GIFTCARD")')->fetchAll(PDO::FETCH_COLUMN, 1);
        if (!in_array('description', $columns, true)) {
            $pdo->exec('ALTER TABLE "GIFTCARD" ADD COLUMN description TEXT NOT NULL DEFAULT \'\'');
        }
        continue;
    }

    $pdo->exec($sql);
}