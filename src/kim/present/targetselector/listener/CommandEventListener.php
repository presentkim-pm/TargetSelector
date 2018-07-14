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

namespace kim\present\targetselector\listener;

use kim\present\targetselector\TargetSelector;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\server\{
	RemoteServerCommandEvent, ServerCommandEvent
};

class CommandEventListener implements Listener{
	/** @var TargetSelector */
	private $plugin;

	/**
	 * CommandEventListener constructor.
	 *
	 * @param TargetSelector $plugin
	 */
	public function __construct(TargetSelector $plugin){
		$this->plugin = $plugin;
	}

	/**
	 * @priority LOWEST
	 *
	 * @param PlayerCommandPreprocessEvent $event
	 */
	public function onPlayerCommandPreprocessEvent(PlayerCommandPreprocessEvent $event) : void{
		if(strpos($message = $event->getMessage(), "/") === 0){
			$command = substr($message, 1);
			$sender = $event->getPlayer();
			$results = $this->plugin->parseCommand($command, $sender);
			if(count($results) === 1){
				$event->setMessage("/{$results[0]}");
			}else{
				//TODO: RUN EACH COMMAND of RESULTS
			}
		}
	}

	/**
	 * @priority LOWEST
	 *
	 * @param ServerCommandEvent $event
	 */
	public function onServerCommandEvent(ServerCommandEvent $event) : void{
		$command = $event->getCommand();
		$sender = $event->getSender();
		$results = $this->plugin->parseCommand($command, $sender);
		if(count($results) === 1){
			$event->setCommand($results[0]);
		}else{
			//TODO: RUN EACH COMMAND of RESULTS
		}
	}

	/**
	 * @priority LOWEST
	 *
	 * @param RemoteServerCommandEvent $event
	 */
	public function onRemoteServerCommandEvent(RemoteServerCommandEvent $event) : void{
		$this->onServerCommandEvent($event);
	}
}
