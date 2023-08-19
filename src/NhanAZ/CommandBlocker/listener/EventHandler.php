<?php

declare(strict_types=1);

namespace NhanAZ\CommandBlocker\listener;

use NhanAZ\CommandBlocker\blocker\Worlds;
use NhanAZ\CommandBlocker\listener\event\CommandBlockEvent;
use NhanAZ\CommandBlocker\Main;
use pocketmine\event\Listener;
use pocketmine\event\server\CommandEvent;
use pocketmine\player\Player;
use pocketmine\Server;

class EventHandler implements Listener {

    public function onCommandEvent(CommandEvent $event): void {
        $sender = $event->getSender();

        if (!$sender instanceof Player) {
            return;
        }

        $command = $event->getCommand();
        $world = $sender->getWorld();

        if (Worlds::get($world->getFolderName())) {
            $permission = Server::getInstance()->getCommandMap()->getCommand($command)->getPermissions();
            $blockers = Worlds::get($world->getFolderName());
            $matchingBlocker = $blockers->testPermissions($permission);
            if (!$matchingBlocker) {
                return;
            }
            if ($matchingBlocker->hasArguments()) {
                $arguments = explode(" ", $event->getCommand());
                // Check whether the arguments are blocked.
                if (!(count($arguments) > 1 && $matchingBlocker->isArgumentsBlocked(array_slice($arguments, 1)))) {
                    return;
                }
            }
            $blockerEvent = new CommandBlockEvent($sender, $matchingBlocker);
            $blockerEvent->setCallback(function (CommandBlockEvent $event) {
                if ($event->isCancelled()) {
                    return;
                }
                $config = Main::getInstance()->getConfig();
                $blocker = $event->getCommandBlocker();
                $player = $event->getPlayer();
                $warn = $config->get("warn");
                $warnMessage = "§c[CommandBlocker] §fCommand §c{$blocker->getCommand()} §fwas triggered by §c{$player->getName()}§f. has been used command /{$blocker->getCommand()}";
                if ($warn["console"]) {
                    Server::getInstance()->getLogger()->warning($warnMessage);
                }
                if ($warn["admins"]) {
                    foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
                        if (Server::getInstance()->isOp($onlinePlayer->getName())) {
                            $onlinePlayer->sendMessage($warnMessage);
                        }
                    }
                }
            });
            if ($matchingBlocker->hasLimit()) {
                $matchingBlocker->trigger($sender);
            }
            $blockerEvent->call();
            $event->cancel();
        }
    }
}