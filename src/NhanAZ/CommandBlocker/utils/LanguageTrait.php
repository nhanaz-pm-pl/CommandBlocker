<?php

declare(strict_types=1);

namespace NhanAZ\CommandBlocker\utils;

use NhanAZ\CommandBlocker\Main;

trait LanguageTrait {

    CONST WARN_MESSAGE = "warn.message";

    CONST LIST_WORLDS = "list.worlds";
    CONST LIST_HELP = "list.help";
    CONST LIST_BLOCKER_IN_WORLD = "list.blocker.in.world";
    CONST WORLD_NOT_FOUND = "world.not.found";
    CONST DONT_HAVE_BLOCKER = "dont.have.blocker.world";
    CONST DONT_HAVE_BLOCKER_COMMAND = "dont.have.blocker.command";

    CONST COMMAND_BLOCKED_NAME = "command.blocked.name";
    CONST COMMAND_BLOCKED_ARGS = "command.blocked.args";
    CONST COMMAND_BLOCKED_ALIAS = "command.blocked.alias";
    CONST COMMAND_BLOCKED_LIMIT = "command.blocked.limit";

    CONST DONT_HAVE_PERMISSION = "dont.have.permission";

    public static function translateString(string $string, array $args = []): string {
        return Main::getInstance()->getLanguage()->translateString($string, $args);
    }
}