<?php

declare(strict_types=1);

namespace NhanAZ\CommandBlocker\blocker\command;

use pocketmine\player\Player;

final class Limit {

    public function __construct(
        private readonly string $action,
        private readonly int    $amount,
        private readonly int $interval
    ) {}

    public function handle(Player $player) :void {
        switch ($this->action) {
            case "kick":
                $player->kick("You are sending too many commands too quickly");
                break;
            case "ban":
                $player->getServer()->getNameBans()->addBan($player->getName(), "You are sending too many commands too quickly");
                $player->kick("You are sending too many commands too quickly");
                break;
            case "warn":
                $player->sendMessage("You are sending too many commands too quickly");
                break;
        }
    }

    public function getAction() :string {
        return $this->action;
    }

    public function getAmount() :int {
        return $this->amount;
    }

    public function getInterval() :int {
        return $this->interval;
    }

    public function toArray() :array {
        return [
            "action" => $this->action,
            "amount" => $this->amount,
            "interval" => $this->interval
        ];
    }

    public static function fromArray(array $data) :Limit {
        return new Limit(
            $data["action"],
            $data["amount"],
            $data["interval"]
        );
    }
}