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
use pocketmine\level\Position;
use pocketmine\Player;

class PlayerVariable extends Variable{
	/** Label of target selector variable */
	public const LABEL = "player";
	/** Identifier of target selector variable */
	public const IDENTIFIER = "p";

	/**
	 * @param string        $command
	 * @param CommandSender $sender
	 *
	 * @return string[]
	 */
	protected function onParse(string $command, CommandSender $sender) : array{
		$target = $sender->getName();
		if($sender instanceof Position){
			$nearPlayer = $sender->getLevel()->getNearestEntity($sender, 0xff, Player::class, true);
			if($nearPlayer instanceof Player){
				$target = $nearPlayer->getName();
			}
		}
		return [preg_replace("/{$this->toString()}/", $target, $command, 1)];
	}
}