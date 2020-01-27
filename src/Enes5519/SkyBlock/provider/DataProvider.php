<?php

declare(strict_types=1);

namespace Enes5519\SkyBlock\provider;

use Enes5519\SkyBlock\Island;
use Enes5519\SkyBlock\SkyBlock;
use Enes5519\SkyBlock\SkyBlockPlayer;
use pocketmine\Player;

abstract class DataProvider{

	public const ERROR_NONE = 0;
	public const ERROR_ALREADY_EXISTS = 1;
	public const ERROR_NOT_FOUND = 2;
	public const ERROR_HAVE_BAN = 3;

	public const PLAYER_CO_OPS = 'coOps';
	public const PLAYER_ISLAND = 'island';
	public const PLAYER_BANNED_TIMESTAMP = 'bannedTimestamp';

	public const ISLAND_CO_OPS = 'coOps';
	public const ISLAND_SPAWN_POINT = 'spawnPoint';
	public const ISLAND_PERMISSIONS = 'permissions';
	public const ISLAND_VISIT_ENABLED = 'visitEnabled';

	public function __construct(SkyBlock $sb){
		$this->load($sb);
	}

	abstract public function load(SkyBlock $sb) : void;

	abstract public function registerPlayer(string $name) : void;

	abstract public function getSkyBlockPlayer(string $name) : SkyBlockPlayer;

	abstract public function saveSkyBlockPlayer(SkyBlockPlayer $player) : void;

	abstract public function createIsland(Player $player) : int;

	abstract public function deleteIsland(Player $player) : int;

	public function getIsland(string $name) : Island{
		return $this->getSkyBlockPlayer($name)->getIsland();
	}
}