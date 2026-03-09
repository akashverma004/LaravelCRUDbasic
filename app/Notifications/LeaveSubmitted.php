<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public LeaveRequest $leaveRequest,
        public string $employeeName,
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
            ->subject('New Leave Request from ' . $this->employeeName)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($this->employeeName . ' has submitted a **' . ucfirst($this->leaveRequest->leave_type) . '** leave request.')
            ->line('📅 ' . $dates)
            ->action('Review Request', url('/leaves/pending'))
            ->line('Please review and take action.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New Leave Request',
            'body' => $this->employeeName . ' submitted a ' . ucfirst($this->leaveRequest->leave_type) . ' leave request.',
            'icon' => 'calendar',
            'leave_id' => $this->leaveRequest->id,
            'employee_name' => $this->employeeName,
            'leave_type' => $this->leaveRequest->leave_type,
            'start_date' => $this->leaveRequest->start_date->toDateString(),
            'end_date' => $this->leaveRequest->end_date->toDateString(),
            'action_url' => '/leaves/pending',
        ];
    }
}
