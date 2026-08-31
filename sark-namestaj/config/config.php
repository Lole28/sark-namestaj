<?php
/**
 * Podešavanja aplikacije.
 * Za lokalni rad kroz XAMPP podrazumevane vrednosti su već ispravne
 * (MySQL: korisnik "root", bez lozinke). Promeni po potrebi.
 */
return [
    'db' => [
        'host'    => '127.0.0.1',
        'port'    => '3306',
        'name'    => 'sark_namestaj',
        'user'    => 'root',
        'pass'    => '',
        'charset' => 'utf8mb4',
    ],

    'app' => [
        'name'             => 'Šark nameštaj po meri',
        'debug'            => true,                 // na produkciji staviti false
        'uploads_path'     => __DIR__ . '/../assets/uploads',
        'uploads_url'      => 'assets/uploads',
        'max_upload_bytes' => 3 * 1024 * 1024,      // 3 MB
        'dozvoljeni_tipovi' => ['image/jpeg', 'image/png', 'image/webp'],
        'kurs_rezerva'     => 117.20,               // EUR -> RSD ako web servis nije dostupan
        'kurs_api'         => 'https://open.er-api.com/v6/latest/EUR',
    ],
];
