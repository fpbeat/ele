<?php

namespace App\Console\Commands;

use App\Services\MemberProcessor;
use BotMan\BotMan\Exceptions\Core\BadMethodCallException;
use Illuminate\Console\Command;

class KickMembers extends Command
{
    /**
     * @var string
     */
    protected $signature = 'telegram:kick';

    /**
     * @var string
     */
    protected $description = 'Kick out outdated members from channels';

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
    public function handle(): int
    {
        $this->memberProcessor->kickOutdated();

        return 1;
    }
}
