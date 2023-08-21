<?php

declare(strict_types=1);

namespace NhanAZ\CommandBlocker\utils;

use NhanAZ\CommandBlocker\blocker\command\CommandBlocker;
use NhanAZ\CommandBlocker\blocker\command\Limit;
use NhanAZ\CommandBlocker\blocker\WorldBlocker;
use NhanAZ\CommandBlocker\blocker\Worlds;
use NhanAZ\CommandBlocker\Main;
use pocketmine\Server;
use pocketmine\world\World;

class Utils {

    public static function parseWorldBlocker(World $world): ?WorldBlocker {
        $config = Main::getInstance()->getConfig();
        $worldName = $world->getFolderName();
        $worlds = $config->get("worlds");

        if(!isset($worlds[$worldName])) {
            return null;
        }
        $commandBlockers = [];
        foreach ($worlds[$world->getFolderName()] as $command => $blockerData) {
            if(is_int($command)) {
                $command = $blockerData;
                $blockerData = [];
            }
            $commandBlockers[] = CommandBlocker::create($command, $blockerData);
        }

        return !empty($commandBlockers) ? WorldBlocker::fromArray($commandBlockers) : null;
    }

    public static function getWorldLoadedByName(string $name): ?World {
        $worldManager = Server::getInstance()->getWorldManager();
        if($worldManager->loadWorld($name) !== null) {
            return $worldManager->getWorldByName($name);
        }
        return null;
    }

    public static function getWorlds(): array {
        $config = Main::getInstance()->getConfig();
        $worlds = $config->get("worlds");
        return array_keys($worlds);
    }

    public static function getBlockersCommands(): array {
        $config = Main::getInstance()->getConfig();
        $worlds = $config->get("worlds");
        $blockers = [];
        foreach (array_keys($worlds) as $world) {
            $world = self::getWorldLoadedByName($world);
            if($world === null) continue;
            if(Worlds::get($world) !== null) {
                $blocker = Worlds::get($world);
                $blockers[$world->getFolderName()] = $blocker->getCommands();
            }
        }
        return $blockers;
    }


    /**
     * @return CommandBlocker[]
     */
    public static function getGlobals(): array {
        $config = Main::getInstance()->getConfig();
        $globals = $config->get("globals");

        return array_map(function ($command) {
            return CommandBlocker::create($command);
        }, $globals);
    }

}