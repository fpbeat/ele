<?php

namespace App\Botman\Conversations;

use App\Contracts\Botman\CustomRequestInterface;
use App\Repositories\{CouponRepository, MemberRepository, PaymentRepository, SubscriptionRepository};
use BotMan\BotMan\Exceptions\Core\BadMethodCallException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\{Arr, Facades\DB};
use App\Botman\Traits\{MessageTrait, UserStorage};
use App\Facades\Message;
use BotMan\Drivers\Telegram\Extensions\{Keyboard, KeyboardButton};

class PostPaymentConversation extends BaseConversation
{
    use MessageTrait, UserStorage;

    /**
     * @var string
     */
    public string $inviteLink;

    /**
     * @throws \Throwable
     */
    private function saySuccessMessage()
    {
        $this->store();
        $this->say(Message::get('joinChannelMessage'), $this->keyboard());
    }

    /**
     * @return array
     */
    private function keyboard(): array
    {
        return Keyboard::create()->type(Keyboard::TYPE_INLINE)
            ->oneTimeKeyboard(false)
            ->resizeKeyboard()
            ->addRow(
                KeyboardButton::create(Message::get('joinChannelButton'))->url($this->inviteLink ?? ''),
            )
            ->toArray();
    }

    /**
     * @return string
     * @throws BindingResolutionException
     * @throws BadMethodCallException
     */
    private function getInviteLink(): string
    {
        $subscription = app()->make(SubscriptionRepository::class)->find($this->getStorageValue('subscription.current'));

        $response = app()->make(CustomRequestInterface::class)->request('createChatInviteLink', [
            'chat_id' => Arr::get($subscription, 'channel.channel_id'),
            'expire_date' => now()->addDays($subscription->duration)->timestamp,
            'member_limit' => 1
        ]);

        if ($response->isSuccess()) {
            return Arr::get($response->getContent(), 'result.invite_link');
        }

        throw new \RuntimeException('Error creating invite link');
    }

    /**
     * @throws \Throwable
     */
    private function store(): void
    {
        DB::transaction(function () {
            $member = app()->make(MemberRepository::class)->store([
                'order_no' => $this->getStorageValue('order.number'),
                'subscription_id' => $this->getStorageValue('subscription.current'),
                'coupon_id' => $this->getStorageValue('coupon.current'),
                'invite_link' => $this->inviteLink,
                'user' => $this->bot->getUser()
            ]);

            app()->make(PaymentRepository::class)->store([
                'amount' => $this->getSubscriptionFinalPrice(),
                'member_id' => $member->id
            ]);
        });
    }

    /**
     * @return float
     * @throws BindingResolutionException
     */
    private function getSubscriptionFinalPrice(): float
    {
        $subscription = $this->getStorageValue('subscription.current');
        $coupon = $this->getStorageValue('coupon.current');

        $subscription = app()->make(SubscriptionRepository::class)->find($subscription);
        $discounts = app()->make(CouponRepository::class)->getDiscounts($coupon);

        return $subscription->getPriceWithDiscount($discounts['sale']);
    }

    /**
     * @throws BadMethodCallException
     * @throws BindingResolutionException
     * @throws \Throwable
     */
    public function run(): void
    {
        $this->inviteLink = $this->getInviteLink();

        $this->saySuccessMessage();
    }
}
