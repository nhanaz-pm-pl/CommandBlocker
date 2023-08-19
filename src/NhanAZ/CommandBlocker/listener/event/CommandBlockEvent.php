<?php

declare(strict_types=1);

namespace NhanAZ\CommandBlocker\listener\event;

use ColinHDev\libAsyncEvent\AsyncEvent;
use ColinHDev\libAsyncEvent\ConsecutiveEventHandlerExecutionTrait;
use NhanAZ\CommandBlocker\blocker\command\CommandBlocker;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\player\PlayerEvent;
use pocketmine\player\Player;

class CommandBlockEvent extends PlayerEvent implements AsyncEvent, Cancellable {
    use CancellableTrait, ConsecutiveEventHandlerExecutionTrait;

    private CommandBlocker $command;

    public function __construct(Player $player, CommandBlocker $command) {
        $this->player = $player;
        $this->command = $command;
    }

    public function getCommandBlocker(): CommandBlocker {
        return $this->command;
    }
}