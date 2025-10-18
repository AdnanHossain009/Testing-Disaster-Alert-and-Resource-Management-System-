<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\HelpRequest;

class StatusUpdatedNotification extends Notification
{
    use Queueable;

    public $request;
    public $oldStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(HelpRequest $request, string $oldStatus)
    {
        $this->request = $request;
        $this->oldStatus = $oldStatus;
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
            ->subject('📋 Status Updated: ' . $this->oldStatus . ' → ' . $this->request->status)
            ->view('emails.status-updated', [
                'request' => $this->request,
                'oldStatus' => $this->oldStatus
            ]);
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
            'old_status' => $this->oldStatus,
            'new_status' => $this->request->status,
            'request_type' => $this->request->request_type,
        ];
    }
}
