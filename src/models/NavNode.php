<?php

namespace viget\base\models;

class NavNode implements \JsonSerializable
{
    public function __construct(
        public string $title,
        public string $path,
        public ?string $url = null,
        public array  $children = [],
    )
    {}

    public function jsonSerialize(): array
    {
        // Exclude path from JSON
        return [
            'title' => $this->title,
            'url' => $this->url,
            'children' => $this->children,
        ];
    }
}
