<?php declare(strict_types=1);

namespace PluginMaster\Container;

use PluginMaster\Container\Contracts\ContainerContract;
use PluginMaster\Container\Exceptions\NotFoundException;
use PluginMaster\Container\Resolver\CallbackResolver;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionMethod;

class Container extends ContainerContract implements ContainerInterface
{
    /**
     * @throws \ReflectionException
     * @throws \PluginMaster\Container\Exceptions\NotFoundException
     */
    public function call($callable, array $parameters = [], $options = []): mixed
    {
        if(is_callable($callable)) {
            return $callable(...$parameters);
        }

        $options['container'] = $this;

        $resolvedCallable = CallbackResolver::resolve($callable, $options);

        $object = is_object($resolvedCallable[0]) ? $resolvedCallable[0] : $this->make($resolvedCallable[0], $parameters);
        $method = $resolvedCallable[1];


        /** @var ReflectionMethod $methodReflection */
        list($methodReflection, $dependencies) = $this->getMethodDependency($object, $method, $parameters);

        return $methodReflection->invoke($object, ...$dependencies);
    }

    /**
     * @throws \ReflectionException|\PluginMaster\Container\Exceptions\NotFoundException
     */
    public function make(mixed $class, array $parameters = []): mixed
    {
        if(is_callable($class)) {
            return $class($parameters);
        }

        $classReflection = new ReflectionClass($class);
        $constructorParams = $classReflection->getConstructor() ? $classReflection->getConstructor()->getParameters() : [];

        return $this->resolved[$class] = $classReflection->newInstance(
            ...$this->getDependencies($constructorParams, $parameters)
        );
    }

    /**
     * @throws \ReflectionException|\PluginMaster\Container\Exceptions\NotFoundException
     */
    public function get(string $id): mixed
    {
        if ($this->resolved[$id] ?? false) {
            return $this->resolved[$id];
        }

        $this->resolved[$id] = $this->make($this->bindings[$id] ?? $id);

        /**
         * Remove bindings callable after resolve
         */
        if ($this->bindings[$id] ?? false) {
            $this->bindings[$id] = '';
        }

        return $this->resolved[$id];
    }

    public function has(string $id): bool
    {
        if ($this->resolved[$id] ?? false) {
            return true;
        }

        return false;
    }

    /**
     * @throws \Exception
     */
    public function set(string $name, mixed $callable): void
    {
        if ($this->bindings[$name] ?? false) {
            throw new NotFoundException("$name binding already exist");
        }

        $this->bindings[$name] = $callable;
    }
}
