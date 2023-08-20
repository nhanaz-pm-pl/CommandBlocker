<?php

declare(strict_types=1);

namespace NhanAZ\CommandBlocker\command\sub;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use NhanAZ\CommandBlocker\blocker\Worlds;
use NhanAZ\CommandBlocker\utils\LanguageEnums;
use NhanAZ\CommandBlocker\utils\LanguageTrait;
use NhanAZ\CommandBlocker\utils\Utils;
use pocketmine\command\CommandSender;

class ListSub extends BaseSubCommand {
    use LanguageTrait;


    /**
     * @throws ArgumentOrderException
     */
    protected function prepare(): void {
        $this->setPermission("commandblocker.command.list");
        $this->registerArgument(0, new RawStringArgument("world"));
        $this->registerArgument(1, new RawStringArgument("command"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        $worldName = $args["world"];
        $world = Utils::getWorldLoadedByName($worldName);
        if($world === null) {
            $sender->sendMessage(self::translateString(LanguageEnums::WORLD_NOT_FOUND, [$worldName]));
            return;
        }
        if(Worlds::get($world) === null) {
            $sender->sendMessage(self::translateString(LanguageEnums::DONT_HAVE_BLOCKER, [$worldName]));
            return;
        }

        $blocker = Worlds::get($world);
        $command = $args["command"] ?? null;
        if ($command !== null) {
            if($blocker->hasCommand($command)) {
                $commandBlocker = $blocker->getCommandBlocker($command);
                if($commandBlocker === null) {
                    $sender->sendMessage(self::translateString(LanguageEnums::DONT_HAVE_BLOCKER_COMMAND, [$command]));
                    return;
                }
                $amount = 0;
                $time = 0;
                $args = "None";
                $aliases = "None";
                if($commandBlocker->hasLimit()) {
                    $amount = $commandBlocker->getLimit()->getAmount();
                    $time = $commandBlocker->getLimit()->getInterval();
                }
                if(count($commandBlocker->getArguments()) > 0) {
                    $args = implode(", ", $commandBlocker->getArguments());
                }
                if(count($commandBlocker->getAliases()) > 0) {
                    $aliases = implode(", ", $commandBlocker->getAliases());
                }
                $sender->sendMessage(self::translateString(LanguageEnums::COMMAND_BLOCKED_NAME, [$command]));
                $sender->sendMessage(self::translateString(LanguageEnums::COMMAND_BLOCKED_ARGS, [$args]));
                $sender->sendMessage(self::translateString(LanguageEnums::COMMAND_BLOCKED_ALIAS, [$aliases]));
                $sender->sendMessage(self::translateString(LanguageEnums::COMMAND_BLOCKED_LIMIT, [$amount, $time]));
            }
        }
    }
}