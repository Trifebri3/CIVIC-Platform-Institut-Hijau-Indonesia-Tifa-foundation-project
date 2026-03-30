<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Cetak Surat - CIVIC Platform</title>
    <style>
        /* Setup Halaman A4 */
        @page {
            margin: 0;
            size: a4;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #1a1a1a;
            line-height: 1.4;
            /* Background Template Surat */
            background-image: url("{{ public_path('images/surat.png') }}");
            background-size: 100% 100%;
            background-repeat: no-repeat;
            width: 210mm;
            height: 297mm;
        }

        .content {
            position: relative;
            width: 100%;
            height: 100%;
        }

        /* Typography Helper */
        .text-bold { font-weight: bold; }
        .italic { font-style: italic; }

        /* --- POSISI PRESISI --- */

        /* Tanggal: Kanan Atas */
        .tanggal-top {
            position: absolute;
            top: 110px;
            right: 75px;
            font-size: 14px;
        }

        /* Metadata: Nomor, Lampiran, Perihal */
        .meta-container {
            position: absolute;
            top: 135px;
            left: 180px;
            font-size: 14px;
            line-height: 1.2;
        }
        .meta-row {
            margin-bottom: 6px; /* Menyesuaikan jarak antar baris titik-titik */
        }

        /* Penerima: Yth. */
        .penerima-section {
            position: absolute;
            top: 260px;
            left: 98px;
            font-size: 14px;
            line-height: 1.5;
        }

/* Agenda: Posisi Tengah Bawah */
.agenda-container {
    position: absolute;
    top: 600px; /* Atur ketinggian baris pertama agar pas di titik pertama */
    left: 330px;
    font-size: 13.5px;
    /* Menggunakan line-height agar jarak antar baris teks konsisten */
    line-height: 0.2;
}

.agenda-row {
    /* Jika line-height di atas dirasa kurang pas, gunakan margin-bottom ini */
    margin-bottom: 18px;
}

        /* Narahubung: Bagian Footer */
        .narahubung-wrapper {
            position: absolute;
            bottom: 327px; /* Menggunakan bottom agar lebih aman jika konten di atas geser */
            left: 98px;
            font-size: 16px;
            font-weight: bold;
            text-transform: lowercase;
        }

    </style>
</head>
<body>
    <div class="content">
        <div class="tanggal-top">Jakarta, {{ $tanggal_indo }}</div>

        <div class="meta-container">
            <div class="meta-row"> {{ $surat->nomor_surat ?? '.../.../...' }}</div>
            <div class="meta-row"> {{ $surat->lampiran ?? '-' }}</div>
            <div class="meta-row"> Undangan kegiatan <span class="text-bold italic">Focus Group Discussion (FGD) di {{ $surat->wilayah_kegiatan }}</span></div>
        </div>

        <div class="penerima-section">
            <div class="text-bold uppercase">{{ $surat->penerima_surat }}</div>
            <div>di Tempat</div>
        </div>

        <div class="agenda-container">
            <div class="agenda-row"> {{ $surat->hari_tanggal }}</div>
            <div class="agenda-row"> {{ $surat->waktu_pelaksanaan }}</div>
            <div class="agenda-row"> {{ $surat->tempat_pelaksanaan }}</div>
        </div>

        <div class="narahubung-wrapper">
            narahubung ({{ $surat->kontak_person }})
        </div>
    </div>
</body>
</html>
