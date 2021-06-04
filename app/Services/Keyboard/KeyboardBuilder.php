<?php

namespace App\Services\Keyboard;

use Illuminate\Support\Collection;

class KeyboardBuilder
{
    /**
     * @var Collection
     */
    private Collection $buttons;

    /**
     * @param Collection $collection
     */
    public function __construct(Collection $collection)
    {
        $this->buttons = $collection->map(fn($item) => InlineButton::create($item->name)->pageId($item->id));
    }

    /**
     * @return static
     */
    static public function create(): self
    {
        return new self(collect());
    }

    /**
     * @param Collection $collection
     * @return static
     */
    static public function fromCollection(Collection $collection): self
    {
        return new self($collection);
    }

    /**
     * @param InlineButton $button
     * @return $this
     */
    public function add(InlineButton $button): self
    {
        $this->buttons->push($button);

        return $this;
    }

    /**
     * @param callable $callback
     * @return $this
     */
    public function each(callable $callback): self
    {
        $this->buttons->each($callback);

        return $this;
    }

    /**
     * @return Collection
     */
    public function all(): Collection
    {
        return $this->buttons->map(fn($item) => $item->toArray());
    }
}
