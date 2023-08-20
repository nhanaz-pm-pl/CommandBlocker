<?php

declare(strict_types=1);

namespace NhanAZ\CommandBlocker\listener;

use NhanAZ\CommandBlocker\blocker\Worlds;
use NhanAZ\CommandBlocker\listener\event\CommandBlockerEvent;
use NhanAZ\CommandBlocker\utils\LanguageTrait;
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

        if (Worlds::get($world)) {
            $commandMap = Server::getInstance()->getCommandMap();
            if($commandMap->getCommand($commandParts[0]) == null) {
                return;
            }
            $permission = $commandMap->getCommand($commandParts[0])->getPermissions();
            $blockers = Worlds::get($world);
            if($blockers == null) {
                return;
            }
            $matchingBlocker = $blockers->testPermissions($permission);
            if (!$matchingBlocker) {
                return;
            }
            if ($matchingBlocker->hasArguments()) {
                // Check whether the arguments are blocked.
                if (!(count($commandParts) > 1 && $matchingBlocker->isArgumentsBlocked(array_slice($commandParts, 1)))) {
                    return;
                }
            }
            $blockerEvent = new CommandBlockerEvent($sender, $matchingBlocker);
            $blockerEvent->call();
            if ($matchingBlocker->hasLimit()) {
                var_dump("IS LIMIT");
                $matchingBlocker->trigger($sender);
            }
        }
    }

    public function onCommandBlockerEvent(CommandBlockerEvent $event): void {
        if ($event->isCancelled()) {
            return;
        }
        $blocker = $event->getCommandBlocker();
        $player = $event->getPlayer();
        $warnMessage = self::translateString(self::WARN_MESSAGE, [$blocker->getBlockedCommand()]);
        $player->sendMessage($warnMessage);
    }
}