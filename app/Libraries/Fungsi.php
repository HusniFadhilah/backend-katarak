<?php

namespace App\Libraries;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
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

    public static function getRoleTextUser($user, $isTextOnly = true)
    {
        $roletext = '';
        $roleAlias = $user->role->alias;
        $roletext .= '<div>' . ($user->role_id != null ? $roleAlias : "Guest") . '</div>';
        $roletext .= '<small>Status Akun: </small>';
        if ($user->is_active) {
            $roletext .= ' <span class="badge badge-info">Aktif</span>';
        } else {
            $roletext .= ' <span class="badge badge-light">Tidak Aktif</span>';
        }

        if ($user->is_verified) {
            $roletext .= ' <span class="badge badge-success">Terverifikasi</span>';
        } else {
            $roletext .= ' <span class="badge badge-light">Tidak Terverifikasi</span>';
        }
        return $roletext;
    }

    public static function getFormattedLocation($latitude, $longitude)
    {
        $apiKey = '9e304c7d13924b718d56a78553dd1b01';
        $apiUrl = 'https://api.opencagedata.com/geocode/v1/json';

        $url = "$apiUrl?key=$apiKey&q=$latitude,$longitude&no_annotations=1";
        $response = Http::get($url)->json();
        if ($response !== false) {
            $data = $response;

            $results = $data['results'];
            if (!empty($results)) {
                $formattedLocation = $results[0]['formatted'];
            }
            if ($data['status']['code'] != 200) {
                Log::channel('command')->info('Error get location');
            }
        }

        return $formattedLocation ?? null;
    }

    public static function sendNotification($users, $title = 'You have a new notification', $body = 'This is a body text', $data = [])
    {
        try {
            $serverKey = env('SERVER_KEY_FIREBASE');
            $apiUrl = 'https://fcm.googleapis.com/fcm/send';
            $statuses = [];
            foreach ($users as $user) {
                foreach ($user->tokens()->get() as $personalAccessToken) {
                    $notificationPayload = [
                        'notification' => [
                            'title' => $title,
                            'body' => $body,
                        ],
                        'data' => $data ?? (object)[],
                        'to' => $personalAccessToken->fcm_token
                    ];

                    $client = new \GuzzleHttp\Client();
                    $response = $client->post($apiUrl, [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $serverKey,
                            'Accept' => 'application/json',
                            'Content-Type' => 'application/json',
                        ],
                        'body' => json_encode($notificationPayload),
                    ]);

                    $response = json_decode($response->getBody(), true);
                    array_push($statuses, $response);
                }
            }
            return $statuses;
        } catch (Exception $error) {
            return $error->getMessage();
        }
    }
}
