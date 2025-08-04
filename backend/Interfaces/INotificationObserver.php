<?php
namespace Interfaces;

interface INotificationObserver {
    public function notifier(string $message): void;
}
