<?php declare(strict_types=1);

namespace PluginMaster\Container;

use PluginMaster\Container\Contracts\ContainerContract;
use PluginMaster\Container\Exceptions\NotFoundException;
use PluginMaster\Container\Resolver\CallbackResolver;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;

class Container implements ContainerContract, ContainerInterface
{
    protected array $resolved = [];

    protected array $bindings = [];

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
            ...$this->getClassDependencies($constructorParams, $parameters)
        );
    }

    /**
     * @throws \ReflectionException
     * @throws \PluginMaster\Container\Exceptions\NotFoundException
     */
    protected function getClassDependencies(array $constructorParams, array $parameters): array
    {
        $dependencies = [];
        /**
         * loop with constructor parameters or dependencies
         */
        foreach ($constructorParams as $constructorParam) {
            $type = $constructorParam->getType();

            if ($type instanceof ReflectionNamedType) {
                $instance = $constructorParam->getType() && ! $constructorParam->getType()->isBuiltin()
                    ? $this->make($constructorParam->getType()->getName())
                    : $constructorParam->getClass()->newInstance();

                $dependencies[] = $instance;
            } else {
                $name = $constructorParam->getName();

                if (! empty($parameters) && array_key_exists($name, $parameters)) {
                    $dependencies[] = $parameters[$name];
                } else {
                    if (! $constructorParam->isOptional()) {
                        throw new NotFoundException('Can not resolve parameters');
                    }
                }
            }
        }

        return $dependencies;
    }

    /**
     * @throws \ReflectionException
     * @throws \PluginMaster\Container\Exceptions\NotFoundException
     */
    protected function getMethodDependency(object $object, string $method, array $parameters): array
    {
        $methodReflection = new ReflectionMethod($object, $method);
        $methodParams = $methodReflection->getParameters();
        $dependencies = [];

        foreach ($methodParams as $param) {
            $type = $param->getType();

            if (! $type instanceof ReflectionNamedType) {
                $dependencies[] = $this->get($param->getName());

                continue;
            }

            $name = $param->getName();
            if (! array_key_exists($name, $parameters)) {
                if (! $param->isOptional()) {
                    throw new NotFoundException('Can not resolve parameters');
                }

                continue;
            }

            $dependencies[] = $parameters[$name];
        }

        return [$methodReflection, $dependencies];
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
