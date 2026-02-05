<?php

$root = dirname(__DIR__);
$envPath = $root . DIRECTORY_SEPARATOR . '.env';
if (!file_exists($envPath)) {
    fwrite(STDERR, ".env not found\n");
    exit(1);
}

$env = [];
foreach (file($envPath, FILE_IGNORE_NEW_LINES) as $line) {
    $line = trim($line);
    if ($line === '' || str_starts_with($line, '#')) {
        continue;
    }
    $parts = explode('=', $line, 2);
    if (count($parts) !== 2) {
        continue;
    }
    $key = trim($parts[0]);
    $val = trim($parts[1]);
    if ($val !== '' && ($val[0] === '"' || $val[0] === "'")) {
        $val = substr($val, 1, -1);
    }
    $env[$key] = $val;
}

$driver = $env['DB_CONNECTION'] ?? 'pgsql';
if ($driver !== 'pgsql') {
    fwrite(STDERR, "Only pgsql is supported by this generator.\n");
    exit(1);
}

$host = $env['DB_HOST'] ?? '127.0.0.1';
$port = $env['DB_PORT'] ?? '5432';
$db   = $env['DB_DATABASE'] ?? '';
$user = $env['DB_USERNAME'] ?? '';
$pass = $env['DB_PASSWORD'] ?? '';
$schema = $env['DB_SCHEMA'] ?? 'public';

$dsn = "pgsql:host={$host};port={$port};dbname={$db}";
try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (Throwable $e) {
    fwrite(STDERR, "DB connect failed: {$e->getMessage()}\n");
    exit(1);
}

$tablesStmt = $pdo->prepare(
    "SELECT table_name FROM information_schema.tables WHERE table_schema = :schema AND table_type = 'BASE TABLE' ORDER BY table_name"
);
$tablesStmt->execute(['schema' => $schema]);
$tables = $tablesStmt->fetchAll(PDO::FETCH_COLUMN);

$columnsStmt = $pdo->prepare(
    "SELECT table_name, column_name, data_type, is_nullable, column_default, ordinal_position,
            character_maximum_length, numeric_precision, numeric_scale
     FROM information_schema.columns
     WHERE table_schema = :schema
     ORDER BY table_name, ordinal_position"
);
$columnsStmt->execute(['schema' => $schema]);
$columns = $columnsStmt->fetchAll(PDO::FETCH_ASSOC);

$pkStmt = $pdo->prepare(
    "SELECT tc.table_name, kcu.column_name
     FROM information_schema.table_constraints tc
     JOIN information_schema.key_column_usage kcu
       ON tc.constraint_name = kcu.constraint_name AND tc.table_schema = kcu.table_schema
     WHERE tc.constraint_type = 'PRIMARY KEY' AND tc.table_schema = :schema"
);
$pkStmt->execute(['schema' => $schema]);
$pkRows = $pkStmt->fetchAll(PDO::FETCH_ASSOC);

$fkStmt = $pdo->prepare(
    "SELECT tc.table_name, kcu.column_name, ccu.table_name AS foreign_table_name,
            ccu.column_name AS foreign_column_name, tc.constraint_name
     FROM information_schema.table_constraints tc
     JOIN information_schema.key_column_usage kcu
       ON tc.constraint_name = kcu.constraint_name AND tc.table_schema = kcu.table_schema
     JOIN information_schema.constraint_column_usage ccu
       ON ccu.constraint_name = tc.constraint_name AND ccu.table_schema = tc.table_schema
     WHERE tc.constraint_type = 'FOREIGN KEY' AND tc.table_schema = :schema
     ORDER BY tc.table_name, kcu.column_name"
);
$fkStmt->execute(['schema' => $schema]);
$fkRows = $fkStmt->fetchAll(PDO::FETCH_ASSOC);

$colsByTable = [];
foreach ($tables as $t) {
    $colsByTable[$t] = [];
}
foreach ($columns as $col) {
    $colsByTable[$col['table_name']][] = $col;
}

