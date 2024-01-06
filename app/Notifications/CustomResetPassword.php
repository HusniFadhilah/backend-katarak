<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Auth\Notifications\ResetPassword;

class CustomResetPassword extends ResetPassword
{
    use Queueable;
    public $token;
    public $name;


    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token, $name)
    {
        $this->token = $token;
        $this->name = $name;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));
        return (new MailMessage)
            ->greeting('Halo ' . $this->name)
            ->subject('RE: Notifikasi Reset Password')
            ->line('Ini adalah email yang kami berikan ke Anda untuk mereset password akun Anda. Sebelumnya kami telah menerima permintaan penggantian password dari Anda. Silahkan klik tombol di bawah untuk mulai mereset password akun Anda.')
            ->action('Reset Password', url($url))
            ->line('Link reset password ini akan hangus dalam ' . config('auth.passwords.' . config('auth.defaults.passwords') . '.expire') . ' menit.')
            ->line('Abaikan link ini jika Anda tidak ingin mereset password akun Anda');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
