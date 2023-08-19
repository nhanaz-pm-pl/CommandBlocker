<?php

declare(strict_types=1);

namespace NhanAZ\CommandBlocker\utils;

use NhanAZ\CommandBlocker\blocker\command\CommandBlocker;
use NhanAZ\CommandBlocker\blocker\command\Limit;
use NhanAZ\CommandBlocker\blocker\WorldBlocker;
use NhanAZ\CommandBlocker\Main;
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

        foreach ($worlds[$world->getFolderName()] as $command => $data) {
            $arguments = $data['arguments'] ?? [];
            $limit = $data['limit'] ?? null;

            if ($limit !== null) {
                $limit = Limit::fromArray($limit);
            }

            $commandBlockers[] = new CommandBlocker($command, $arguments, $limit);
        }

        return WorldBlocker::fromArray($commandBlockers);
    }

}