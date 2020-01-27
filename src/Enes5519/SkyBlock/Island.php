<?php

declare(strict_types=1);

namespace Enes5519\SkyBlock;

use Enes5519\SkyBlock\provider\DataProvider;
use Enes5519\SkyBlock\utils\Utils;
use pocketmine\level\Position;

class Island{

	/** @var SkyBlockPlayer */
	private $owner;
	/** @var array */
	private $coOps;
	/** @var Position */
	private $spawnPoint;
	/** @var IslandPermission */
	private $permission;
	/** @var bool */
	private $visitEnabled;

	public function __construct(SkyBlockPlayer $owner, array $data){
		$this->owner = $owner;
		$this->coOps = array_flip($data[DataProvider::ISLAND_CO_OPS]); // for performance
		$this->spawnPoint = Utils::decodePosition($data[DataProvider::ISLAND_SPAWN_POINT]);
		$this->permission = new IslandPermission($this, $data[DataProvider::ISLAND_PERMISSIONS]);
		$this->visitEnabled = $data[DataProvider::ISLAND_VISIT_ENABLED];
	}

	/**
	 * @return SkyBlockPlayer
	 */
	public function getOwner() : SkyBlockPlayer{
		return $this->owner;
	}

	/**
	 * @return IslandPermission
	 */
	public function getPermission() : IslandPermission{
		return $this->permission;
	}

	public function isCoOp(string $name) : bool{
		return isset($this->coOps[$name]);
	}

	/**
	 * @return array
	 */
	public function getCoOps() : array{
		return $this->coOps;
	}

	public function addCoOp(string $coOpName) : void{
		if(isset($this->coOps[$coOpName])){
			return;
		}

		$this->coOps[$coOpName] = true;
		SkyBlock::getAPI()->getProvider()->getSkyBlockPlayer($coOpName)->addCoOp($this->owner->getName());
		$this->save();
	}

	public function kickCoOp(string $coOpName) : void{
		unset($this->coOps[$coOpName]);
		SkyBlock::getAPI()->getProvider()->getSkyBlockPlayer($coOpName)->removeCoOp($this->owner->getName());
		$this->save();
	}

	/**
	 * @return bool
	 */
	public function isVisitEnabled() : bool{
		return $this->visitEnabled;
	}

	/**
	 * @param bool $visitEnabled
	 */
	public function setVisitEnabled(bool $visitEnabled) : void{
		$this->visitEnabled = $visitEnabled;
		$this->save();
	}

	/**
	 * @return Position
	 */
	public function getSpawnPoint() : Position{
		return $this->spawnPoint;
	}

	/**
	 * @param Position $spawnPoint
	 */
	public function setSpawnPoint(Position $spawnPoint) : void{
		$this->spawnPoint->getLevel()->setSpawnLocation($spawnPoint);
		$this->spawnPoint = $spawnPoint;
		$this->save();
	}

	public function save() : void{
		$this->owner->save();
	}

	public function toArray() : array{
		return [
			DataProvider::ISLAND_CO_OPS => array_keys($this->coOps),
			DataProvider::ISLAND_SPAWN_POINT => Utils::encodePosition($this->spawnPoint),
			DataProvider::ISLAND_PERMISSIONS => $this->permission->toArray(),
			DataProvider::ISLAND_VISIT_ENABLED => $this->visitEnabled
		];
	}
}