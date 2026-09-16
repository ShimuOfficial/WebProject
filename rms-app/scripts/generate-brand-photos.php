<?php

$brandDir = dirname(__DIR__) . '/public/images/brand';
$dishDir = dirname(__DIR__) . '/public/images/dishes';
if (! is_dir($brandDir)) {
    mkdir($brandDir, 0777, true);
}
if (! is_dir($dishDir)) {
    mkdir($dishDir, 0777, true);
}

function rms_color($im, string $hex): int
{
    $hex = ltrim($hex, '#');
    return imagecolorallocate($im, hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2)));
}

function rms_gradient($im, int $w, int $h, array $from, array $to): void
{
    for ($y = 0; $y < $h; $y++) {
        $t = $y / max(1, $h - 1);
        imageline(
            $im,
            0,
            $y,
            $w,
            $y,
            imagecolorallocate(
                $im,
                (int) round($from[0] + ($to[0] - $from[0]) * $t),
                (int) round($from[1] + ($to[1] - $from[1]) * $t),
                (int) round($from[2] + ($to[2] - $from[2]) * $t)
            )
        );
    }
}

function rms_plate($im, int $cx, int $cy, int $rx, int $ry, string $food = 'c45c26'): void
{
    imagefilledellipse($im, $cx + 10, $cy + 16, $rx * 2 + 20, $ry * 2 + 16, rms_color($im, '140e0a'));
    imagefilledellipse($im, $cx, $cy, $rx * 2, $ry * 2, rms_color($im, 'f6efe4'));
    imagefilledellipse($im, $cx, $cy, (int) ($rx * 1.7), (int) ($ry * 1.7), rms_color($im, 'ead9c4'));
    imagefilledellipse($im, $cx, $cy, (int) ($rx * 1.38), (int) ($ry * 1.38), rms_color($im, $food));
    imagefilledellipse($im, $cx - (int) ($rx * 0.2), $cy - (int) ($ry * 0.22), (int) ($rx * 0.62), (int) ($ry * 0.4), rms_color($im, 'f1c27d'));
    imagefilledellipse($im, $cx + (int) ($rx * 0.2), $cy + (int) ($ry * 0.1), (int) ($rx * 0.4), (int) ($ry * 0.26), rms_color($im, '7a2410'));
}

function rms_scene(string $path, int $w, int $h, callable $draw): void
{
    $im = imagecreatetruecolor($w, $h);
    $draw($im, $w, $h);
    imagejpeg($im, $path, 88);
    imagedestroy($im);
}

rms_scene($brandDir . '/hero.jpg', 1600, 1000, function ($im, $w, $h) {
    rms_gradient($im, $w, $h, [24, 16, 10], [96, 46, 24]);
    imagefilledrectangle($im, 0, (int) ($h * 0.62), $w, $h, rms_color($im, '24160f'));
    for ($i = 0; $i < 8; $i++) {
        $x = 90 + $i * 180;
        imagefilledrectangle($im, $x, 80, $x + 64, 420, rms_color($im, $i % 2 ? '3b2418' : '4c2d1c'));
        imagefilledrectangle($im, $x + 14, 110, $x + 50, 390, rms_color($im, 'e8b86d'));
    }
    rms_plate($im, 1180, 640, 230, 150);
    rms_plate($im, 430, 730, 150, 95, '8b2e12');
});

rms_scene($brandDir . '/about.jpg', 1400, 1050, function ($im, $w, $h) {
    rms_gradient($im, $w, $h, [16, 14, 12], [52, 32, 22]);
    imagefilledrectangle($im, 110, 170, $w - 110, 430, rms_color($im, '2a211b'));
    imagefilledrectangle($im, 130, 190, $w - 130, 240, rms_color($im, 'c45c26'));
    for ($i = 0; $i < 6; $i++) {
        imagefilledellipse($im, 250 + $i * 180, 340, 100, 64, rms_color($im, $i % 2 ? 'd9783a' : 'e8b86d'));
    }
    imagefilledrectangle($im, 0, 620, $w, $h, rms_color($im, '1b140f'));
    rms_plate($im, 700, 760, 270, 165, 'b8431f');
});

rms_scene($brandDir . '/login.jpg', 1200, 1500, function ($im, $w, $h) {
    rms_gradient($im, $w, $h, [20, 14, 10], [72, 38, 22]);
    imagefilledrectangle($im, 150, 180, $w - 150, 920, rms_color($im, '241810'));
    for ($r = 0; $r < 4; $r++) {
        for ($c = 0; $c < 2; $c++) {
            $x = 250 + $c * 380;
            $y = 260 + $r * 150;
            imagefilledrectangle($im, $x, $y, $x + 240, $y + 88, rms_color($im, '3a2418'));
            imagefilledellipse($im, $x + 120, $y + 40, 74, 46, rms_color($im, 'c45c26'));
        }
    }
    imagefilledrectangle($im, 0, 1040, $w, $h, rms_color($im, '1a120c'));
    rms_plate($im, 600, 1230, 240, 140);
});

rms_scene($brandDir . '/staff.jpg', 1200, 1500, function ($im, $w, $h) {
    rms_gradient($im, $w, $h, [14, 16, 14], [42, 28, 18]);
    imagefilledrectangle($im, 70, 150, $w - 70, 500, rms_color($im, '2a2f2c'));
    for ($i = 0; $i < 6; $i++) {
        imagefilledrectangle($im, 120 + $i * 175, 190, 250 + $i * 175, 450, rms_color($im, $i % 2 ? 'c45c26' : 'e8b86d'));
    }
    imagefilledrectangle($im, 0, 680, $w, 840, rms_color($im, '11100e'));
    rms_plate($im, 400, 1080, 180, 120, '8b2e12');
    rms_plate($im, 860, 1100, 200, 130);
});

rms_scene($brandDir . '/contact.jpg', 1600, 900, function ($im, $w, $h) {
    rms_gradient($im, $w, $h, [30, 22, 16], [86, 48, 28]);
    imagefilledrectangle($im, 0, 540, $w, $h, rms_color($im, '1e140e'));
    imagefilledrectangle($im, 90, 120, 620, 480, rms_color($im, '3d291c'));
    imagefilledrectangle($im, 120, 150, 590, 450, rms_color($im, 'e8b86d'));
    rms_plate($im, 1100, 520, 250, 150);
});

rms_scene($dishDir . '/plain-rice.jpg', 900, 700, function ($im, $w, $h) {
    rms_gradient($im, $w, $h, [36, 24, 16], [90, 48, 26]);
    rms_plate($im, 450, 380, 300, 210);
});

echo "Brand photos written.\n";
