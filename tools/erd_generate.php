<?php

if (!extension_loaded('gd')) {
    fwrite(STDERR, "GD extension not loaded.\n");
    exit(1);
}

$columns = [
    'Basic / User' => [
        'branches',
        'users',
        'positions',
        'roles',
        'permission_groups',
        'permissions',
        'role_has_permissions',
        'model_has_roles',
        'model_has_permissions',
        'user_branches',
    ],
    'Corsec Core' => [
        'corsec_directorates',
        'corsec_incoming_letters',
        'corsec_incoming_letter_routes',
        'corsec_incoming_letter_directorates',
        'corsec_senders',
        'corsec_letter_types',
        'corsec_letter_numbers',
        'corsec_outgoing_letters',
        'corsec_outgoing_letter_number_requests',
        'corsec_attachments',
        'corsec_workflows',
        'corsec_workflow_steps',
    ],
    'Corsec Ops' => [
        'corsec_meetings',
        'corsec_meeting_agendas',
        'corsec_meeting_materials',
        'corsec_meeting_minutes',
        'corsec_meeting_decisions',
        'corsec_decision_updates',
        'corsec_library_categories',
        'corsec_library_items',
        'corsec_work_programs',
        'corsec_work_program_items',
        'corsec_work_program_updates',
    ],
];

$edges = [
    ['users', 'branches'],
    ['users', 'corsec_directorates'],
    ['users', 'positions'],
    ['roles', 'positions'],
    ['permissions', 'permission_groups'],
    ['role_has_permissions', 'roles'],
    ['role_has_permissions', 'permissions'],
    ['model_has_roles', 'roles'],
    ['model_has_permissions', 'permissions'],
    ['user_branches', 'users'],
    ['user_branches', 'branches'],

    ['corsec_incoming_letters', 'corsec_directorates'],
    ['corsec_incoming_letters', 'corsec_senders'],
    ['corsec_incoming_letters', 'corsec_letter_types'],
    ['corsec_incoming_letters', 'branches'],

    ['corsec_incoming_letter_routes', 'corsec_incoming_letters'],
    ['corsec_incoming_letter_routes', 'corsec_directorates'],

    ['corsec_incoming_letter_directorates', 'corsec_incoming_letters'],
    ['corsec_incoming_letter_directorates', 'corsec_directorates'],

    ['corsec_outgoing_letters', 'corsec_directorates'],
    ['corsec_outgoing_letters', 'corsec_senders'],
    ['corsec_outgoing_letters', 'corsec_incoming_letters'],
    ['corsec_outgoing_letters', 'corsec_attachments'],
    ['corsec_outgoing_letters', 'corsec_letter_numbers'],
    ['corsec_outgoing_letter_number_requests', 'corsec_outgoing_letters'],

    ['corsec_workflow_steps', 'corsec_workflows'],
    ['corsec_workflow_steps', 'roles'],

    ['corsec_meeting_agendas', 'corsec_meetings'],
    ['corsec_meeting_agendas', 'corsec_directorates'],
    ['corsec_meeting_materials', 'corsec_meetings'],
    ['corsec_meeting_materials', 'corsec_meeting_agendas'],
    ['corsec_meeting_materials', 'corsec_attachments'],
    ['corsec_meeting_minutes', 'corsec_meetings'],
    ['corsec_meeting_minutes', 'corsec_attachments'],
    ['corsec_meeting_decisions', 'corsec_meetings'],
    ['corsec_meeting_decisions', 'corsec_directorates'],
    ['corsec_decision_updates', 'corsec_meeting_decisions'],

    ['corsec_library_items', 'corsec_library_categories'],
    ['corsec_library_items', 'corsec_attachments'],

    ['corsec_work_programs', 'corsec_directorates'],
    ['corsec_work_program_items', 'corsec_work_programs'],
    ['corsec_work_program_updates', 'corsec_work_program_items'],
];

$imgW = 2400;
$imgH = 1600;
$marginX = 60;
$marginY = 60;
$boxW = 360;
$boxH = 46;
$rowHeight = 70;
$headerGap = 32;

$img = imagecreatetruecolor($imgW, $imgH);
$white = imagecolorallocate($img, 255, 255, 255);
$black = imagecolorallocate($img, 40, 40, 40);
$gray = imagecolorallocate($img, 120, 120, 120);
$boxFill = imagecolorallocate($img, 232, 240, 255);
$headerColor = imagecolorallocate($img, 30, 30, 30);

imagefill($img, 0, 0, $white);

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

function wrapLabel(string $text, int $maxWidth, int $fontSize, string $font): array
{
    $parts = explode('_', $text);
    $lines = [];
    $current = '';

    foreach ($parts as $part) {
        $candidate = $current === '' ? $part : $current . '_' . $part;
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

$positions = [];
$colCount = count($columns);
$colWidth = (int) (($imgW - ($marginX * 2)) / $colCount);

$colIndex = 0;
foreach ($columns as $colName => $tables) {
    $x = (int) ($marginX + ($colIndex * $colWidth) + (($colWidth - $boxW) / 2));
    $yStart = $marginY + $headerGap;

    foreach ($tables as $rowIndex => $table) {
        $y = (int) ($yStart + ($rowIndex * $rowHeight));
        $positions[$table] = [
            'x' => $x,
            'y' => $y,
            'w' => $boxW,
            'h' => $boxH,
            'cx' => (int) ($x + ($boxW / 2)),
            'cy' => (int) ($y + ($boxH / 2)),
        ];
    }

    $colIndex++;
}

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

$headerSize = 16;
$labelSize = 10;
$labelPadding = 10;
$lineHeight = textHeight($labelSize, $font, 'Ag') + 2;

$colIndex = 0;
foreach ($columns as $colName => $tables) {
    $headerX = (int) ($marginX + ($colIndex * $colWidth));
    $headerY = (int) ($marginY + 8 + $headerSize);
    imagettftext($img, $headerSize, 0, $headerX, $headerY, $headerColor, $font, $colName);
    $colIndex++;
}

foreach ($positions as $table => $pos) {
    $x = $pos['x'];
    $y = $pos['y'];
    $w = $pos['w'];
    $h = $pos['h'];

    imagefilledrectangle($img, $x, $y, $x + $w, $y + $h, $boxFill);
    imagerectangle($img, $x, $y, $x + $w, $y + $h, $black);

    $lines = wrapLabel($table, $w - ($labelPadding * 2), $labelSize, $font);
    $totalTextHeight = count($lines) * $lineHeight;
    $textY = (int) ($y + (($h - $totalTextHeight) / 2) + $lineHeight);

    foreach ($lines as $line) {
        $lineWidth = textWidth($labelSize, $font, $line);
        $textX = (int) ($x + (($w - $lineWidth) / 2));
        imagettftext($img, $labelSize, 0, $textX, $textY, $black, $font, $line);
        $textY += $lineHeight;
    }
}

$footer = 'Core ERD (audit + polymorphic relations omitted for readability)';
$footerSize = 10;
$footerX = $marginX;
$footerY = $imgH - 30;
imagettftext($img, $footerSize, 0, $footerX, $footerY, $gray, $font, $footer);

$root = dirname(__DIR__);
$docsDir = $root . DIRECTORY_SEPARATOR . 'docs';
if (!is_dir($docsDir)) {
    mkdir($docsDir, 0775, true);
}
$output = $docsDir . DIRECTORY_SEPARATOR . 'erd.png';
imagepng($img, $output);
imagedestroy($img);

echo "Wrote {$output}\n";
