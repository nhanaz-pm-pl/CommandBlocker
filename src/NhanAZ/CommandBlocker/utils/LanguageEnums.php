<?php

declare(strict_types=1);

namespace NhanAZ\CommandBlocker\utils;

class LanguageEnums {
    public const WARN_MESSAGE = "warn.message";
    public const LIST_WORLDS = "list.worlds";
    public const LIST_HELP = "list.help";
    public const LIST_BLOCKER_IN_WORLD = "list.blocker.in.world";
    public const WORLD_NOT_FOUND = "world.not.found";
    public const DONT_HAVE_BLOCKER = "dont.have.blocker.world";
    public const DONT_HAVE_BLOCKER_COMMAND = "dont.have.blocker.command";
    public const COMMAND_BLOCKED_NAME = "command.blocked.name";
    public const COMMAND_BLOCKED_ARGS = "command.blocked.args";
    public const COMMAND_BLOCKED_ALIAS = "command.blocked.alias";
    public const COMMAND_BLOCKED_LIMIT = "command.blocked.limit";
    public const DONT_HAVE_PERMISSION = "dont.have.permission";

    public const LIMIT_ACTION_KICK = "limit.action.kick";
    public const LIMIT_ACTION_BAN = "limit.action.ban";
    public const LIMIT_ACTION_WARN = "limit.action.warn";
}
