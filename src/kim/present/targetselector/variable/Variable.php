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

use function preg_match;
use function str_replace;

abstract class Variable{
    protected const PERMISSION_PREFIX = "targetselector.";

    /** Label of target selector variable */
    public const LABEL = "";

    /** Identifier of target selector variable */
    public const IDENTIFIER = "";

    /** @return string[] */
    public function parse(string $command, CommandSender $sender) : array{
        if($sender->hasPermission($this->getPermission())){
            return $this->onParse($command, $sender);
        }

        //Filter out cases where not has permission
        return [str_replace($this->toString(), "PERMISSION_DENIED", $command)];
    }

    /** @return string[] */
    abstract protected function onParse(string $command, CommandSender $sender) : array;

    public function validate(string $command) : bool{
        return (bool) preg_match($this->getPattern(), $command, $matches);
    }

    public function getPattern() : string{
        return "/\@(?:" . static::IDENTIFIER . "\s|" . static::IDENTIFIER . "$)/i";
    }

    public function getPermission() : string{
        return self::PERMISSION_PREFIX . static::LABEL;
    }

    public function toString() : string{
        return "@" . static::IDENTIFIER;
    }
}