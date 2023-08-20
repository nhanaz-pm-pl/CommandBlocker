<?php

declare(strict_types=1);

namespace NhanAZ\CommandBlocker\listener;

use NhanAZ\CommandBlocker\blocker\WorldBlocker;
use NhanAZ\CommandBlocker\blocker\Worlds;
use NhanAZ\CommandBlocker\listener\event\CommandBlockerEvent;
use NhanAZ\CommandBlocker\utils\LanguageEnums;
use NhanAZ\CommandBlocker\utils\LanguageTrait;
use NhanAZ\CommandBlocker\utils\Utils;
use pocketmine\event\Listener;
use pocketmine\event\server\CommandEvent;
use pocketmine\player\Player;
use pocketmine\Server;

class EventHandler implements Listener {
    use LanguageTrait;

    public function onCommandEvent(CommandEvent $event): void {
        $sender = $event->getSender();
        if (!$sender instanceof Player) {
            return;
        }

        $commandParts = explode(" ", $event->getCommand());
        $world = $sender->getWorld();
        $commandName = $commandParts[0];

        $commandMap = Server::getInstance()->getCommandMap();
        $command = $commandMap->getCommand($commandName);

        if ($command === null) {
            return;
        }

        $permission = $command->getPermissions();
        $blockers = Worlds::get($world);
        $matchingBlocker = $blockers?->testPermissions($permission);

        if (!$matchingBlocker) {
            $globalsBlocker = WorldBlocker::fromArray(Utils::getGlobals());
            $matchingBlocker = $globalsBlocker->testPermissions($permission);
        }

        if ($matchingBlocker) {
            if ($matchingBlocker->hasArguments() && count($commandParts) > 1) {
                if (!$matchingBlocker->isArgumentsBlocked(array_slice($commandParts, 1))) {
                    return;
                }
            }
            $blockerEvent = new CommandBlockerEvent($sender, $matchingBlocker);
            $blockerEvent->call();
            if ($matchingBlocker->hasLimit()) {
                $matchingBlocker->trigger($sender);
            }
            $event->cancel();
        }

    }

    public function onCommandBlockerEvent(CommandBlockerEvent $event): void {
        if ($event->isCancelled()) {
            return;
        }
        $blocker = $event->getCommandBlocker();
        $player = $event->getPlayer();
        $warnMessage = self::translateString(LanguageEnums::WARN_MESSAGE, [$blocker->getBlockedCommand()]);
        $player->sendMessage($warnMessage);
    }
}