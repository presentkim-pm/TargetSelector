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

namespace kim\present\targetselector;

use kim\present\targetselector\listener\CommandEventListener;
use kim\present\targetselector\task\CheckUpdateAsyncTask;
use kim\present\targetselector\variable\AllVariable;
use kim\present\targetselector\variable\PlayerVariable;
use kim\present\targetselector\variable\RandomVariable;
use kim\present\targetselector\variable\Variable;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\permission\PermissionParser;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

use function array_pop;
use function count;
use function fclose;
use function file_exists;
use function fopen;
use function is_string;
use function mkdir;
use function stream_copy_to_stream;

final class TargetSelector extends PluginBase{
    use SingletonTrait;

    /** @var Variable[] */
    private array $variables = [];

    protected function onLoad() : void{
        self::$instance = $this;
    }

    protected function onEnable() : void{
        //Load config file
        $this->saveDefaultConfig();
        $this->reloadConfig();
        $config = $this->getConfig();

        //Check latest version
        if($config->getNested("settings.update-check", false)){
            $this->getServer()->getAsyncPool()->submitTask(new CheckUpdateAsyncTask());
        }

        //Register default variable
        $permManager = PermissionManager::getInstance();
        $opRoot = $permManager->getPermission(DefaultPermissions::ROOT_OPERATOR);
        $everyoneRoot = $permManager->getPermission(DefaultPermissions::ROOT_USER);
        /** @var $variable Variable */
        foreach([new PlayerVariable(), new RandomVariable(), new AllVariable()] as $variable){
            $this->registerVariable($variable);

            $permissionName = $variable->getPermission();
            $permission = new Permission($permissionName, "Target Selector - @" . $variable::IDENTIFIER);
            $permManager->removePermission($permission);
            $opRoot->removeChild($permissionName);
            $everyoneRoot->removeChild($permissionName);

            $permManager->addPermission($permission);
            //Load permission's default value from config
            $defaultValue = $config->getNested("permission." . $variable::LABEL);
            if(is_string($defaultValue)){
                match (PermissionParser::defaultFromString($defaultValue)) {
                    PermissionParser::DEFAULT_TRUE => $everyoneRoot->addChild($permissionName, true),
                    PermissionParser::DEFAULT_OP => $opRoot->addChild($permissionName, true),
                    PermissionParser::DEFAULT_NOT_OP => $everyoneRoot->addChild($permissionName, true) | $opRoot->addChild($permissionName, false)
                };
            }
        }

        //Register event listeners
        $this->getServer()->getPluginManager()->registerEvents(new CommandEventListener($this), $this);
    }

    /** @return Variable[] */
    public function getVariables() : array{
        return $this->variables;
    }

    public function registerVariable(Variable $variable, bool $replace = false) : bool{
        $identifier = $variable::IDENTIFIER;
        if(isset($this->variables[$identifier]) && !$replace){
            return false;
        }
        $this->variables[$identifier] = $variable;
        return true;
    }

    public function unregisterVariable(string $identifier) : bool{
        if(isset($this->variables[$identifier])){
            unset($this->variables[$identifier]);
            return true;
        }
        return false;
    }

    /** @Override for multilingual support of the config file */
    public function saveDefaultConfig() : bool{
        $resource = $this->getResource("lang/{$this->getServer()->getLanguage()->getLang()}/config.yml");
        if($resource === null){
            $resource = $this->getResource("lang/eng/config.yml");
        }

        $dataFolder = $this->getDataFolder();
        $configFile = "{$dataFolder}config.yml";
        if(!file_exists($configFile)){
            if(!file_exists($dataFolder)){
                mkdir($dataFolder, 0755, true);
            }
            $ret = stream_copy_to_stream($resource, $fp = fopen($configFile, "wb")) > 0;
            fclose($fp);
            fclose($resource);
            return $ret;
        }
        return false;
    }

    /** @return string[] */
    public function parseCommand(string $command, CommandSender $sender) : array{
        foreach($this->variables as $variable){
            if(!$variable->validate($command)){
                continue;
            }
            $results = $variable->parse($command, $sender);
            if(count($results) === 1){
                return $this->parseCommand(array_pop($results), $sender);
            }else{
                $allResult = [];
                foreach($results as $result){
                    $allResult += $this->parseCommand($result, $sender);
                }
                return $allResult;
            }
        }
        return [$command];
    }
}