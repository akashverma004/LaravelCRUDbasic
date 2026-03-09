<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveApproved extends Notification implements ShouldQueue
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
            ->subject('Leave Request Approved ✅')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your **' . ucfirst($this->leaveRequest->leave_type) . '** leave request has been **approved**.')
            ->line('📅 ' . $dates)
            ->action('View Details', url('/leaves/' . $this->leaveRequest->id))
            ->line('Enjoy your time off!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Leave Approved ✅',
            'body' => 'Your ' . ucfirst($this->leaveRequest->leave_type) . ' leave has been approved.',
            'icon' => 'check-circle',
            'leave_id' => $this->leaveRequest->id,
            'leave_type' => $this->leaveRequest->leave_type,
            'start_date' => $this->leaveRequest->start_date->toDateString(),
            'end_date' => $this->leaveRequest->end_date->toDateString(),
            'action_url' => '/leaves/' . $this->leaveRequest->id,
        ];
    }
}
