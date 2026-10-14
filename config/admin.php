<?php

use App\Models\Article;
use App\Models\HeroSlide;
use App\Models\Package;
use App\Models\Page;
use App\Models\Testimonial;

return [
    'slides' => ['label' => 'Hero Carousel', 'model' => HeroSlide::class, 'title' => 'title', 'fields' => [
        'title' => ['Judul slide', 'text', 'required|string|max:120'],
        'caption' => ['Keterangan singkat', 'text', 'nullable|string|max:200'],
        'image_alt' => ['Deskripsi gambar', 'text', 'required|string|max:200'],
        'sort_order' => ['Urutan tampil (angka terkecil dahulu)', 'number', 'required|integer|min:0|max:10000'],
        'is_published' => ['Tampilkan slide', 'checkbox', 'boolean'],
    ]],
    'packages' => ['label' => 'Paket Umrah', 'model' => Package::class, 'title' => 'name', 'fields' => [
        'name' => ['Nama paket', 'text', 'required|string|max:180'],
        'slug' => ['URL paket', 'text', 'required|string|max:180|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
        'departure_date' => ['Tanggal keberangkatan', 'date', 'required|date'],
        'days' => ['Durasi (hari)', 'number', 'required|integer|min:1|max:90'],
        'price' => ['Harga per jamaah (Rp)', 'number', 'required|integer|min:0|max:2000000000'],
        'airline' => ['Maskapai', 'text', 'required|string|max:150'],
        'madinah_hotel' => ['Hotel Madinah', 'text', 'required|string|max:150'],
        'madinah_stars' => ['Bintang hotel Madinah', 'number', 'required|integer|min:1|max:5'],
        'makkah_hotel' => ['Hotel Makkah', 'text', 'required|string|max:150'],
        'makkah_stars' => ['Bintang hotel Makkah', 'number', 'required|integer|min:1|max:5'],
        'includes_thaif' => ['Termasuk kunjungan Thaif', 'checkbox', 'boolean'],
        'description' => ['Deskripsi / itinerary (Markdown)', 'textarea', 'nullable|string|max:30000'],
        'facilities' => ['Fasilitas khusus paket (Markdown)', 'textarea', 'nullable|string|max:10000'],
        'seo_title' => ['Judul SEO', 'text', 'nullable|string|max:70'],
        'seo_description' => ['Deskripsi SEO', 'textarea', 'nullable|string|max:170'],
        'is_published' => ['Tampilkan di situs', 'checkbox', 'boolean'],
    ]],
    'articles' => ['label' => 'Berita Umrah', 'model' => Article::class, 'title' => 'title', 'fields' => [
        'title' => ['Judul berita', 'text', 'required|string|max:180'],
        'slug' => ['URL berita', 'text', 'required|string|max:180|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
        'excerpt' => ['Ringkasan', 'textarea', 'required|string|max:500'],
        'body' => ['Isi berita (Markdown)', 'textarea', 'required|string|max:100000'],
        'author' => ['Penulis', 'text', 'required|string|max:150'],
        'source_url' => ['Tautan sumber resmi (opsional)', 'url', 'nullable|url:http,https|max:1000'],
        'youtube_url' => ['URL video YouTube (opsional, bukan kode iframe)', 'url', 'nullable|url:https|max:1000'],
        'image_alt' => ['Deskripsi gambar untuk aksesibilitas', 'text', 'nullable|string|max:200'],
        'seo_title' => ['Judul SEO', 'text', 'nullable|string|max:70'],
        'seo_description' => ['Deskripsi SEO', 'textarea', 'nullable|string|max:170'],
        'published_at' => ['Waktu terbit (WIB)', 'datetime-local', 'nullable|date|required_if:is_published,1'],
        'is_published' => ['Publikasikan / jadwalkan', 'checkbox', 'boolean'],
    ]],
    'testimonials' => ['label' => 'Testimoni', 'model' => Testimonial::class, 'title' => 'name', 'fields' => [
        'name' => ['Nama jamaah', 'text', 'required|string|max:150'],
        'city' => ['Kota asal', 'text', 'required|string|max:150'],
        'body' => ['Testimoni asli jamaah', 'textarea', 'required|string|max:3000'],
        'rating' => ['Penilaian (1–5)', 'number', 'required|integer|min:1|max:5'],
        'travel_date' => ['Tanggal perjalanan (opsional)', 'date', 'nullable|date|before_or_equal:today'],
        'consent_confirmed' => ['Jamaah telah mengizinkan nama, foto, dan testimoni ditampilkan', 'checkbox', 'boolean|accepted_if:is_published,1'],
        'is_published' => ['Tampilkan di situs', 'checkbox', 'boolean'],
    ]],
    'pages' => ['label' => 'Halaman Informasi', 'model' => Page::class, 'title' => 'title', 'fields' => [
        'title' => ['Judul halaman', 'text', 'required|string|max:180'],
        'slug' => ['URL halaman', 'text', 'required|string|max:180|regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/'],
        'intro' => ['Pengantar', 'textarea', 'nullable|string|max:1000'],
        'body' => ['Isi halaman (Markdown)', 'textarea', 'required|string|max:100000'],
        'seo_title' => ['Judul SEO', 'text', 'nullable|string|max:70'],
        'seo_description' => ['Deskripsi SEO', 'textarea', 'nullable|string|max:170'],
        'is_published' => ['Tampilkan di situs', 'checkbox', 'boolean'],
    ]],
];
