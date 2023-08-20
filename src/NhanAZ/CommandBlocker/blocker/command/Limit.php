<?php

declare(strict_types=1);

namespace NhanAZ\CommandBlocker\blocker\command;

use NhanAZ\CommandBlocker\utils\LanguageEnums;
use NhanAZ\CommandBlocker\utils\LanguageTrait;
use pocketmine\player\Player;

final class Limit {
    use LanguageTrait;

    private string $action;
    private int $amount;
    private int $interval;

    public function __construct(string $action, int $amount, int $interval) {
        $this->action = $action;
        $this->amount = $amount;
        $this->interval = $interval;
    }

    public function handle(Player $player): void {
        switch ($this->action) {
            case "kick":
                $player->kick(self::translateString(LanguageEnums::LIMIT_ACTION_KICK));
                break;
            case "ban":
                $player->getServer()->getNameBans()->addBan($player->getName(), self::translateString(LanguageEnums::LIMIT_ACTION_BAN));
                break;
            case "warn":
                $player->sendMessage(self::translateString(LanguageEnums::LIMIT_ACTION_WARN));
                break;
        }
    }

    public function getAction(): string {
        return $this->action;
    }

    public function getAmount(): int {
        return $this->amount;
    }

    public function getInterval(): int {
        return $this->interval;
    }

    public function toArray(): array {
        return [
            "action" => $this->action,
            "amount" => $this->amount,
            "interval" => $this->interval
        ];
    }

    public static function fromArray(array $data): Limit {
        return new Limit(
            $data["action"],
            $data["amount"],
            $data["interval"]
        );
    }
}
