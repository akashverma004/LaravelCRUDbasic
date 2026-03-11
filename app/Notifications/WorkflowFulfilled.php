<?php

namespace App\Notifications;

use App\Models\WorkflowRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkflowFulfilled extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public WorkflowRequest $workflowRequest,
        public string $assetName,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Asset Request Fulfilled')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your asset request "' . $this->workflowRequest->title . '" has been fulfilled.')
            ->line('Assigned asset: ' . $this->assetName)
            ->action('Open Workflow Inbox', url('/workflows'))
            ->line('You can review the request details from the workflow inbox.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Workflow Fulfilled',
            'body' => 'Your asset request "' . $this->workflowRequest->title . '" was fulfilled with ' . $this->assetName . '.',
            'icon' => 'cube',
            'workflow_id' => $this->workflowRequest->id,
            'workflow_type' => $this->workflowRequest->type,
            'asset_name' => $this->assetName,
            'action_url' => '/workflows?workflow=' . $this->workflowRequest->id . '&modal=timeline',
        ];
    }
}
