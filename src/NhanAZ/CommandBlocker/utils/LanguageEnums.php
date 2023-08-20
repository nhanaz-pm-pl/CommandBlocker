<?php

declare(strict_types=1);

namespace NhanAZ\CommandBlocker\utils;

enum LanguageEnums: string {
    case WARN_MESSAGE = "warn.message";
    case LIST_WORLDS = "list.worlds";
    case LIST_HELP = "list.help";
    case LIST_BLOCKER_IN_WORLD = "list.blocker.in.world";
    case WORLD_NOT_FOUND = "world.not.found";
    case DONT_HAVE_BLOCKER = "dont.have.blocker.world";
    case DONT_HAVE_BLOCKER_COMMAND = "dont.have.blocker.command";
    case COMMAND_BLOCKED_NAME = "command.blocked.name";
    case COMMAND_BLOCKED_ARGS = "command.blocked.args";
    case COMMAND_BLOCKED_ALIAS = "command.blocked.alias";
    case COMMAND_BLOCKED_LIMIT = "command.blocked.limit";
    case DONT_HAVE_PERMISSION = "dont.have.permission";

    case LIMIT_ACTION_KICK = "limit.action.kick";
    case LIMIT_ACTION_BAN = "limit.action.ban";
    case LIMIT_ACTION_WARN = "limit.action.warn";

}