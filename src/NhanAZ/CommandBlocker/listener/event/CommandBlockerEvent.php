<?php

declare(strict_types=1);

namespace NhanAZ\CommandBlocker\listener\event;

use ColinHDev\libAsyncEvent\AsyncEvent;
use ColinHDev\libAsyncEvent\ConsecutiveEventHandlerExecutionTrait;
use NhanAZ\CommandBlocker\blocker\command\CommandBlocker;
use NhanAZ\CommandBlocker\listener\EventHandler;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;
use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

class CommandBlockerEvent extends PlayerEvent implements Cancellable {
    use CancellableTrait;

    private CommandBlocker $command;

    public function __construct(Player $player, CommandBlocker $command) {
        $this->player = $player;
        $this->command = $command;
    }

    public function getCommandBlocker(): CommandBlocker {
        return $this->command;
    }
}