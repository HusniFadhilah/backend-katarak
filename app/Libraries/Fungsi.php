<?php

namespace App\Libraries;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as Image;

/**
 * Format response.
 */
class Fungsi
{
    public static function compressImage($image, $path, $intensity = 70, $maxWidth = '', $maxHeight = '')
    {
        $fileExtension   = strtolower($image->getClientOriginalExtension());
        $file_name       = sha1(uniqid() . $image . uniqid()) . '.' . $fileExtension;
        $destinationPath = 'assets/img/' . $path;
        $img = Image::make($image->getRealPath());

        // Cek ukuran gambar
        $width = $img->width();
        $height = $img->height();

        $maxWidth = $maxWidth ?? $width;
        $maxHeight = $maxHeight ?? $height;
        // Periksa jika ukuran gambar melebihi batas maksimal
        if ($width > $maxWidth || $height > $maxHeight) {
            // Tentukan skala penyesuaian berdasarkan sisi terpanjang
            $scale = $width > $height ? $maxWidth / $width : $maxHeight / $height;
            // Resize gambar
            $img->resize($width * $scale, $height * $scale);
        }

        $img->save(storage_path() . '/app/public/' . $destinationPath . $file_name, $intensity);
        return [$image->getClientOriginalName(), $destinationPath . $file_name];
    }

    public static function sweetalert($text, $icon, $title, $href = null)
    {
        if ($href != null) {
            session()->setFlashdata('href', $href);
        }
        session()->flash('text', $text);
        session()->flash('icon', $icon);
        session()->flash('title', $title);
    }

    public static function convertToAlphabet($number)
    {
        $alphabet = range('A', 'Z'); // Array abjad dari A hingga Z

        $result = '';
        while ($number > 0) {
            $remainder = ($number - 1) % 26; // Menghitung sisa pembagian dengan 26
            $result = $alphabet[$remainder] . $result; // Menambahkan abjad ke hasil
            $number = floor(($number - 1) / 26); // Membagi angka dengan 26
        }

        return $result;
    }

    public static function getRoleSession()
    {
        return Str::slug(Auth::user()->role->name ?? '');
    }

    public static function uniqueCode($limit)
    {
        return substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, $limit);
    }

    public static function randomColor()
    {
        return sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    }

    public static function sliceStringByWord($string)
    {
        $url = env('APP_URL', 'http://localhost');
        $path = substr($url, -1) == "/" ? 'storage/' : '/storage/';
        return Str::after($string,  $url . $path);
    }

    public static function sliceStringByParams($string, $url)
    {
        return Str::after($string, $url);
    }

    public static function currency($value, $isSpace = true)
    {
        if ($isSpace)
            return "Rp. " . number_format($value, 0, ',', '.');
        else
            return "Rp." . number_format($value, 0, ',', '.');
    }

    public static function floorDecimal($number, $digits = 2)
    {
        return $number !== null ? number_format((float)$number, $digits, '.', '') : '-';
    }
}
