<?php declare(strict_types=1);

namespace PluginMaster\Container\Contracts;

use PluginMaster\Container\Exceptions\NotFoundException;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;

abstract class ContainerContract
{
    protected array $resolved = [];

    protected array $bindings = [];

    abstract public function make(string $class, array $parameters = []): mixed;

    abstract public function call($callable, array $parameters = []): mixed;

    abstract public function get(string $id): mixed;

    abstract public function set(string $name, mixed $callable): void;

    abstract public function has(string $id): bool;

    /**
     * @param  array<int, ReflectionParameter>  $reflectionParameters
     * @throws \ReflectionException
     * @throws \PluginMaster\Container\Exceptions\NotFoundException
     */
    public function getDependencies(array $reflectionParameters, array $parameters): array
    {
        $dependencies = [];

        foreach ($reflectionParameters as $reflectionParameter) {
            $dependencies[] = $this->processParameter($reflectionParameter, $parameters);
        }

        return $dependencies;
    }

    /**
     * @throws \ReflectionException
     * @throws \PluginMaster\Container\Exceptions\NotFoundException
     */
    private function processParameter(ReflectionParameter $reflectionParameter, $parameters): mixed
    {
        $type = $parameters->getType();

        if (
            $type instanceof ReflectionNamedType
            && $reflectionParameter->getType()
            && ! $reflectionParameter->getType()->isBuiltin()
        ) {
            return $this->get($reflectionParameter->getType()->getName());
        }

        $name = $reflectionParameter->getName();

        return $this->getValueFromArrayParams($name, $parameters, $reflectionParameter->isOptional());
    }


    /**
     * @throws \PluginMaster\Container\Exceptions\NotFoundException
     */
    private function getValueFromArrayParams($name, $parameters, $isOptional = false): mixed
    {
        if (! empty($parameters) && array_key_exists($name, $parameters)) {
            return $parameters[$name];
        }

        if (! $isOptional) {
            throw new NotFoundException("Can not resolve parameter $name");
        }

        return null;
    }

    /**
     * @throws \ReflectionException
     * @throws \PluginMaster\Container\Exceptions\NotFoundException
     */
    protected function getMethodDependency(object $object, string $method, array $parameters): array
    {
        $methodReflection = new ReflectionMethod($object, $method);
        $methodParams = $methodReflection->getParameters();
        $dependencies = $this->getDependencies($methodParams, $parameters);

        return [$methodReflection, $dependencies];
    }

}
