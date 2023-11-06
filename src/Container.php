<?php

declare(strict_types=1);

namespace Andersma\MapCropperWebService;

final class Container
{
    private static array $singletonInstances = [];

    public static function get(string $id): object|null
    {
        return self::$singletonInstances[$id] ?? null;
    }

    /** @param $id null|string By default, class string of given object is used */
    public static function set(object $obj, ?string $id = null): void
    {
        $id ??= \get_class($obj);

        if (!isset(self::$singletonInstances[$id])) {
            self::$singletonInstances[$id] = $obj;
        } else {
            throw new \LogicException(\sprintf('Provided "%s" service is already registered', $id));
        }
    }

    /** @param class-string $id */
    public static function getOrNew(string $id): object
    {
        if (isset(self::$singletonInstances[$id])) {
            return self::$singletonInstances[$id];
        }

        $instance = new $id();

        self::$singletonInstances[$id] = $instance;
        return $instance;
    }
}