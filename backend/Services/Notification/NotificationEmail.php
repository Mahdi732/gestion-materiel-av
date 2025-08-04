<?php
namespace Services\Notification;

use Interfaces\INotificationObserver;

class NotificationEmail implements INotificationObserver {
    public function notifier(string $message): void {
        echo "Notification email envoyée: $message\n";
    }
}
