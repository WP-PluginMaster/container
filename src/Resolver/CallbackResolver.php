<?php declare(strict_types=1);

namespace PluginMaster\Container\Resolver;

use PluginMaster\Container\Exceptions\NotFoundException;

class CallbackResolver
{
    /**
     * @throws \PluginMaster\Container\Exceptions\NotFoundException|\ReflectionException
     */
    public static function resolve(mixed $callback, array $options = []): array
    {
        $methodSeparator = $options['methodSeparator'] ?? '@';
        $namespace = $options['namespace'] ?? '';

        $callbackClass = null;
        $callbackMethod = null;

        if (is_string($callback)) {
            $segments = explode($methodSeparator, $callback);

            $callbackClass = class_exists($segments[0]) ? $segments[0] : $namespace.$segments[0];
            $callbackMethod = $segments[1] ?? '__invoke';
        }

        if (is_array($callback)) {
            if (is_object($callback[0])) {
                $callbackClass = $callback[0];
            }

            if (is_string($callback[0])) {
                $callbackClass = class_exists($callback[0]) ? $callback[0] : $namespace.$callback[0];
            }

            $callbackMethod = $callback[1] ?? '__invoke';
        }


        if (! $callbackClass || ! $callbackMethod) {
            throw new NotFoundException('Controller Class or Method not found');
        }

        return [$callbackClass, $callbackMethod];
    }

}
