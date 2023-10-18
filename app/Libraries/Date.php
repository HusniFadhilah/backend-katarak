<?php

namespace App\Libraries;

use Illuminate\Support\Str;
use Carbon\Carbon;

/**
 * Format response.
 */
class Date
{
    public static function hari($date)
    {
        $day = date('D', strtotime($date));
        $dayList = array(
            'Sun' => 'Minggu',
            'Mon' => 'Senin',
            'Tue' => 'Selasa',
            'Wed' => 'Rabu',
            'Thu' => 'Kamis',
            'Fri' => 'Jumat',
            'Sat' => 'Sabtu'
        );
        return $dayList[$day];
    }

    public static function pukul($date)
    {
        $d = substr($date, 11, 5);
        return $d;
    }

    public static function tanggal($date)
    {
        $d = substr($date, 8, 2);
        return $d;
    }

    public static function bulan($date, $isFull = true)
    {
        $m = substr($date, 5, 2);
        return self::bulanDariAngka($m, $isFull);
    }

    public static function bulanDariAngka($angka, $isFull = true)
    {
        switch ($angka) {
            case 1:
                return $isFull ? "Januari" : "Jan";
            case 2:
                return $isFull ? "Februari" : "Feb";
            case 3:
                return $isFull ? "Maret" : "Mar";
            case 4:
                return $isFull ? "April" : "Apr";
            case 5:
                return "Mei";
            case 6:
                return $isFull ? "Juni" : "Jun";
            case 7:
                return $isFull ? "Juli" : "Jul";
            case 8:
                return $isFull ? "Agustus" : "Agu";
            case 9:
                return $isFull ? "September" : "Sep";
            case 10:
                return $isFull ? "Oktober" : "Okt";
            case 11:
                return $isFull ? "November" : "Nov";
            case 12:
                return $isFull ? "Desember" : "Des";
        }
    }

    public static function month($date, $isFull = true)
    {
        $m = substr($date, 5, 2);
        switch ($m) {
            case 1:
                return $isFull ? "January" : "Jan";
            case 2:
                return $isFull ? "February" : "Feb";
            case 3:
                return $isFull ? "March" : "Mar";
            case 4:
                return $isFull ? "April" : "Apr";
            case 5:
                return "May";
            case 6:
                return $isFull ? "June" : "Jun";
            case 7:
                return $isFull ? "July" : "Jul";
            case 8:
                return $isFull ? "Augustus" : "Aug";
            case 9:
                return $isFull ? "September" : "Sep";
            case 10:
                return $isFull ? "October" : "Okt";
            case 11:
                return $isFull ? "November" : "Nov";
            case 12:
                return $isFull ? "December" : "Dec";
        }
    }

    public static function bulanAngka($date)
    {
        $y = substr($date, 5, 2);
        return $y;
    }

    public static function tahun($date)
    {
        $y = substr($date, 0, 4);
        return $y;
    }

    public static function tglIndo($date)
    {
        $d = self::tanggal($date);
        $m = self::bulan($date);
        $y = self::tahun($date);
        return $d . " " . $m . " " . $y;
    }

    public static function indoDate($date)
    {
        $d = self::tanggal($date);
        $m = self::month($date);
        $y = self::tahun($date);
        return $d . " " . $m . " " . $y;
    }

    public static function bulanTahun($date)
    {
        return self::bulan($date) . ' ' . self::tahun($date);
    }

    public static function tglWaktu($date)
    {
        return self::tglIndo($date) . ' pukul ' . self::pukul($date);
    }

    public static function hariTglWaktu($date)
    {
        return self::hari($date) . ', ' . self::tglIndo($date) . ' pukul ' . self::pukul($date);
    }

    public static function hariTgl($date)
    {
        return self::hari($date) . ', ' . self::tglIndo($date);
    }

    public static function tglDefault($date)
    {
        $d = self::tanggal($date);
        $m = self::bulanAngka($date);
        $y = self::tahun($date);
        return $d . "/" . $m . "/" . $y;
    }
}
