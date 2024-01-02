<?php

namespace App\Libraries;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\{Auth, File, Log};
use Intervention\Image\ImageManagerStatic as Image;

/**
 * Format response.
 */
class Fungsi
{
    public static function compressImage($image, $path, $intensity = 70, $maxWidth = null, $maxHeight = null)
    {
        // Validate image MIME type
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/octet-stream'];
        $mime = $image->getClientMimeType();

        if (!in_array($mime, $allowedMimeTypes)) {
            // Handle invalid image type
            return null;
        }

        // Generate unique filename
        $fileExtension = strtolower($image->getClientOriginalExtension());
        $file_name = Str::uuid() . '.' . $fileExtension;

        $destinationPath = 'assets/img/' . $path;

        // Check if destination path exists, if not, create it
        if (!File::isDirectory(storage_path('app/public/' . $destinationPath))) {
            File::makeDirectory(storage_path('app/public/' . $destinationPath), 0777, true, true);
        }

        $img = Image::make($image->getRealPath());

        // Check image dimensions and resize if necessary
        $width = $img->width();
        $height = $img->height();

        $maxWidth = $maxWidth ?? $width;
        $maxHeight = $maxHeight ?? $height;

        $aspectRatio = $width / $height;

        if ($aspectRatio > $maxWidth / $maxHeight) {
            $img->resize($maxWidth, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        } else {
            $img->resize(null, $maxHeight, function ($constraint) {
                $constraint->aspectRatio();
            });
        }

        // Save image with intensity
        try {
            $img->save(storage_path('app/public/' . $destinationPath . $file_name), $intensity);
        } catch (Exception $error) {
            Log::channel('command')->info($error);
            return null;
        }
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
