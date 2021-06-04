<?php

namespace App\Services\Keyboard;

use Illuminate\Contracts\Support\Arrayable;

class InlineButton implements Arrayable
{
    /**
     * @var string
     */
    const BUTTON_TYPE_INTERNAL = 'internal';

    /**
     * @var string
     */
    const BUTTON_TYPE_EXTERNAL = 'external_link';

    /**
     * @var string
     */
    private string $type = self::BUTTON_TYPE_INTERNAL;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var int
     */
    private int $pageId;

    /**
     * @param string|null $name
     * @return static
     */
    static public function create(?string $name = null): self
    {
        return resolve(static::class, [
            'name' => $name
        ]);
    }

    /**
     * @param string|null $name
     */
    public function __construct(?string $name = null)
    {
        $this->name($name);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param int $pageId
     * @return $this
     */
    public function pageId(int $pageId): self
    {
        $this->pageId = $pageId;

        return $this;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function type(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'type' => $this->type,
            'page_id' => $this->pageId
        ];
    }
}
