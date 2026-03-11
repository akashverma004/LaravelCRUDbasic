<?php

namespace App\Notifications;

use App\Models\WorkflowRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkflowApproved extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public WorkflowRequest $workflowRequest,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Workflow Request Approved')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your workflow request "' . $this->workflowRequest->title . '" has been approved.')
            ->action('Open Workflow Inbox', url('/workflows'))
            ->line('You can review the decision history from the workflow inbox.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Workflow Approved',
            'body' => 'Your request "' . $this->workflowRequest->title . '" has been approved.',
            'icon' => 'check-circle',
            'workflow_id' => $this->workflowRequest->id,
            'workflow_type' => $this->workflowRequest->type,
            'action_url' => '/workflows?workflow=' . $this->workflowRequest->id . '&modal=timeline',
        ];
    }
}
