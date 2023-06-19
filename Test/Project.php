<?php declare(strict_types=1);

class Project
{
    public function __construct(protected User $user, $name)
    {
        $this->user->setName($name);
    }

    public function get(): string
    {
        return 'Running...';
    }

    public function user(): User
    {
        return $this->user;
    }

    public function call(): string
    {
        return 'Calling...';
    }

    public function make(): string
    {
        return 'Calling...';
    }

}
