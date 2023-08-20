<?php

declare(strict_types=1);

namespace NhanAZ\CommandBlocker\blocker\command;

use pocketmine\player\Player;
use pocketmine\Server;

class CommandBlocker {

    private string $command;
    private ?array $arguments;
    private ?Limit $limit;
    private array $permissions;

    private array $triggered = [];


    public function __construct(string $command, ?array $arguments = null, ?Limit $limit = null) {
        $this->command = $command;
        $this->arguments = $arguments;
        $this->limit = $limit;
        $commandMap = Server::getInstance()->getCommandMap()->getCommand($command);
        $this->permissions = $commandMap !== null ? $commandMap->getPermissions() : [];
    }

    public function getCommand(): string {
        return $this->command;
    }

    public function getArguments(): ?array {
        return $this->arguments;
    }

    public function getLimit(): ?Limit {
        return $this->limit;
    }

    public function getPermissions(): array {
        return $this->permissions;
    }

    public function getAliases() :array {
        $commandMap = Server::getInstance()->getCommandMap()->getCommand($this->getCommand());
        return $commandMap !== null ? $commandMap->getAliases() : [];
    }

    public function hasLimit() :bool {
        return $this->limit !== null;
    }

    public function hasArguments() :bool {
        return $this->arguments !== null;
    }

    public function isArgumentsBlocked(array $arguments): bool {
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

    public function getBlockedCommand() :string {
        $command = $this->getCommand();
        $arguments = $this->getArguments();
        if($arguments !== null) {
            $command .= " " . implode("|", $arguments);
        }
        return $command;
    }

    public function testPermission(array $permission): bool {
        $intersect = array_intersect($this->permissions, $permission);
        return !empty($intersect);
    }

    public function trigger(Player $player): void {
        $now = time();

        if (!isset($this->triggered[$player->getName()])) {
            $this->triggered[$player->getName()] = [
                "lastTrigger" => $now,
                "VL" => 0
            ];
            return;
        }

        $lastTrigger = $this->triggered[$player->getName()]["lastTrigger"];
        $limit = $this->getLimit();

        if ($now - $lastTrigger >= $limit->getInterval()) {
            $this->triggered[$player->getName()]["lastTrigger"] = $now;
            $this->triggered[$player->getName()]["VL"] = 0;
        } else {
            $this->triggered[$player->getName()]["VL"]++;
        }

        if ($this->triggered[$player->getName()]["VL"] >= $limit->getAmount()) {
            $limit->handle($player);
        }
    }


    public function toArray() :array {
        return [
            "command" => $this->command,
            "arguments" => $this->arguments ?? [],
            "limit" => $this->limit?->toArray() ?? [],
            "permissions" => $this->permissions,
        ];
    }

    public static function create(string $command, array $array = []) :self {
        return new self(
            $command,
            $array["arguments"] ?? null,
            isset($array["limit"]) ? Limit::fromArray($array["limit"]) : null
        );
    }

}