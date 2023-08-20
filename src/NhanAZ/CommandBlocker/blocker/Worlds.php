<?php

declare(strict_types=1);

namespace NhanAZ\CommandBlocker\blocker;

use NhanAZ\CommandBlocker\utils\Utils;
use pocketmine\world\World;
use WeakMap;

final class Worlds {

    private static WeakMap $worlds;

    public static function get(World $world) :?WorldBlocker {
        if(!self::has($world)) {
            $map = new WeakMap();
            self::$worlds = $map;
        }

        return self::$worlds[$world] ??= self::load($world);
    }

    public static function set(World $world, WorldBlocker $blocker) : void {
        self::$worlds[$world] = $blocker;
    }

    public static function remove(World $world) : void {
        unset(self::$worlds[$world]);
    }

    public static function has(World $world) : bool {
        return isset(self::$worlds[$world]);
    }

    public static function load(World $world) : ?WorldBlocker {
        return Utils::parseWorldBlocker($world);
    }

}