<?php

/**
 *  ____                           _   _  ___
 * |  _ \ _ __ ___  ___  ___ _ __ | |_| |/ (_)_ __ ___
 * | |_) | '__/ _ \/ __|/ _ \ '_ \| __| ' /| | '_ ` _ \
 * |  __/| | |  __/\__ \  __/ | | | |_| . \| | | | | | |
 * |_|   |_|  \___||___/\___|_| |_|\__|_|\_\_|_| |_| |_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author  PresentKim (debe3721@gmail.com)
 * @link    https://github.com/PresentKim
 * @license https://www.gnu.org/licenses/lgpl-3.0 LGPL-3.0 License
 *
 *   (\ /)
 *  ( . .) ♥
 *  c(")(")
 *
 * @noinspection PhpIllegalPsrClassPathInspection
 * @noinspection PhpDocSignatureInspection
 * @noinspection SpellCheckingInspection
 * @noinspection PhpUnusedParameterInspection
 * @noinspection PhpUnused
 */

declare(strict_types=1);

namespace kim\present\targetselector\listener;

use kim\present\targetselector\TargetSelector;
use pocketmine\event\Listener;
use pocketmine\event\server\CommandEvent;

use function count;

/** @internal */
final class CommandEventListener implements Listener{
    public function __construct(
        private TargetSelector $plugin
    ){
    }

    /** @priority LOWEST */
    public function onCommandEvent(CommandEvent $event) : void{
        $command = $event->getCommand();
        $sender = $event->getSender();
        $results = $this->plugin->parseCommand($command, $sender);
        if(count($results) === 1){
            $event->setCommand($results[0]);
        }else{
            $event->cancel();

            foreach($results as $result){
                $this->plugin->getServer()->dispatchCommand($sender, $result);
            }
        }
    }
}
