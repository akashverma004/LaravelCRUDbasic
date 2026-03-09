<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public LeaveRequest $leaveRequest,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $dates = $this->leaveRequest->start_date->format('d M Y')
            . ' — ' . $this->leaveRequest->end_date->format('d M Y');

        return (new MailMessage)
            ->subject('Leave Request Rejected')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your **' . ucfirst($this->leaveRequest->leave_type) . '** leave request has been **rejected**.')
            ->line('📅 ' . $dates)
            ->action('View Details', url('/leaves/' . $this->leaveRequest->id))
            ->line('Please contact your manager or HR for further details.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Leave Rejected ❌',
            'body' => 'Your ' . ucfirst($this->leaveRequest->leave_type) . ' leave has been rejected.',
            'icon' => 'x-circle',
            'leave_id' => $this->leaveRequest->id,
            'leave_type' => $this->leaveRequest->leave_type,
            'start_date' => $this->leaveRequest->start_date->toDateString(),
            'end_date' => $this->leaveRequest->end_date->toDateString(),
            'action_url' => '/leaves/' . $this->leaveRequest->id,
        ];
    }
}
