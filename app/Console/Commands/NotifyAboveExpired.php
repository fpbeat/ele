<?php

namespace App\Console\Commands;

use App\Services\MemberProcessor;
use BotMan\BotMan\Exceptions\Core\BadMethodCallException;
use Illuminate\Console\Command;

class NotifyAboveExpired extends Command
{
    /**
     * @var string
     */
    protected $signature = 'telegram:notify';

    /**
     * @var string
     */
    protected $description = 'Notify members whose subscriptions is above to expired';

    /**
     * @var MemberProcessor
     */
    private MemberProcessor $memberProcessor;

    /**
     * @param MemberProcessor $memberProcessor
     */
    public function __construct(MemberProcessor $memberProcessor)
    {
        parent::__construct();

        $this->memberProcessor = $memberProcessor;
    }

    /**
     * @return int
     * @throws BadMethodCallException
     */
    public function handle(): bool
    {
        $this->memberProcessor->notifyAboveExpired();

        return 1;
    }
}
