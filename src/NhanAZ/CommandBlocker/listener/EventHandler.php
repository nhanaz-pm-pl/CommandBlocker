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

        if(!$sender instanceof Player) {
            return;
        }

        $command = $event->getCommand();
        $world = $sender->getWorld();

        if(Worlds::get($world->getFolderName())) {
            $permission = Server::getInstance()->getCommandMap()->getCommand($command)->getPermissions();
            $blockers = Worlds::get($world->getFolderName());
            if(($blocker = $blockers->testPermissions($permission))) {
                $array = explode(" ", $command);
                $arguments = [];
                if(count($array) > 1) {
                    $arguments = array_slice($array, 1);
                }
                if($blocker->compareArguments($arguments)) {
                    if($blocker->getLimit() !== null) {
                        $blocker->trigger($sender);
                    }
                }
                $blockerEvent = new CommandBlockEvent($sender, $blocker);
                $blockerEvent->setCallback(function(CommandBlockEvent $event){
                    if($event->isCancelled()) {
                        return;
                    }
                    $config = Main::getInstance()->getConfig();
                    $blocker = $event->getCommandBlocker();
                    $player = $event->getPlayer();
                    $warn = $config->get("warn");
                    $warnMessage = "§c[CommandBlocker] §fCommand §c{$blocker->getCommand()} §fwas triggered by §c{$player->getName()}§f. has been used command /{$blocker->getCommand()}";
                    if($warn["console"]) {
                        Server::getInstance()->getLogger()->warning($warnMessage);
                    }
                    if($warn["admins"]) {
                        foreach (Server::getInstance()->getOnlinePlayers() as $onlinePlayer) {
                            if(Server::getInstance()->isOp($onlinePlayer->getName())) {
                                $onlinePlayer->sendMessage($warnMessage);
                            }
                        }
                    }
                });
                $event->call();
                $event->cancel();
            }
        }
    }
}