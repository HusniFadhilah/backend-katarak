<?php

namespace App\Libraries;


/**
 * Format response.
 */
class Configuration
{
    public static function getDescription()
    {
        return 'SP3STAB-SIPENAHANA atau Sistem Pencatatan Pengukuran dan Penanganan Sampah yang Timbul Akibat Bencana merupakan sistem yang menangani perencanaan penanganan, pelaksanaan penanganan, pelaporan penanganan sebagai kesatuan manajemen resiko penanganan STAB yang dikemas secara online berbasis web gis';
    }

    public static function getTitle($isFull = true)
    {
        return $isFull ? 'Sistem Pencatatan Pengukuran dan Penanganan Sampah yang Timbul Akibat Bencana' : 'SP3STAB-SIPENAHANA';
    }

    public static function getLogo()
    {
        return asset('assets/img/logo/logo-gabung-bg.png');
    }

    public static function getKeyword()
    {
        return 'SP3STAB-SIPENAHANA (Sistem Pencatatan Pengukuran dan Penanganan Sampah yang Timbul Akibat Bencana)';
    }

    public static function getURL()
    {
        return 'https://sp3stab.doltinuku.id';
    }

    public static function getDocumentDocumentation()
    {
        return [
            'contingency_plan' => [
                ['id' => 'fgd', 'title' => 'Rapat FGD dan Pertemuan Sejenis Lainnya'], ['id' => 'survey', 'title' => 'Survey Estimasi Timbulan dan Karakteristik']
            ],
            'handling_plan' => [
                ['id' => 'fgd', 'title' => 'Rapat FGD dan Pertemuan Sejenis Lainnya'], ['id' => 'survey', 'title' => 'Survey Estimasi Timbulan dan Karakteristik']
            ],
            'handling_implementation' => [
                ['id' => 'pemilahan', 'title' => 'Pemilahan'], ['id' => 'pengangkutan', 'title' => 'Pengangkutan'], ['id' => 'pemanfaatan_kembali', 'title' => 'Pemanfaatan Kembali'], ['id' => 'pengolahan', 'title' => 'Pengolahan'], ['id' => 'pemrosesan_akhir', 'title' => 'Pemprosesan Akhir']
            ]
        ];
    }

    public static function getVllageCategories()
    {
        return array(
            1 => ['label' => 'Desa', 'description' => ''],
            2 => ['label' => 'Desa/Kota', 'description' => ''],
            3 => ['label' => 'Kota Kecil', 'description' => '(20-50 (ribu) Jiwa)'],
            4 => ['label' => 'Kota Sedang', 'description' => '(50-100 (ribu) Jiwa)'],
            5 => ['label' => 'Kota Besar', 'description' => '(100 ribu-1 juta Jiwa)'],
            6 => ['label' => 'Kota Metropolitan', 'description' => '(Lebih dari 1 juta Jiwa)'],
            7 => ['label' => 'Kota Megapolitan', 'description' => '(Lebih dari 5 juta jiwa)']
        );
    }
}