$pkByTable = [];
foreach ($pkRows as $row) {
    $pkByTable[$row['table_name']][] = $row['column_name'];
}

$fkByTable = [];
$edges = [];
foreach ($fkRows as $row) {
    $t = $row['table_name'];
    $c = $row['column_name'];
    $ft = $row['foreign_table_name'];
    $fkByTable[$t][$c][] = $row;
    $edges[$t . '->' . $ft] = [$t, $ft];
}

// ===== Drawing =====
if (!extension_loaded('gd')) {
    fwrite(STDERR, "GD extension not loaded.\n");
    exit(1);
}

$font = 'C:\\Windows\\Fonts\\arial.ttf';
if (!file_exists($font)) {
    fwrite(STDERR, "Font not found: {$font}\n");
    exit(1);
}

function textWidth($fontSize, $font, $text): int
{
    $bbox = imagettfbbox($fontSize, 0, $font, $text);
    return (int) abs($bbox[2] - $bbox[0]);
}

function textHeight($fontSize, $font, $text): int
{
    $bbox = imagettfbbox($fontSize, 0, $font, $text);
    return (int) abs($bbox[5] - $bbox[1]);
}

function wrapLine(string $text, int $maxWidth, int $fontSize, string $font): array
{
    if (textWidth($fontSize, $font, $text) <= $maxWidth) {
        return [$text];
    }
    $parts = explode(' ', $text);
    $lines = [];
    $current = '';
    foreach ($parts as $part) {
        $candidate = $current === '' ? $part : $current . ' ' . $part;
        if (textWidth($fontSize, $font, $candidate) <= $maxWidth) {
            $current = $candidate;
        } else {
            if ($current !== '') {
                $lines[] = $current;
            }
            $current = $part;
        }
    }
    if ($current !== '') {
        $lines[] = $current;
    }
    return $lines;
}

function formatType(array $col): string
{
    $type = $col['data_type'];
    if ($type === 'character varying' && !empty($col['character_maximum_length'])) {
        $type = 'varchar(' . $col['character_maximum_length'] . ')';
    } elseif ($type === 'character' && !empty($col['character_maximum_length'])) {
        $type = 'char(' . $col['character_maximum_length'] . ')';
    } elseif ($type === 'numeric' && $col['numeric_precision'] !== null) {
        $scale = $col['numeric_scale'] ?? 0;
        $type = 'numeric(' . $col['numeric_precision'] . ',' . $scale . ')';
    }
    return $type;
}

$boxW = 520;
$boxPadding = 8;
$headerFont = 12;
$colFont = 9;
$lineGap = 2;
$headerHeight = textHeight($headerFont, $font, 'Ag') + 6;
$lineHeight = textHeight($colFont, $font, 'Ag') + $lineGap;

$tableData = [];
$totalHeight = 0;
foreach ($tables as $table) {
    $colLines = [];
    foreach ($colsByTable[$table] as $col) {
        $type = formatType($col);
        $label = $col['column_name'] . ' : ' . $type;
        $isPk = in_array($col['column_name'], $pkByTable[$table] ?? [], true);
        $isFk = isset($fkByTable[$table][$col['column_name']]);
        if ($isPk) {
            $label .= ' [PK]';
        }
        if ($isFk) {
            $label .= ' [FK]';
        }
        $wrapped = wrapLine($label, $boxW - ($boxPadding * 2), $colFont, $font);
        $colLines[] = $wrapped;
    }

    $linesCount = 0;
    foreach ($colLines as $lines) {
        $linesCount += count($lines);
    }
    $height = $boxPadding + $headerHeight + $boxPadding + ($linesCount * $lineHeight) + $boxPadding;
    $tableData[$table] = [
        'lines' => $colLines,
        'height' => $height,
    ];
    $totalHeight += $height + 30; // spacing
}

