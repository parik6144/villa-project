<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AdminNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $message;
    private $subject;

    public function __construct($message, $subject = 'Default Subject')
    {
        $this->message = $message;
        $this->subject = $subject;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject($this->subject)
                    ->line($this->message)
                    ->action('View Dashboard', url('/admin'));
    }

    public function sendAdminNotification($adminEmail, $message, $subject)
    {
        $to = $adminEmail;
        $fromAddress = "admin@booking.dits.md";
        // $headers = "From: " . env('MAIL_FROM_ADDRESS') . "\r\n";
        // $headers .= "Reply-To: " . env('MAIL_FROM_ADDRESS') . "\r\n";
        $headers = "From: " . $fromAddress . "\r\n";
        $headers .= "Reply-To: " . $fromAddress . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        $body = $message;

        try {
            if (filter_var($to, FILTER_VALIDATE_EMAIL) && filter_var($fromAddress, FILTER_VALIDATE_EMAIL)) {
                if (mail($to, $subject, $body, $headers)) {
                    return true;
                } else {
                    \Log::error("Mail sending failed due to unknown reasons.");
                    return false;
                }
            } else {
                throw new \Exception("Invalid email address in 'to' or 'from' fields.");
            }
        } catch (\Exception $e) {
            \Log::error('Mail send error: ' . $e->getMessage());
            return false;
        }
    }

    public function send()
    {
        return $this->sendAdminNotification('admin@booking.dits.md', $this->message, $this->subject);
    }
}
