<?php

declare(strict_types=1);

namespace Enes5519\SkyBlock;

use Enes5519\SkyBlock\command\IslandCommand;
use Enes5519\SkyBlock\provider\DataProvider;
use Enes5519\SkyBlock\provider\YAMLProvider;
use pocketmine\level\format\io\BaseLevelProvider;
use pocketmine\level\Level;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class SkyBlock extends PluginBase{

	public const PERMISSION_OP = "enes5519.skyblock.op";

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

		$this->saveDefaultConfig();

		$this->setProvider(new YAMLProvider($this));

		$this->getServer()->getPluginManager()->registerEvents(new EventListener($this, $this->getConfig()->get("worlds-of-server")), $this);
		$this->getServer()->getCommandMap()->register("SkyBlock", new IslandCommand());
	}

	public static function extractIslandMap(string $name) : Level{
		$zip = new \ZipArchive();
		$zip->open(self::$api->getDataFolder() . "world.zip");
		mkdir(Server::getInstance()->getDataPath() . "worlds/$name");
		$zip->extractTo(Server::getInstance()->getDataPath() . "worlds/$name");
		$zip->close();

		return self::fixLevelName(self::getLevel($name), $name);
	}

	public static function fixLevelName(Level $level, string $name) : Level{
		/** @var BaseLevelProvider $provider */
		$provider = $level->getProvider();
		$provider->getLevelData()->setString("LevelName", $name);
		return $level;
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