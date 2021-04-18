<?php

namespace App\Repositories;

use App\Models\Member;
use BotMan\Drivers\Telegram\Extensions\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class MemberRepository
{
    /**
     * @var SubscriptionRepository
     */
    private SubscriptionRepository $subscriptionRepository;

    /**
     * @param SubscriptionRepository $subscriptionRepository
     */
    public function __construct(SubscriptionRepository $subscriptionRepository)
    {
        $this->subscriptionRepository = $subscriptionRepository;
    }

    /**
     * @param int $memberId
     * @return Member|null
     */
    public function find(int $memberId): ?Member
    {
        return Member::whereId($memberId)
            ->selectRaw('*')
            ->daysLeft()
            ->firstOrFail();
    }

    public function store(array $data)
    {
        return Member::create([
            'order_no' => $data['order_no'],
            'user_id' => $data['user']->getId(),
            'first_last_name' => $this->formatFirstLastName($data['user']),
            'username' => $data['user']->getUsername(),
            'subscription_id' => $data['subscription_id'],
            'coupon_id' => $data['coupon_id'],
            'invite_link' => $data['invite_link'],
            'active' => Member::STATUS_ACTIVE,
            'up' => now(),
            'till' => $this->getTillADate($data['subscription_id']),
        ]);
    }

    /**
     * @param User $user
     * @return string
     */
    public function formatFirstLastName(User $user): string
    {
        return collect([$user->getLastName(), $user->getFirstName()])
            ->filter()
            ->join(' ');
    }

    /**
     * @param int $subscriptionId
     * @return Carbon
     */
    public function getTillADate(int $subscriptionId): Carbon
    {
        $subscription = $this->subscriptionRepository->find($subscriptionId);

        return now()->addDays($subscription->duration);
    }

    /**
     * @return Collection
     */
    public function getOutdated(): Collection
    {
        return Member::outdated()->get();
    }

    /**
     * @return Collection
     */
    public function getAboveExpired(): Collection
    {
        return Member::aboveExpired()->get();
    }

    /**
     * @param Member $member
     * @return void
     */
    public function makeInactive(Member $member): void
    {
        $member->active = Member::STATUS_INACTIVE;
        $member->save();
    }

    /**
     * @param Member $member
     * @return void
     */
    public function markExpiredNotifyStatus(Member $member): void
    {
        $member->expired_notify_send = Member::STATUS_EXPIRED_NOTIFY_SEND;
        $member->save();
    }
}
