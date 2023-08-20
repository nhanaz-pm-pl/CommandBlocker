<?php

declare(strict_types=1);

namespace NhanAZ\CommandBlocker\command\sub;

use CortexPE\Commando\BaseSubCommand;
use NhanAZ\CommandBlocker\utils\LanguageTrait;
use NhanAZ\CommandBlocker\utils\Utils;
use pocketmine\command\CommandSender;

class WorldsSub extends BaseSubCommand {
    use LanguageTrait;

    protected function prepare(): void {
        $this->setPermission("commandblocker.command.worlds");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        $commandBlockers = Utils::getBlockersCommands();
        $worldsBlockers = array_keys($commandBlockers);
        foreach($commandBlockers as $world => $blockers) {
            $sender->sendMessage(self::translateString(self::LIST_BLOCKER_IN_WORLD, [$world, implode(", ", $blockers)]));
        }
        $sender->sendMessage(self::translateString(self::LIST_HELP));
    }
}