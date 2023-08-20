<?php

declare(strict_types=1);

namespace NhanAZ\CommandBlocker;

use NhanAZ\CommandBlocker\command\BlockerCommand;
use NhanAZ\CommandBlocker\listener\EventHandler;
use pocketmine\lang\Language;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class Main extends PluginBase {
    use SingletonTrait;

    private Language $language;

    protected function onLoad(): void {
        self::setInstance($this);
    }

    protected function onEnable(): void {
        $this->saveDefaultConfig();
        $this->saveResource("languages/eng.ini");
        $this->getServer()->getPluginManager()->registerEvents(new EventHandler(), $this);
        $this->getServer()->getCommandMap()->register("CommandBlocker", new BlockerCommand($this, "commandblocker", "CommandBlocker command", ["cb"]));
        $languageCode = $this->getConfig()->get("language", "eng");
        $this->language = new Language($languageCode, $this->getDataFolder() . "languages" . DIRECTORY_SEPARATOR);
    }

    public function getLanguage(): Language {
        return $this->language;
    }

}