$targetHeight = 5200;
$cols = (int) ceil($totalHeight / $targetHeight);
$cols = max(1, min(6, $cols));
$colGap = 60;
$marginX = 60;
$marginY = 60;

$colHeights = array_fill(0, $cols, $marginY);
$positions = [];

foreach ($tables as $table) {
    $minIndex = 0;
    $minHeight = $colHeights[0];
    for ($i = 1; $i < $cols; $i++) {
        if ($colHeights[$i] < $minHeight) {
            $minHeight = $colHeights[$i];
            $minIndex = $i;
        }
    }

    $x = $marginX + ($minIndex * ($boxW + $colGap));
    $y = $colHeights[$minIndex];
    $positions[$table] = [
        'x' => $x,
        'y' => $y,
        'w' => $boxW,
        'h' => $tableData[$table]['height'],
        'cx' => (int) ($x + ($boxW / 2)),
        'cy' => (int) ($y + ($tableData[$table]['height'] / 2)),
    ];
    $colHeights[$minIndex] += $tableData[$table]['height'] + 30;
}

$imgW = (int) ($marginX * 2 + $cols * $boxW + ($cols - 1) * $colGap);
$imgH = (int) (max($colHeights) + $marginY);

$img = imagecreatetruecolor($imgW, $imgH);
$white = imagecolorallocate($img, 255, 255, 255);
$black = imagecolorallocate($img, 20, 20, 20);
$gray = imagecolorallocate($img, 120, 120, 120);
$boxFill = imagecolorallocate($img, 236, 245, 255);
$headerFill = imagecolorallocate($img, 210, 226, 250);

imagefill($img, 0, 0, $white);

// Draw edges first
foreach ($edges as $edge) {
    $from = $edge[0];
    $to = $edge[1];
    if (!isset($positions[$from], $positions[$to])) {
        continue;
    }
    $x1 = $positions[$from]['cx'];
    $y1 = $positions[$from]['cy'];
    $x2 = $positions[$to]['cx'];
    $y2 = $positions[$to]['cy'];
    imageline($img, $x1, $y1, $x2, $y2, $gray);
}

// Draw boxes
foreach ($tables as $table) {
    $pos = $positions[$table];
    $x = $pos['x'];
    $y = $pos['y'];
    $w = $pos['w'];
    $h = $pos['h'];

    imagefilledrectangle($img, $x, $y, $x + $w, $y + $h, $boxFill);
    imagerectangle($img, $x, $y, $x + $w, $y + $h, $black);

    // Header
    imagefilledrectangle($img, $x, $y, $x + $w, $y + $headerHeight + ($boxPadding / 2), $headerFill);
    imagerectangle($img, $x, $y, $x + $w, $y + $headerHeight + ($boxPadding / 2), $black);

    $headerTextWidth = textWidth($headerFont, $font, $table);
    $headerX = (int) ($x + (($w - $headerTextWidth) / 2));
    $headerY = (int) ($y + $headerHeight);
    imagettftext($img, $headerFont, 0, $headerX, $headerY, $black, $font, $table);

    $cursorY = (int) ($y + $headerHeight + $boxPadding + 6);
    foreach ($tableData[$table]['lines'] as $lines) {
        foreach ($lines as $line) {
            $cursorY += $lineHeight;
            imagettftext($img, $colFont, 0, $x + $boxPadding, $cursorY, $black, $font, $line);
        }
    }
}

$footer = 'Full DB ERD (tables + columns + FK). Polymorphic relations shown via *_type/_id columns.';
$footerFont = 9;
$footerY = $imgH - 20;
imagettftext($img, $footerFont, 0, $marginX, $footerY, $gray, $font, $footer);

$docsDir = $root . DIRECTORY_SEPARATOR . 'docs';
if (!is_dir($docsDir)) {
    mkdir($docsDir, 0775, true);
}

$output = $docsDir . DIRECTORY_SEPARATOR . 'erd_full.jpg';
imagejpeg($img, $output, 90);
imagedestroy($img);

echo "Wrote {$output}\n";
