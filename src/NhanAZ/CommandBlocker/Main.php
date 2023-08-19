<?php

declare(strict_types=1);

namespace NhanAZ\CommandBlocker;

use NhanAZ\CommandBlocker\listener\EventHandler;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class Main extends PluginBase {
    use SingletonTrait;

    protected function onLoad(): void {
        self::setInstance($this);
    }

    protected function onEnable(): void {
        $this->saveDefaultConfig();
        $this->getServer()->getPluginManager()->registerEvents(new EventHandler(), $this);
    }

}
