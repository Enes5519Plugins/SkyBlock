<?php

declare(strict_types=1);

namespace Enes5519\SkyBlock;

use Enes5519\SkyBlock\utils\Utils;
use pocketmine\level\Position;

class Island{

	/** @var string */
	private $owner;
	/** @var array */
	private $coOps;
	/** @var Position */
	private $spawnPoint;
	/** @var array TODO Permission class */
	private $permissions;
	/** @var bool */
	private $visitEnabled;

	public function __construct(string $owner, array $coOps, Position $spawnPoint, array $permissions, bool $visitEnabled){
		$this->owner = $owner;
		$this->coOps = $coOps;
		$this->spawnPoint = $spawnPoint;
		$this->permissions = $permissions;
		$this->visitEnabled = $visitEnabled;
	}

	/**
	 * @return string
	 */
	public function getOwner() : string{
		return $this->owner;
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
		SkyBlock::getAPI()->getProvider()->setIslandOption($this, "visitEnabled", $visitEnabled);
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
		SkyBlock::getAPI()->getProvider()->setIslandOption($this, "spawnPoint", Utils::encodePosition($spawnPoint));
	}

	public static function fromArray(string $name, array $islandData) : Island{
		return new Island($name, $islandData["coOps"], Utils::decodePosition($islandData["spawnPoint"]), $islandData["permissions"], $islandData["visitEnabled"]);
	}

	public function toArray() : array{
		return [
			"coOps" => $this->coOps,
			"spawnPoint" => Utils::encodePosition($this->spawnPoint),
			"permissions" => $this->permissions,
			"visitEnabled" => $this->visitEnabled
		];
	}
}