<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DefaultNotification extends Notification
{
    use Queueable;
    protected $user;
    protected $subject;
    protected $message;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $subject, $message)
    {
        $this->user = $user;
        $this->subject = $subject;
        $this->message = $message;
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
        return (new MailMessage)
            ->from('noreply@doltinuku.id', 'Deteksi Dini Katarak')
            ->line($this->message)
            ->greeting('Halo ' . $this->user->name)
            ->subject($this->subject)
            ->line('Terimakasih telah menggunakan layanan kami!');
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
