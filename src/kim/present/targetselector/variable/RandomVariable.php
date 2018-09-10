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
 * it under the terms of the MIT License. see <https://opensource.org/licenses/MIT>.
 *
 * @author  PresentKim (debe3721@gmail.com)
 * @link    https://github.com/PresentKim
 * @license https://opensource.org/licenses/MIT MIT License
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 */

declare(strict_types=1);

namespace kim\present\targetselector\variable;

use pocketmine\command\CommandSender;
use pocketmine\Server;

class RandomVariable extends Variable{
	/** Label of target selector variable */
	public const LABEL = "random";
	/** Identifier of target selector variable */
	public const IDENTIFIER = "r";

	/**
	 * @param string        $command
	 * @param CommandSender $sender
	 *
	 * @return string[]
	 */
	protected function onParse(string $command, CommandSender $sender) : array{
		$players = Server::getInstance()->getOnlinePlayers();
		if(empty($players)){
			return [];
		}else{
			$randPlayer = $players[array_rand($players)];
			return [preg_replace("/{$this->toString()}/", $randPlayer->getName(), $command, 1)];
		}
	}
}