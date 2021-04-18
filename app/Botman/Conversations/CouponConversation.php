<?php

namespace App\Botman\Conversations;

use Carbon\Carbon;
use App\Facades\Message;
use App\Models\Coupon;
use App\Repositories\CouponRepository;
use BotMan\BotMan\Messages\Incoming\Answer;
use Illuminate\Contracts\Container\BindingResolutionException;
use App\Botman\Traits\{MessageTrait, UserStorage};
use BotMan\Drivers\Telegram\Extensions\{Keyboard, KeyboardButton};

class CouponConversation extends BaseConversation
{
    use MessageTrait, UserStorage;

    /**
     * @var string
     */
    const INTERACTIVE_BUTTON_BACK = 'coupon_back';

    /**
     * @var string
     */
    const INTERACTIVE_BUTTON_CONTINUE = 'coupon_continue';

    /**
     * @return void
     */
    private function askForCoupon(): void
    {
        $this->ask(Message::get('askCouponMessage'), function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $this->deleteLastMessage($answer);
                $this->handleInteractiveAnswer($answer->getValue());
            } else {
                $coupon = app()->make(CouponRepository::class)->find($answer->getText());

                $this->handleCouponCode($coupon);
            }
        }, $this->keyboard());
    }

    /**
     * @param Coupon|null $coupon
     * @throws BindingResolutionException
     */
    private function handleCouponCode(?Coupon $coupon): void
    {
        if ($this->getStorageValue('coupon.locked')) {
            $this->repeat(Message::get('tryIn10MinMessage', [':minutes' => $this->getLockingIntervalDiff()]));

        } elseif ($coupon !== NULL) {
            $this->handleSuccessCoupon($coupon);
        } elseif ($this->getStorageValue('coupon.attempt', 0) >= config('chatbot.coupon.max_attempts')) {
            $this->setStorageValue('coupon', [
                'locked' => TRUE,
                'ttl' => now()->addMinutes(config('chatbot.coupon.locking_interval'))->toIso8601String()
            ]);

            $this->repeat(Message::get('tryIn10MinMessage', [':minutes' => $this->getLockingIntervalDiff(config('chatbot.coupon.locking_interval'))]));
        } else {
            $this->setStorageValue('coupon.attempt', $this->getStorageValue('coupon.attempt', 0) + 1);

            $this->repeat(Message::get('wrongCouponMessage'));
        }
    }

    /**
     * @param Coupon|null $coupon
     * @throws BindingResolutionException
     */
    public function handleSuccessCoupon(?Coupon $coupon): void
    {
        $this->setStorageValue('coupon', [
            'current' => $coupon->id
        ]);

        $discounts = app()->make(CouponRepository::class)->getDiscounts($coupon->id);

        $this->say(Message::get('couponSuccessMessage', [
            ':sale' => $coupon->sale,
            ':channels' => count($discounts['selected']) === 0 ? trans('chatbot.coupon.all') : sprintf('%s %s', implode(', ', $discounts['selected']), trans_choice('chatbot.coupon.channel', count($discounts['selected'])))
        ]));

        $this->bot->typesAndWaits(1);

        $this->bot->startConversation(new SubscriptionConversation);
    }


    /**
     * @param int|null $initial
     * @return string
     */
    public function getLockingIntervalDiff(?int $initial = NULL): string
    {
        $ttl = $this->getStorageValue('coupon.ttl');

        $difference = $initial ?? Carbon::parse($ttl)->diffInMinutes(now());

        return $difference > 0 ? sprintf('%s %s', $difference, trans_choice('chatbot.coupon.minute', $difference)) : trans('chatbot.coupon.less_than_minute');
    }

    /**
     * @return array
     */
    public function keyboard(): array
    {
        return Keyboard::create()->type(Keyboard::TYPE_INLINE)
            ->oneTimeKeyboard(false)
            ->resizeKeyboard()
            ->addRow(KeyboardButton::create(Message::get('continueWithoutCouponButton'))->callbackData(self::INTERACTIVE_BUTTON_CONTINUE))
            ->addRow(KeyboardButton::create(Message::get('backToStartButton'))->callbackData(self::INTERACTIVE_BUTTON_BACK))
            ->toArray();
    }

    /**
     * @param string $value
     * @return void
     */
    private function handleInteractiveAnswer(string $value): void
    {
        switch ($value) {
            case self::INTERACTIVE_BUTTON_BACK:
                $this->bot->startConversation(new PostStartConversation);

                break;
            case self::INTERACTIVE_BUTTON_CONTINUE:
                $this->setStorageValue('coupon.current', null);

                $this->bot->startConversation(new SubscriptionConversation);

                break;
        }
    }

    /**
     * @return void
     */
    public function run(): void
    {
        $this->askForCoupon();
    }
}
