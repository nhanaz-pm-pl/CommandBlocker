<?php

declare(strict_types=1);

namespace NhanAZ\CommandBlocker\blocker\command;

use pocketmine\player\Player;
use pocketmine\Server;

class CommandBlocker {

    private string $command;
    private array $arguments;
    private Limit $limit;
    private array $permissions;

    private array $triggered = [];


    public function __construct(string $command, array $arguments = [], ?Limit $limit = null) {
        $this->command = $command;
        $this->arguments = $arguments;
        $this->limit = $limit;
        $this->permissions = Server::getInstance()->getCommandMap()->getCommand($command)->getPermissions();
    }

    public function getCommand(): string {
        return $this->command;
    }

    public function getArguments(): array {
        return $this->arguments;
    }

    public function getLimit(): ?Limit {
        return $this->limit;
    }

    public function getPermissions(): array {
        return $this->permissions;
    }

    public function compareArguments(array $arguments): bool {
        if (count($this->arguments) !== count($arguments)) {
            return false;
        }
        foreach ($this->arguments as $index => $argument) {
            if ($argument !== $arguments[$index]) {
                return false;
            }
        }
        return true;
    }

    public function testPermission(array $permission): bool {
        $intersect = array_intersect($this->permissions, $permission);
        return !empty($intersect);
    }

    public function trigger(Player $player): void {
        $now = time();
        $lastTrigger = $this->triggered[$player->getName()] ?? 0;
        $limit = $this->getLimit();
        if($now - $lastTrigger > $limit->getInterval()) {
            $this->triggered[$player->getName()] = $now;
        }
        if($limit->getLimit() <= count($this->triggered)) {
            $limit->handle($player);
        }
    }

}