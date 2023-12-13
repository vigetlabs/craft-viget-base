<?php

namespace viget\base\models;

class NavNode implements \JsonSerializable
{
    public function __construct(
        public string $title,
        public string $url,
        public string $path,
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
