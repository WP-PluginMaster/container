<?php declare(strict_types=1);

namespace PluginMaster\Container\Exceptions;

use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends \Exception implements NotFoundExceptionInterface
{
}
