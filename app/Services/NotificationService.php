<?php

namespace App\Services;

use App\Facades\Reflection;
use Illuminate\Contracts\Container\BindingResolutionException;
use App\Notifications\{SendOrder, SendProposal, SendReview};
use Illuminate\Support\Facades\Notification;
use App\Facades\Setting;

class NotificationService
{
    /**
     * @var array|string[]
     */
    protected array $eventsMapping = [
        'review' => SendReview::class,
        'proposal' => SendProposal::class,
        'order' => SendOrder::class
    ];

    /**
     * @param $notification
     * @param ...$params
     * @throws BindingResolutionException
     */
    public function send($notification, ...$params): void
    {
        Notification::send($this->getSubscribers(), app()->make(
            $this->getMappingClassName($notification),
            array_combine(Reflection::getInstantiableClassParameters($this->getMappingClassName($notification)), $params)
        ));
    }

    /**
     * @param $notification
     * @param mixed ...$params
     * @throws BindingResolutionException
     */
    public function sendIfEnabled($notification, ...$params): void
    {
        if ($this->isEnabled($notification)) {

            $this->send(...func_get_args());
        }
    }

    /**
     * @param string $notification
     * @return bool
     */
    protected function isEnabled(string $notification): bool
    {
        return Setting::get('notifications.events', [])
            ->contains($notification);
    }

    /**
     * @param string $name
     * @return string
     */
    protected function getMappingClassName(string $name): string
    {
        return $this->eventsMapping[$name] ?? '';
    }

    /**
     * @return array
     */
    protected function getSubscribers(): array
    {
        return Setting::get('notifications.groups', [])
            ->pluck('id')
            ->toArray();
    }
}
