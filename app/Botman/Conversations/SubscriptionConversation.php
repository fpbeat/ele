<?php

namespace App\Botman\Conversations;

use App\Facades\Message;
use App\Repositories\CouponRepository;
use App\Repositories\SubscriptionRepository;
use BotMan\BotMan\Messages\Incoming\Answer;
use Illuminate\Contracts\Container\BindingResolutionException;
use App\Botman\Traits\{MessageTrait, UserStorage};
use BotMan\Drivers\Telegram\Extensions\{Keyboard, KeyboardButton};

class SubscriptionConversation extends BaseConversation
{
    use MessageTrait, UserStorage;

    /**
     * @var string
     */
    const INTERACTIVE_BUTTON_BACK = 'subscription_back';

    /**
     * @return void
     * @throws BindingResolutionException
     */
    private function askSubscription(): void
    {
        $this->ask(Message::get('subscriptionListMessage'), function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                $this->deleteLastMessage($answer);
                $this->handleInteractiveAnswer($answer->getValue());
            }
        }, $this->keyboard());
    }

    /**
     * @return array
     * @return array
     * @throws BindingResolutionException
     */
    public function keyboard(): array
    {
        $subscription = app()->make(SubscriptionRepository::class)->all();

        $keyboard = Keyboard::create()->type(Keyboard::TYPE_INLINE)
            ->oneTimeKeyboard(true)
            ->resizeKeyboard();

        $subscription
            ->map(function ($single) {
                return KeyboardButton::create($this->getSubscriptionButtonName($single))->callbackData($single->id);
            })
            ->split(ceil(count($subscription) / 2))
            ->each(function ($single) use ($keyboard) {
                $keyboard->addRow(...$single);
            });

        $keyboard->addRow(KeyboardButton::create(Message::get('backToCouponButton'))->callbackData(self::INTERACTIVE_BUTTON_BACK));

        return $keyboard->toArray();
    }

    public function getSubscriptionButtonName($subscription)
    {
        $coupon = $this->getStorageValue('coupon.current');

        $discounts = app()->make(CouponRepository::class)->getDiscounts($coupon);

        return Message::get('subscriptionButtonText', [
            ':name' => $subscription->name,
            ':price' => in_array($subscription->id, $discounts['subscriptions']) ? $subscription->getPriceWithDiscount($discounts['sale']) : $subscription->price
        ]);
    }

    /**
     * @param string $value
     * @return void
     */
    private function handleInteractiveAnswer(string $value): void
    {
        switch ($value) {
            case self::INTERACTIVE_BUTTON_BACK:
                $this->bot->startConversation(new CouponConversation);

                break;
            default:
                $this->setStorageValue('subscription.current', $value);

                $this->bot->startConversation(new FakePaymentConversation);

//                $this->bot->sendRequest('sendInvoice', [
//                    'chat_id' => $this->bot->getMessage()->getPayload()['chat']['id'],
//                    'title' => 'Product name, 1-32 characters',
//                    'description' => 'Product description, 1-255 characters',
//                    'payload' => 'test_string',
//                    'provider_token' => '410694247:TEST:bc439931-796e-4162-a61b-00ce50765e40',
//                    'start_parameter' => 'test_what',
//                    'currency' => 'USD',
//                    'prices' => json_encode([
//                        ['label' => 'Nike Shoes', 'amount' => 6600]
//                    ]),
//                ]);
        }
    }

    /**
     * @throws BindingResolutionException
     */
    public function run(): void
    {
        $this->askSubscription();
    }
}
