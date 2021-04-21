<?

namespace App\Contracts\Botman;

interface NodeConversationInterface
{
    /**
     * @return void
     */
    public function showPageMessage(): void;

    /**
     * @return array
     */
    public function keyboard(): array;

    /**
     * @return void
     */
    public function run(): void;
}
