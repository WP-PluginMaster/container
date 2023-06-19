<?php declare(strict_types=1);

namespace PluginMaster\Container\Contracts;

interface ContainerContract
{
    public function make(string $class, array $parameters = []): mixed;

    public function call($callable, array $parameters = []): mixed;

    public function get(string $id): mixed;

    public function set(string $name, mixed $callable): void;

    public function has(string $id): bool;

}
