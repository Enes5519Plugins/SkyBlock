<?php

declare(strict_types=1);

namespace Enes5519\SkyBlock;

use Enes5519\SkyBlock\provider\DataProvider;
use Enes5519\SkyBlock\provider\YAMLProvider;
use pocketmine\level\Level;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class SkyBlock extends PluginBase{

	public const PREFIX = TextFormat::GREEN . 'SkyBlock ' . TextFormat::DARK_GRAY . '> ' . TextFormat::GRAY;

	/** @var SkyBlock */
	private static $api;

	/** @var DataProvider */
	private $provider;

	public function onEnable(){
		self::$api = $this;

		if(!file_exists($this->getDataFolder())){
			mkdir($this->getDataFolder());
		}

		if(!file_exists($this->getDataFolder() . "world.zip")){
			throw new \CompileError("world.zip dosyası bulunamadı!");
		}

		$this->setProvider(new YAMLProvider($this));
	}

	public static function extractIslandMap(string $name) : Level{
		$zip = new \ZipArchive();
		$zip->open(self::$api->getDataFolder() . "world.zip");
		mkdir(Server::getInstance()->getDataPath() . "worlds/$name");
		$zip->extractTo(Server::getInstance()->getDataPath() . "worlds/$name");
		$zip->close();

		return self::getLevel($name);
	}

	public static function getLevel(string $name) : Level{
		Server::getInstance()->loadLevel($name);
		return Server::getInstance()->getLevelByName($name);
	}

	/**
	 * @return $this
	 */
	public static function getAPI() : SkyBlock{
		return self::$api;
	}

	/**
	 * @return DataProvider
	 */
	public function getProvider() : DataProvider{
		return $this->provider;
	}

	/**
	 * @param DataProvider $provider
	 */
	public function setProvider(DataProvider $provider) : void{
		$this->provider = $provider;
	}

}