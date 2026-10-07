<?php

return [
    'disable' => env('CAPTCHA_DISABLE', false),
    'characters' => ['2', '3', '4', '6', '7', '8', '9'],
    'default' => [
        'length' => 4,
        'width' => 250,
        'height' => 76,
        'quality' => 90,
        'math' => false,
        'expire' => 300, // Ubah dari 120 menjadi 300 (5 menit)
        'encrypt' => false,
    ],
    'math' => [
        'length' => 9,
        'width' => 120,
        'height' => 36,
        'quality' => 90,
        'math' => true,
        'expire' => 300, // Tambahkan agar sesuai dengan durasi 5 menit
    ],

    'flat' => [
        'length' => 6,
        'width' => 160,
        'height' => 46,
        'quality' => 90,
        'lines' => 6,
        'bgImage' => false,
        'bgColor' => '#ecf2f4',
        'fontColors' => ['#2c3e50', '#c0392b', '#16a085', '#c0392b', '#8e44ad', '#303f9f', '#f57c00', '#795548'],
        'contrast' => -5,
        'expire' => 300, // Tambahkan agar semua tipe memiliki expired yang sama
    ],
    'mini' => [
        'length' => 3,
        'width' => 60,
        'height' => 32,
        'expire' => 300, // Tambahkan expired 5 menit
    ],
    'inverse' => [
        'length' => 5,
        'width' => 120,
        'height' => 36,
        'quality' => 90,
        'sensitive' => true,
        'angle' => 12,
        'sharpen' => 10,
        'blur' => 2,
        'invert' => true,
        'contrast' => -5,
        'expire' => 300, // Tambahkan expired 5 menit
    ]
];
