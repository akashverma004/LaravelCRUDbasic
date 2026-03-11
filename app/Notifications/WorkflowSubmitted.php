<?php

namespace App\Notifications;

use App\Models\WorkflowRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkflowSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public WorkflowRequest $workflowRequest,
        public string $requesterName,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Workflow Request: ' . $this->workflowRequest->title)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($this->requesterName . ' submitted a new ' . ($this->workflowRequest->type_label ?? ucfirst($this->workflowRequest->type)) . ' request.')
            ->line('Title: ' . $this->workflowRequest->title)
            ->action('Review Request', url('/workflows'))
            ->line('Open the workflow inbox to review and take action.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'New Workflow Request',
            'body' => $this->requesterName . ' submitted "' . $this->workflowRequest->title . '".',
            'icon' => 'clipboard-list',
            'workflow_id' => $this->workflowRequest->id,
            'workflow_type' => $this->workflowRequest->type,
            'requester_name' => $this->requesterName,
            'action_url' => '/workflows?workflow=' . $this->workflowRequest->id . '&modal=timeline',
        ];
    }
}
