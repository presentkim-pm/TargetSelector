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
use function curl_getinfo;
use function curl_init;
use function curl_setopt;
use function curl_setopt_array;
use function explode;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function json_decode;
use function json_encode;
use function str_starts_with;
use function strlen;
use function strpos;
use function substr;
use function substr_compare;
use function version_compare;

/** @internal */
final class CheckUpdateAsyncTask extends AsyncTask{
    private const CACHE_ENTITY_TAG = 0;
    private const CACHE_LATEST_VERSION = 1;
    private const CACHE_FILE_NAME = 2;
    private const CACHE_DOWNLOAD_URL = 3;
    private const RELEASE_URL = "https://api.github.com/repos/PresentKim-pm/TargetSelector/releases/latest";

    /** Latest version of plugin */
    private ?string $latestVersion = null;

    /** File-name of the latest release */
    private ?string $fileName = null;

    /** File-name of the latest release */
    private ?string $downloadURL = null;

    /** Path of the latest response cache file */
    private string $cachePath;

    public function __construct(){
        $this->cachePath = TargetSelector::getInstance()->getDataFolder() . ".update_check_cache";
    }

    public function onRun() : void{
        try{
            //Initialize a cURL session and set option
            curl_setopt_array($curlHandle = curl_init(), [
                CURLOPT_URL => self::RELEASE_URL,
                CURLOPT_HEADER => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_USERAGENT => "true"
            ]);

            //Load the latest cache for prevent "API rate limit exceeded"
            $latestCache = [];
            if(file_exists($this->cachePath)){
                $latestCache = json_decode(file_get_contents($this->cachePath), true);
                curl_setopt($curlHandle, CURLOPT_HTTPHEADER, ["If-None-Match: " . $latestCache[self::CACHE_ENTITY_TAG]]);
            }

            //Perform a cURL session and get header size of session
            $response = curl_exec($curlHandle);
            $headerSize = curl_getinfo($curlHandle, CURLINFO_HEADER_SIZE);
            curl_close($curlHandle);

            //Get the latest release data from cURL response when data is modified
            $header = substr($response, 0, $headerSize);
            if(!strpos($header, "304 Not Modified")){
                foreach(explode(PHP_EOL, $header) as $line){
                    if(str_starts_with($line, "ETag: ")){
                        $latestCache[self::CACHE_ENTITY_TAG] = substr($line, strlen("ETag: "));
                    }
                }
                $jsonData = json_decode(substr($response, $headerSize), true);
                $latestCache[self::CACHE_LATEST_VERSION] = $jsonData["tag_name"];
                foreach($jsonData["assets"] as $assetData){
                    if(substr_compare($assetData["name"], ".phar", -strlen(".phar")) === 0){ //ends with ".phar"
                        $latestCache[self::CACHE_FILE_NAME] = $assetData["name"];
                        $latestCache[self::CACHE_DOWNLOAD_URL] = $assetData["browser_download_url"];
                    }
                }
            }

            //Save latest cache
            file_put_contents($this->cachePath, json_encode($latestCache));

            //Mapping the latest cache to properties values
            $this->latestVersion = $latestCache[self::CACHE_LATEST_VERSION] ?? null;
            $this->fileName = $latestCache[self::CACHE_FILE_NAME] ?? null;
            $this->downloadURL = $latestCache[self::CACHE_DOWNLOAD_URL] ?? null;
        }catch(Exception){
        }
    }

    public function onCompletion() : void{
        $plugin = TargetSelector::getInstance();
        if($this->latestVersion === null){
            $plugin->getLogger()->critical("Update check failed : Connection to release server failed");
        }elseif(version_compare($plugin->getDescription()->getVersion(), $this->latestVersion) >= 0){
            $plugin->getLogger()->notice("The plugin is latest version or higher (Latest version: {$this->latestVersion})");
        }else{
            $plugin->getLogger()->warning("The plugin is outdated. We recommend that you update your plugin. (Latest : {$this->latestVersion})");

            //Shorten download url of the latest release
            $plugin->getServer()->getAsyncPool()->submitTask(new ShortenDownloadURLAsyncTask($this->fileName, $this->downloadURL));
        }
    }
}