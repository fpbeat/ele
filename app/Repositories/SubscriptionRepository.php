<?php

namespace App\Repositories;

use App\Models\Subscription;

class SubscriptionRepository
{
    public function all()
    {
        return Subscription::all();
    }

    public function find($subscriptionId)
    {
        return Subscription::where('id', $subscriptionId)->firstOrFail();
    }
}
