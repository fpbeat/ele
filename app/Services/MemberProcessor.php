<?php

namespace App\Services;

use App\Contracts\Botman\CustomRequestInterface;
use App\Facades\Message;
use App\Repositories\MemberRepository;
use BotMan\BotMan\Exceptions\Core\BadMethodCallException;
use Illuminate\Support\Arr;

class MemberProcessor
{
    /**
     * @var MemberRepository
     */
    private MemberRepository $memberRepository;

    /**
     * @var CustomRequestInterface
     */
    private CustomRequestInterface $customRequest;

    /**
     * @param MemberRepository $memberRepository
     * @param CustomRequestInterface $customRequest
     */
    public function __construct(MemberRepository $memberRepository, CustomRequestInterface $customRequest)
    {
        $this->memberRepository = $memberRepository;
        $this->customRequest = $customRequest;
    }

    /**
     * @return void
     * @throws BadMethodCallException
     */
    public function kickOutdated(): void
    {
        foreach ($this->memberRepository->getOutdated() as $member) {
            $channelId = Arr::get($member, 'subscription.channel.channel_id');

            $response = $this->customRequest->request('unbanChatMember', [
                'chat_id' => $channelId,
                'user_id' => $member->user_id
            ]);

            if ($channelId === NULL || $response->isSuccess()) {
                $this->memberRepository->makeInactive($member);
            }
        }
    }

    /**
     * @return void
     * @throws BadMethodCallException
     */
    public function notifyAboveExpired()
    {
        foreach ($this->memberRepository->getAboveExpired() as $member) {
            $response = $this->customRequest->request('sendMessage', [
                'chat_id' => $member->user_id,
                'text' => Message::get('remindOfRenewalMessage')
            ]);

            if ($response->isSuccess()) {
                $this->memberRepository->markExpiredNotifyStatus($member);
            }
        }
    }
}
