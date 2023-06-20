<?php declare(strict_types=1);

class User
{
    public function __construct(string $name = '') {
        $this->testName = $name;
    }

    private string $testName =  '';
    private string $name = '';

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): string
    {
        return $this->name = $this->testName ? $this->testName : $name;
    }
}
