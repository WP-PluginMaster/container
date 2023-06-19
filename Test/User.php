<?php declare(strict_types=1);

class User
{
    private string $name;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): string
    {
        return $this->name = $name;
    }
}
