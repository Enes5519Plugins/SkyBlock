<?php

declare(strict_types=1);

namespace Enes5519\SkyBlock\provider;

use Enes5519\SkyBlock\Island;
use Enes5519\SkyBlock\SkyBlock;
use Enes5519\SkyBlock\utils\Utils;
use pocketmine\level\Location;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Config;

class YAMLProvider extends DataProvider{

	/** @var string */
	private $path;

	/** @var Config[] */
	private $configs;
	/** @var Island[] */
	private $islands;

	public function load(SkyBlock $sb) : void{
		$this->path = $sb->getDataFolder() . "islands" . DIRECTORY_SEPARATOR;

		if(!file_exists($this->path)){
			mkdir($this->path);
		}
	}

	public function createIsland(Player $player) : int{
		if($this->getConfig($name = $player->getLowerCaseName()) === null){
			$level = SkyBlock::extractIslandMap($name);
			$this->islands[$name] = new Island($name, [], Location::fromObject($level->getProvider()->getSpawn(), $level), [], true);
			$this->configs[$name] = new Config($this->path . $name . ".yaml", Config::YAML, [
				"coOps" => [],
				"island" => $this->islands[$name]->toArray(),
				"bannedTimestamp" => 0
			]);
			return self::ERROR_NONE;
		}elseif(Server::getInstance()->isLevelGenerated($player->getLowerCaseName())){
			return self::ERROR_ALREADY_EXISTS;
		}else{
			$timestamp = $this->configs[$name]->get('bannedTimestamp', 0);
			if(time() > $timestamp){
				$level = SkyBlock::extractIslandMap($name);
				$this->islands[$name] = new Island($name, [], Location::fromObject($level->getProvider()->getSpawn(), $level), [], true);
				$this->configs[$name]->set("island", $this->islands[$name]->toArray());
				$this->configs[$name]->set("bannedTimestamp", 0);
				$this->configs[$name]->save();
				return self::ERROR_NONE;
			}

			return self::ERROR_HAVE_BAN;
		}
	}

	public function deleteIsland(Player $player) : int{
		if($this->getConfig($player->getLowerCaseName()) === null){
			return self::ERROR_NOT_FOUND;
		}else{
			$config = $this->configs[$player->getLowerCaseName()];
			$config->remove("island");
			$config->set("bannedTimestamp", strtotime("+1 week"));
			$config->save();

			Utils::deleteDir($player->getServer()->getDataPath() . "worlds" . DIRECTORY_SEPARATOR . $player->getLowerCaseName());

			return self::ERROR_NONE;
		}
	}

	public function getIsland(string $name) : Island{
		if(!isset($this->islands[$name])){
			$this->islands[$name] = Island::fromArray($name, $this->getConfig($name)->getAll()["island"]);
		}

		return $this->islands[$name];
	}

	public function getConfig(string $name) : ?Config{
		if(file_exists($this->path . $name . ".yaml")){
			$this->configs[$name] = new Config($this->path . $name . ".yaml", Config::YAML);
			return $this->configs[$name];
		}

		return null;
	}
}