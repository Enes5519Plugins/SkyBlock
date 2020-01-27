<?php

declare(strict_types=1);

namespace Enes5519\SkyBlock;

use Enes5519\SkyBlock\provider\DataProvider;
use Enes5519\SkyBlock\utils\Utils;
use pocketmine\level\Position;

class Island{

	public static function fromArray(SkyBlockPlayer $player, array $data) : Island{
		return new Island(
			$player,
			$data[DataProvider::ISLAND_CO_OPS],
			Utils::decodePosition($data[DataProvider::ISLAND_SPAWN_POINT]),
			$data[DataProvider::ISLAND_PERMISSIONS],
			$data[DataProvider::ISLAND_VISIT_ENABLED]
		);
	}

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

	public function __construct(SkyBlockPlayer $owner, array $coOps, Position $spawnPoint, array $permissions, bool $visitEnabled){
		$this->owner = $owner;
		$this->coOps = array_flip($coOps); // for performance
		$this->spawnPoint = $spawnPoint;
		$this->permission = new IslandPermission($this, $permissions);
		$this->visitEnabled = $visitEnabled;
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