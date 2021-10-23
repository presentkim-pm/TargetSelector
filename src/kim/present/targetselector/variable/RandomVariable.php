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

namespace kim\present\targetselector\variable;

use pocketmine\command\CommandSender;
use pocketmine\Server;

use function array_rand;
use function preg_replace;

class RandomVariable extends Variable{
    /** Label of target selector variable */
    public const LABEL = "random";

    /** Identifier of target selector variable */
    public const IDENTIFIER = "r";

    /** @return string[] */
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