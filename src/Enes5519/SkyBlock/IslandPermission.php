<?php

declare(strict_types=1);

namespace Enes5519\SkyBlock;

class IslandPermission{

	// hepsi true en başta
	public const BLOCK_PLACE = 0;
	public const BLOCK_BREAK = 1;
	public const INTERACT = 2;
	public const PICK_ITEM = 3;

	/** @var bool[] */
	private $permissions;
	/** @var Island */
	private $island;

	public function __construct(Island $island, array $permissions){
		$this->permissions = array_merge($permissions, self::getDefaults());
		$this->island = $island;
	}

	public function hasPermission(int $permission) : bool{
		return $this->permissions[$permission];
	}

	public function changePermission(int $permission) : bool{
		$this->permissions[$permission] = !$this->permissions[$permission];
		SkyBlock::getAPI()->getProvider()->setIslandOption($this->island, "permissions", $this->permissions);
		return $this->permissions[$permission];
	}

	/**
	 * @return bool[]
	 */
	public function toArray() : array{
		return $this->permissions;
	}

	private static function getDefaults() : array{
		return [
			self::BLOCK_PLACE => true,
			self::BLOCK_BREAK => true,
			self::INTERACT => true,
			self::PICK_ITEM => true
		];
	}
}



