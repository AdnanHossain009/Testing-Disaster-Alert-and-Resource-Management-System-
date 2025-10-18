<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\HelpRequest;

class RequestSubmittedNotification extends Notification
{
    use Queueable;

    public $request;

    /**
     * Create a new notification instance.
     */
    public function __construct(HelpRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('✅ Request Submitted Successfully - #' . $this->request->id)
            ->view('emails.request-submitted', ['request' => $this->request]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'request_id' => $this->request->id,
            'request_type' => $this->request->request_type,
            'urgency_level' => $this->request->urgency_level,
            'location' => $this->request->location,
        ];
    }
}
