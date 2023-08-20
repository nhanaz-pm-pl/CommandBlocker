<?php

declare(strict_types=1);

namespace NhanAZ\CommandBlocker\utils;

use NhanAZ\CommandBlocker\Main;

trait LanguageTrait {

    public static function translateString(LanguageEnums $enums, array $args = []): string {
        return Main::getInstance()->getLanguage()->translateString($enums->value, $args);
    }
}