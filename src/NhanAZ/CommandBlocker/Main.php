<?php

declare(strict_types=1);

namespace NhanAZ\CommandBlocker;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\server\CommandEvent;

class Main extends PluginBase implements Listener {

	private array $blockedCommands = [];

	protected function onEnable(): void {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->saveDefaultConfig();
		$this->blockedCommands = $this->getConfig()->get("blockedCommands");
		foreach ($this->blockedCommands as $cmd) {
			$cmdMap = $this->getServer()->getCommandMap()->getCommand($cmd);
			if (is_null($cmdMap)) {
				$this->getLogger()->warning("Unknown command: $cmd");
				$key = array_search($cmd, $this->blockedCommands);
				unset($this->blockedCommands[$key]);
				$this->getConfig()->set("blockedCommands", array_values($this->blockedCommands));
				continue;
			}
			$aliases = $cmdMap->getAliases();
			foreach ($aliases as $aliase) {
				array_push($this->blockedCommands, $aliase, $cmdMap->getName(), $cmdMap->getLabel());
			}
		}
	}

	/**
	 * @handleCancelled true
	 */
	public function onCmd(CommandEvent $event): void {
		$cmd = explode(" ", $event->getCommand());
		$cmd = strtolower($cmd[0]);
		$cmd = str_replace(["\"", "'", "pocketmine:"], "", $cmd);
		if (in_array($cmd, $this->blockedCommands)) {
			$event->getSender()->sendMessage("§cBanned command: §b{$cmd}");
			$event->cancel();
		}
	}
}
