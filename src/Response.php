<?php

namespace ImgBB;

class Response
{
    public readonly array $data;

    public function __construct(array $response)
    {
        $this->data = $response['data'] ?? [];
    }

    public function id(): ?string
    {
        return $this->data['id'] ?? null;
    }

    public function url(): ?string
    {
        return $this->data['url'] ?? null;
    }

    public function displayUrl(): ?string
    {
        return $this->data['display_url'] ?? null;
    }

    public function deleteUrl(): ?string
    {
        return $this->data['delete_url'] ?? null;
    }

    public function thumbnail(): ?string
    {
        return $this->data['thumb']['url'] ?? null;
    }

    public function medium(): ?string
    {
        return $this->data['medium']['url'] ?? null;
    }

    public function originalFilename(): ?string
    {
        return $this->data['image']['filename'] ?? null;
    }

    public function name(): ?string
    {
        return $this->data['image']['name'] ?? null;
    }

    public function mime(): ?string
    {
        return $this->data['image']['mime'] ?? null;
    }

    public function extension(): ?string
    {
        return $this->data['image']['extension'] ?? null;
    }

    public function width(): ?int
    {
        return isset($this->data['width'])
            ? (int) $this->data['width']
            : null;
    }

    public function height(): ?int
    {
        return isset($this->data['height'])
            ? (int) $this->data['height']
            : null;
    }

    public function size(): ?int
    {
        return isset($this->data['size'])
            ? (int) $this->data['size']
            : null;
    }

    public function expiration(): ?int
    {
        return isset($this->data['expiration'])
            ? (int) $this->data['expiration']
            : null;
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
