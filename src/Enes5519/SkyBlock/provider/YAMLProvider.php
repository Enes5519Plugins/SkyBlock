<?php

declare(strict_types=1);

namespace Enes5519\SkyBlock\provider;

use Enes5519\SkyBlock\Island;
use Enes5519\SkyBlock\SkyBlock;
use Enes5519\SkyBlock\SkyBlockPlayer;
use Enes5519\SkyBlock\utils\Utils;
use pocketmine\Player;
use pocketmine\utils\Config;

class YAMLProvider extends DataProvider{

	public const PLAYER_DEFAULTS = [
		self::PLAYER_CO_OPS => [],
		self::PLAYER_BANNED_TIMESTAMP => 0
	];

	/** @var string */
	private $path;

	/** @var Config[] */
	private $configs;
	/** @var SkyBlockPlayer[] */
	private $skyBlockPlayers;

	public function load(SkyBlock $sb) : void{
		$this->path = $sb->getDataFolder() . "islands" . DIRECTORY_SEPARATOR;

		if(!file_exists($this->path)){
			mkdir($this->path);
		}
	}

	public function registerPlayer(string $name) : void{
		if(!isset($this->configs[$name])){
			$this->configs[$name] = new Config($this->path . $name . '.yml', Config::YAML, self::PLAYER_DEFAULTS);
		}
	}

	public function getSkyBlockPlayer(string $name) : SkyBlockPlayer{
		$this->registerPlayer($name);

		if(!isset($this->skyBlockPlayers[$name])){
			$this->skyBlockPlayers[$name] = new SkyBlockPlayer($name, $this->configs[$name]->getAll());
		}

		return $this->skyBlockPlayers[$name];
	}

	public function createIsland(Player $player) : int{
		$skyBlockPlayer = $this->getSkyBlockPlayer($name = $player->getLowerCaseName());
		if($skyBlockPlayer->getIsland() === null){
			$level = SkyBlock::extractIslandMap($name);
			$skyBlockPlayer->setIsland(new Island($skyBlockPlayer, [], $level->getSpawnLocation(), [], true));
			$skyBlockPlayer->setBannedTimestamp(strtotime('+1 week'));
			$skyBlockPlayer->save();
			return self::ERROR_NONE;
		}else{
			return self::ERROR_ALREADY_EXISTS;
		}
	}

	public function deleteIsland(Player $player) : int{
		$skyBlockPlayer = $this->getSkyBlockPlayer($name = $player->getLowerCaseName());
		if($skyBlockPlayer->getIsland() === null){
			return self::ERROR_NOT_FOUND;
		}else{
			if($skyBlockPlayer->checkBan()){
				return self::ERROR_HAVE_BAN;
			}

			$player->getServer()->unloadLevel($skyBlockPlayer->getIsland()->getSpawnPoint()->getLevel());

			$skyBlockPlayer->setIsland(null);
			$skyBlockPlayer->save();

			Utils::deleteDir($player->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . $player->getLowerCaseName());

			return self::ERROR_NONE;
		}
	}

	public function saveSkyBlockPlayer(SkyBlockPlayer $player) : void{
		$this->configs[$player->getName()]->setAll($player->toArray());
		$this->configs[$player->getName()]->save();
	}
}