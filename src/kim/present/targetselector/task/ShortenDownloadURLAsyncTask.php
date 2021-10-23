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

namespace kim\present\targetselector\task;

use Exception;
use kim\present\targetselector\TargetSelector;
use pocketmine\scheduler\AsyncTask;

use function curl_close;
use function curl_exec;
use function curl_init;
use function curl_setopt_array;
use function explode;
use function is_string;
use function str_starts_with;
use function strlen;
use function substr;

/** @internal */
final class ShortenDownloadURLAsyncTask extends AsyncTask{
    private const URL = "https://git.io";

    public function __construct(
        private string $fileName,
        private string $downloadURL
    ){
    }

    public function onRun() : void{
        try{
            curl_setopt_array($curlHandle = curl_init(), [
                CURLOPT_URL => self::URL,
                CURLOPT_POSTFIELDS => [
                    "code" => $this->fileName,
                    "url" => $this->downloadURL
                ],
                CURLOPT_HEADER => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false
            ]);
            foreach(explode("\n", curl_exec($curlHandle)) as $line){
                if(str_starts_with($line, "Location: ")){
                    $this->setResult(substr($line, strlen("Location: ")));
                }
            }
            curl_close($curlHandle);
        }catch(Exception){
        }
    }

    public function onCompletion() : void{
        $result = $this->getResult();
        if(is_string($result)){
            $plugin = TargetSelector::getInstance();
            $plugin->getLogger()->warning("latest release link : $result");
        }
    }
}