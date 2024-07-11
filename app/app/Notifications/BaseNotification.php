<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Orchid\Platform\Notifications\DashboardChannel;
use Orchid\Platform\Notifications\DashboardMessage;
use Orchid\Support\Color;

class BaseNotification extends Notification
{
    use Queueable;

    private string $title;
    private string $message;
    private Color $type;

    public function __construct(string $title, string $message, Color $type)
    {
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
    }

    public function via($notifiable)
    {
        return [DashboardChannel::class];
    }

    public function toDashboard($notifiable)
    {
        return (new DashboardMessage())
            ->title($this->title)
            ->message($this->message)
            ->type($this->type);
    }
}
