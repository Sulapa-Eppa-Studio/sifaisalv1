<?php

namespace App\Enums;

enum FileType: string
{
        // Untuk Uang Muka
    case SURAT_PERMOHONAN_PEMBAYARAN_UANG_MUKA = 'surat_permohonan_pembayaran_uang_muka';
    case RINCIAN_PENGGUNAAN_UANG_MUKA = 'rincian_penggunaan_uang_muka';
    case SPTJB = 'sptjb';
    case BA_SERAH_TERIMA_SAKTI = 'ba_serah_terima_sakti';
    case KUITANSI = 'kuitansi';
    case BERITA_ACARA_PEMBAYARAN = 'berita_acara_pembayaran';
    case BUKTI_PAJAK = 'bukti_pajak';
    case JAMINAN_UANG_MUKA = 'jaminan_uang_muka';
    case KARWAS_SAKTI = 'karwas_sakti';
    case SPP = 'spp';
    case RINGKASAN_KONTRAK = 'ringkasan_kontrak';

        // Tanpa Uang Muka
    case KARWAS = 'karwas';
    case BAPP_BAST = 'bapp_bast';
    case BA_SERAH_TERIMA = 'ba_serah_terima';
    case SURAT_PERMOHONAN_PENYEDIA_JASA = 'surat_permohonan_penyedia_jasa';
    case BAP = 'bap';
    case BUKTI_PEMBAYARAN = 'bukti_pembayaran';

    public function label(): string
    {
        return match ($this) {
            self::SURAT_PERMOHONAN_PEMBAYARAN_UANG_MUKA => 'Surat Permohonan Pembayaran Uang Muka',
            self::RINCIAN_PENGGUNAAN_UANG_MUKA => 'Rincian Penggunaan Uang Muka',
            self::SPTJB => 'SPTJB',
            self::BA_SERAH_TERIMA_SAKTI => 'BA Serah Terima SAKTI',
            self::KUITANSI => 'Kuitansi',
            self::BERITA_ACARA_PEMBAYARAN => 'Berita Acara Pembayaran',
            self::BUKTI_PAJAK => 'Bukti Pajak',
            self::JAMINAN_UANG_MUKA => 'Jaminan Uang Muka',
            self::KARWAS_SAKTI => 'Karwas SAKTI',
            self::SPP => 'SPP',
            self::RINGKASAN_KONTRAK => 'Ringkasan Kontrak',
            self::KARWAS => 'Karwas',
            self::BAPP_BAST => 'BAPP / BAST',
            self::BA_SERAH_TERIMA => 'BA Serah Terima',
            self::SURAT_PERMOHONAN_PENYEDIA_JASA => 'Surat Permohonan Penyedia Jasa',
            self::BAP => 'BAP',
            self::BUKTI_PEMBAYARAN => 'Bukti Pembayaran',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::SURAT_PERMOHONAN_PEMBAYARAN_UANG_MUKA => 'Surat resmi yang diajukan untuk permohonan pembayaran uang muka.',
            self::RINCIAN_PENGGUNAAN_UANG_MUKA => 'Dokumen yang berisi rincian penggunaan uang muka yang telah diterima.',
            self::SPTJB => 'Surat Pernyataan Tanggung Jawab Belanja.',
            self::BA_SERAH_TERIMA_SAKTI => 'Berita Acara Serah Terima yang terkait dengan sistem SAKTI.',
            self::KUITANSI => 'Bukti penerimaan pembayaran dalam bentuk kuitansi.',
            self::BERITA_ACARA_PEMBAYARAN => 'Dokumen resmi yang mencatat proses pembayaran yang telah dilakukan.',
            self::BUKTI_PAJAK => 'Dokumen yang menunjukkan bukti pembayaran pajak.',
            self::JAMINAN_UANG_MUKA => 'Dokumen yang menjamin pengembalian uang muka jika syarat tidak terpenuhi.',
            self::KARWAS_SAKTI => 'Kartu Pengawasan proyek yang terkait dengan SAKTI.',
            self::SPP => 'Surat Perintah Pembayaran.',
            self::RINGKASAN_KONTRAK => 'Dokumen ringkasan dari kontrak yang telah disepakati.',
            self::KARWAS => 'Kartu Pengawasan proyek.',
            self::BAPP_BAST => 'Berita Acara Pemeriksaan Pekerjaan / Berita Acara Serah Terima.',
            self::BA_SERAH_TERIMA => 'Berita Acara Serah Terima pekerjaan atau barang.',
            self::SURAT_PERMOHONAN_PENYEDIA_JASA => 'Surat permohonan yang diajukan oleh penyedia jasa.',
            self::BAP => 'Berita Acara Pembayaran.',
            self::BUKTI_PEMBAYARAN => 'Dokumen yang menunjukkan bukti bahwa pembayaran telah dilakukan.',
        };
    }
}
