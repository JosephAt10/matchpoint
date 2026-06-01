<?php

return [
    'nav' => [
        'contact' => 'Hubungi Kami',
        'help' => 'Bantuan / FAQ',
        'how' => 'Cara Kerja',
        'terms' => 'Syarat & Ketentuan',
        'privacy' => 'Kebijakan Privasi',
    ],
    'common' => [
        'back_home' => 'Kembali ke beranda',
        'support' => 'Dukungan',
    ],
    'contact' => [
        'title' => 'Hubungi Kami',
        'subtitle' => 'Butuh bantuan terkait booking, pertandingan publik, atau akun pemilik lapangan? Gunakan detail dukungan berikut untuk menghubungi tim MatchPoint.',
        'channels' => [
            ['label' => 'Email', 'value' => 'support@matchpoint.com', 'description' => 'Cocok untuk pertanyaan akses akun, masalah booking, dan bantuan yang membutuhkan catatan tertulis.'],
            ['label' => 'Telepon', 'value' => '+62 812 1727 5362', 'description' => 'Gunakan untuk pertanyaan booking yang mendesak selama jam dukungan.'],
            ['label' => 'Lokasi', 'value' => 'Malang, Indonesia', 'description' => 'MatchPoint saat ini mendukung booking venue olahraga di Malang dan area sekitarnya.'],
        ],
        'before_contact_title' => 'Sebelum menghubungi dukungan',
        'before_contact' => [
            ['title' => 'Masalah booking', 'body' => 'Siapkan tanggal booking, nama venue, slot waktu yang dipilih, dan status booking saat ini.'],
            ['title' => 'Pertanyaan pemilik lapangan', 'body' => 'Sertakan email akun dan nama lapangan agar admin dapat meninjau persetujuan atau masalah visibilitas lapangan.'],
        ],
    ],
    'help' => [
        'title' => 'Bantuan / FAQ',
        'subtitle' => 'Jawaban cepat untuk pengunjung, pemain, dan pemilik lapangan yang menggunakan MatchPoint.',
        'faq_title' => 'Pertanyaan yang Sering Diajukan',
        'faqs' => [
            ['question' => 'Apakah saya perlu akun untuk melihat lapangan?', 'answer' => 'Tidak. Pengunjung dapat melihat lapangan yang disetujui dan pertandingan publik, tetapi booking lapangan atau bergabung dengan pertandingan membutuhkan login.'],
            ['question' => 'Bagaimana booking lapangan dikonfirmasi?', 'answer' => 'Setelah memilih lapangan dan slot waktu, booking masuk ke alur peninjauan. Pemilik lapangan memeriksa detail booking lalu mengonfirmasi atau menolaknya.'],
            ['question' => 'Mengapa booking saya masih pending?', 'answer' => 'Booking tetap pending sampai pemilik lapangan meninjau dan mengonfirmasinya.'],
            ['question' => 'Bisakah saya menjadwalkan ulang booking?', 'answer' => 'Hanya booking outdoor yang sudah dikonfirmasi yang dapat dijadwalkan ulang, dan permintaan harus dibuat sebelum tanggal booking. Booking indoor tidak dapat dijadwalkan ulang.'],
            ['question' => 'Bagaimana cara kerja pertandingan publik?', 'answer' => 'Pengguna dengan booking terkonfirmasi dapat membuat pertandingan publik, mengatur tim dan detail peserta, lalu pengguna lain dapat meminta slot.'],
            ['question' => 'Siapa yang menyetujui akun pemilik lapangan?', 'answer' => 'Admin meninjau pendaftaran pemilik lapangan. Pemilik lapangan harus aktif sebelum lapangannya tampil secara publik.'],
        ],
        'need_help_title' => 'Masih butuh bantuan?',
        'need_help_body' => 'Halaman Kontak tersedia untuk semua orang, bahkan saat belum login.',
    ],
    'how' => [
        'title' => 'Cara Kerja',
        'subtitle' => 'Ringkasan alur utama MatchPoint dari melihat lapangan sampai bergabung dengan pertandingan publik.',
        'steps' => [
            ['eyebrow' => 'Langkah 1', 'title' => 'Jelajahi lapangan', 'body' => 'Cari venue yang disetujui berdasarkan olahraga, lokasi, ketersediaan, dan harga sebelum membuka halaman detail lapangan.'],
            ['eyebrow' => 'Langkah 2', 'title' => 'Booking slot waktu', 'body' => 'Pilih tanggal dan slot waktu yang tersedia, lalu periksa ringkasan booking sebelum mengirim booking.'],
            ['eyebrow' => 'Langkah 3', 'title' => 'Tunggu konfirmasi', 'body' => 'Pemilik lapangan meninjau booking Anda. Booking yang dikonfirmasi menjadi aktif dan tampil di dasbor Anda.'],
            ['eyebrow' => 'Langkah 4', 'title' => 'Buat atau gabung pertandingan', 'body' => 'Booking terkonfirmasi dapat dijadikan pertandingan publik. Pemain lain dapat bergabung dengan memilih tim dan mengirim permintaan bergabung.'],
        ],
    ],
    'terms' => [
        'title' => 'Syarat & Ketentuan',
        'subtitle' => 'Ketentuan ini menjelaskan tanggung jawab dasar dalam menggunakan fitur booking dan pertandingan publik MatchPoint.',
        'sections' => [
            ['title' => 'Tanggung jawab akun', 'body' => 'Pengguna harus memberikan informasi yang akurat dan menjaga kredensial akun tetap aman. Pemilik lapangan bertanggung jawab menjaga detail lapangan, harga, dan jadwal tetap akurat.'],
            ['title' => 'Pembayaran manual', 'body' => 'MatchPoint mendukung peninjauan pembayaran manual di luar sistem. Pengguna, pemilik lapangan, dan admin bertanggung jawab mengikuti instruksi pembayaran yang disepakati.'],
            ['title' => 'Status booking', 'body' => 'Booking dapat berstatus pending, terkonfirmasi, selesai, atau dibatalkan. Booking terkonfirmasi otomatis menjadi selesai setelah tanggal dan waktu jadwal lewat saat scheduler berjalan.'],
            ['title' => 'Reschedule dan pembatalan', 'body' => 'Hanya booking outdoor terkonfirmasi yang dapat dijadwalkan ulang sebelum tanggal booking. Booking pending dapat dibatalkan sesuai aturan platform. Pengembalian dana dilakukan di luar MatchPoint.'],
            ['title' => 'Pertandingan publik', 'body' => 'Pembuat pertandingan bertanggung jawab atas detail pertandingan, pengaturan tim, dan persetujuan peserta.'],
        ],
    ],
    'privacy' => [
        'title' => 'Kebijakan Privasi',
        'subtitle' => 'Halaman ini menjelaskan data yang digunakan MatchPoint untuk menyediakan fitur booking, notifikasi, dan pertandingan publik.',
        'sections' => [
            ['title' => 'Informasi yang dikumpulkan', 'body' => 'MatchPoint menyimpan detail akun, catatan booking, slot waktu yang dipilih, notifikasi, dan catatan partisipasi pertandingan publik.'],
            ['title' => 'Cara data digunakan', 'body' => 'Data digunakan untuk mengelola booking, memberi notifikasi kepada pengguna dan pemilik lapangan, menyetujui akun, dan menampilkan ketersediaan pertandingan publik.'],
            ['title' => 'Siapa yang dapat melihat informasi booking', 'body' => 'Informasi booking hanya terlihat oleh pengguna dengan peran yang sesuai, seperti pengguna booking, pemilik lapangan terkait, atau admin.'],
            ['title' => 'Notifikasi dan audit log', 'body' => 'Sistem menyimpan notifikasi dan audit log agar peristiwa penting terkait booking, pembayaran, akun, dan pertandingan dapat ditinjau.'],
            ['title' => 'Perlindungan data', 'body' => 'Pengguna sebaiknya tidak mengirim informasi pribadi yang tidak terkait. MatchPoint menyimpan catatan akun dan aktivitas hanya untuk operasi dan peninjauan platform.'],
        ],
    ],
];
