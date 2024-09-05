<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Orchid\Platform\Notifications\DashboardChannel;
use Orchid\Platform\Notifications\DashboardMessage;
use Orchid\Support\Color;

class DashboardMessageNotification extends Notification
{
    use Queueable;

    private DashboardMessage $dashboardMessage;

    public function __construct(DashboardMessage $dashboardMessage)
    {
        $this->dashboardMessage = $dashboardMessage;
    }

    public function via($notifiable)
    {
        return [DashboardChannel::class];
    }

    public function toDashboard($notifiable)
    {
        return $this->dashboardMessage;
    }
}
