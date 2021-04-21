<?php

namespace App\Botman\Conversations;

use App\Botman\Conversations\Basics\NodeConversation;
use App\Contracts\Botman\NodeConversationInterface;
use App\Facades\Setting;
use BotMan\BotMan\Messages\Attachments\Location;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;
use Illuminate\Support\Arr;

class ContactsConversation extends NodeConversation implements NodeConversationInterface
{
    /**
     * @var bool
     */
    const IMAGE_SINGLY = true;

    /**
     * @var array|null
     */
    private ?array $coordinates = null;

    /**
     * @return void
     */
    public function showPageMessage(): void
    {
        $this->sendLocation();

        parent::showPageMessage();
    }

    /**
     * @return void
     */
    private function prepareCoordinates(): void
    {
        $address = Setting::get('contact.address');

        if ($address) {
            $address = json_decode($address, true);

            $this->coordinates = collect(Arr::get($address, 'latlng'))
                ->values()
                ->toArray();
        }
    }

    /**
     * @return void
     */
    private function sendLocation(): void
    {
        if ($this->coordinates) {
            $attachment = new Location(...$this->coordinates);
            $this->say(OutgoingMessage::create()->withAttachment($attachment));
        }
    }

    /**
     * @return void
     */
    public function run(): void
    {
        parent::run();

        $this->prepareCoordinates();
        $this->showPageMessage();
    }
}
