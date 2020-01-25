<?php

declare(strict_types=1);

namespace Enes5519\SkyBlock\provider;

use Enes5519\SkyBlock\Island;
use Enes5519\SkyBlock\SkyBlock;
use pocketmine\Player;

abstract class DataProvider{

	public const ERROR_NONE = 0;
	public const ERROR_ALREADY_EXISTS = 1;
	public const ERROR_NOT_FOUND = 2;
	public const ERROR_HAVE_BAN = 3;

	public function __construct(SkyBlock $sb){
		$this->load($sb);
	}

	abstract public function load(SkyBlock $sb) : void;

	abstract public function createIsland(Player $player) : int;

	abstract public function setIslandOption(Island $island, string $key, $data);

	abstract public function deleteIsland(Player $player) : int;

	abstract public function getIsland(string $name) : Island;
}