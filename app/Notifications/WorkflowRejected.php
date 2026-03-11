<?php

namespace App\Notifications;

use App\Models\WorkflowRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkflowRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public WorkflowRequest $workflowRequest,
        public ?string $comment = null,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Workflow Request Rejected')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your workflow request "' . $this->workflowRequest->title . '" has been rejected.');

        if ($this->comment) {
            $mail->line('Reviewer note: ' . $this->comment);
        }

        return $mail
            ->action('Open Workflow Inbox', url('/workflows'))
            ->line('You can review the full decision history from the workflow inbox.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Workflow Rejected',
            'body' => 'Your request "' . $this->workflowRequest->title . '" has been rejected.',
            'icon' => 'x-circle',
            'workflow_id' => $this->workflowRequest->id,
            'workflow_type' => $this->workflowRequest->type,
            'comment' => $this->comment,
            'action_url' => '/workflows?workflow=' . $this->workflowRequest->id . '&action=resubmit',
        ];
    }
}
