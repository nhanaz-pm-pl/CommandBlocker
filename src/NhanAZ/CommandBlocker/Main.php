<?php

declare(strict_types=1);

namespace NhanAZ\CommandBlocker;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\server\CommandEvent;
use pocketmine\player\Player;

class Main extends PluginBase implements Listener {

	private array $blockedCommands = [];
	private array $blockedPermissions = [];

	protected function onEnable(): void {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->saveDefaultConfig();
		$this->blockedCommands = $this->getConfig()->get("blockedCommands");
		$hasUnknownCommandsExist = false;
		$unknownCommands = [];
		foreach ($this->blockedCommands as $command => $worldName) {
			$commandMap = $this->getServer()->getCommandMap()->getCommand($command);
			if (is_null($commandMap)) {
				array_push($unknownCommands, $command);
				$hasUnknownCommandsExist = true;
				continue;
			}
			$aliases = $commandMap->getAliases();
			$permissions = $commandMap->getPermissions();
			$this->blockedCommands = array_merge($this->blockedCommands, $aliases, [$commandMap->getName()], [$commandMap->getLabel()]);
			$this->blockedPermissions = array_merge($this->blockedPermissions, $permissions);
		}
		if ($hasUnknownCommandsExist) {
			$this->getLogger()->error("Unknown commands: [" . implode(", ", $unknownCommands) . "]");
			$this->getServer()->getPluginManager()->disablePlugin($this);
		}
	}

	/**
	 * @handleCancelled true
	 */
	public function onCommandEvent(CommandEvent $event): void {
		$sender = $event->getSender();
		if (!$sender instanceof Player) {
			return;
		}
		$command = $event->getCommand();
		$command = explode(" ", $command);
		$command = strtolower($command[0]);
		$commandMap = $this->getServer()->getCommandMap()->getCommand($command);
		$blockedCommand = false;
		if (!is_null($commandMap)) {
			$permissions = $commandMap->getPermissions();
			$allAlias = array_merge($commandMap->getAliases(), [$commandMap->getName()], [$commandMap->getLabel()]);
			if (!empty(array_intersect($permissions, $this->blockedPermissions))) {
				$blockedCommand = true;
			}
		}
		$command = str_replace(["\"", "'", "pocketmine:"], "", $command);
		if (in_array($command, $this->blockedCommands)) {
			$blockedCommand = true;
		}
		if ($blockedCommand) {
			$senderWorld = $sender->getWorld()->getFolderName();
			foreach ($allAlias as $alias) {
				$worldsName = $this->getConfig()->getNested("blockedCommands.$alias");
				if ($worldsName !== null) {
					if (count($worldsName) === 0) {
						$sender->sendMessage("§f[§aCommandBlocker§f] §cYou can't use §b/{$command} §cin all worlds!");
						$event->cancel();
						break;
					}
					foreach ($worldsName as $worldName) {
						if ($senderWorld == $worldName) {
							$sender->sendMessage("§f[§aCommandBlocker§f] §cYou can't use §b/{$command} §cin worlds §f[§b" . implode("§f,§b ", $worldsName) . "§f]§c!");
							$event->cancel();
							break;
						}
					}
					break;
				}
			}
		}
	}
}
