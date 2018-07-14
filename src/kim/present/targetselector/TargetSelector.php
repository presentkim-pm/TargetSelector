<?php

/*
 *
 *  ____                           _   _  ___
 * |  _ \ _ __ ___  ___  ___ _ __ | |_| |/ (_)_ __ ___
 * | |_) | '__/ _ \/ __|/ _ \ '_ \| __| ' /| | '_ ` _ \
 * |  __/| | |  __/\__ \  __/ | | | |_| . \| | | | | | |
 * |_|   |_|  \___||___/\___|_| |_|\__|_|\_\_|_| |_| |_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author  PresentKim (debe3721@gmail.com)
 * @link    https://github.com/PresentKim
 * @license https://www.gnu.org/licenses/agpl-3.0.html AGPL-3.0.0
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 */

declare(strict_types=1);

namespace kim\present\targetselector;

use kim\present\targetselector\listener\CommandEventListener;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;

class TargetSelector extends PluginBase{
	/** @var TargetSelector */
	private static $instance;

	/** @return TargetSelector */
	public static function getInstance() : TargetSelector{
		return self::$instance;
	}

	/**
	 * Called when the plugin is loaded, before calling onEnable()
	 */
	public function onLoad() : void{
		self::$instance = $this;
	}

	/**
	 * Called when the plugin is enabled
	 */
	public function onEnable() : void{
		//Register event listeners
		$this->getServer()->getPluginManager()->registerEvents(new CommandEventListener($this), $this);
	}

	/**
	 * Parse target selector in command
	 *
	 * @param string        $command
	 * @param CommandSender $sender
	 *
	 * @return string[]
	 */
	public function parseCommand(string $command, CommandSender $sender) : array{
		//TODO: Parse target selector in command
		return [$command];
	}
}