<?php

declare(strict_types=1);

namespace NhanAZ\CommandBlocker;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\server\CommandEvent;

class Main extends PluginBase implements Listener {

	private array $blockedCommands = [];

	private array $blockedPermissions = [];

	protected function onEnable(): void {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->saveDefaultConfig();
		$this->blockedCommands = $this->getConfig()->get("blockedCommands");
		foreach ($this->blockedCommands as $command) {
			$commandMap = $this->getServer()->getCommandMap()->getCommand($command);
			if (is_null($commandMap)) {
				$this->getLogger()->warning("Unknown command: $command");
				$key = array_search($command, $this->blockedCommands);
				unset($this->blockedCommands[$key]);
				$this->getConfig()->set("blockedCommands", array_values($this->blockedCommands));
				continue;
			}
			$aliases = $commandMap->getAliases();
			foreach ($aliases as $aliase) {
				array_push($this->blockedCommands, $aliase, $commandMap->getName(), $commandMap->getLabel());
			}
			$permissions = $commandMap->getPermissions();
			foreach ($permissions as $permission) {
				array_push($this->blockedPermissions, $permission);
			}
		}
	}

	/**
	 * @handleCancelled true
	 */
	public function onCommandEvent(CommandEvent $event): void {
		$command = $event->getCommand();
		$commandMap = $this->getServer()->getCommandMap()->getCommand($command);
		$blockedCommand = false;
		if (!is_null($commandMap)) {
			$permissions = $commandMap->getPermissions();
			foreach ($permissions as $permission) {
				if (in_array($permission, $this->blockedPermissions)) {
					$blockedCommand = true;
				}
			}
		}
		$command = explode(" ", $command);
		$command = strtolower($command[0]);
		$command = str_replace(["\"", "'", "pocketmine:"], "", $command);
		if (in_array($command, $this->blockedCommands)) {
			$blockedCommand = true;
		}
		if ($blockedCommand) {
			$event->getSender()->sendMessage("§f[§aCommandBlocker§f] §cBanned command: §b/{$command}");
			$event->cancel();
		}
	}
}