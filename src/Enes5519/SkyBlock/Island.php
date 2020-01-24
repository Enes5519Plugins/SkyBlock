<?php

declare(strict_types=1);

namespace Enes5519\SkyBlock;

use Enes5519\SkyBlock\utils\Utils;
use pocketmine\level\Location;

class Island{

	/** @var string */
	private $name;
	/** @var array */
	private $coOps;
	/** @var Location */
	private $spawnPoint;
	/** @var array TODO Permission class */
	private $permissions;
	/** @var bool */
	private $visitEnabled;

	public function __construct(string $name, array $coOps, Location $spawnPoint, array $permissions, bool $visitEnabled){
		$this->name = $name;
		$this->coOps = $coOps;
		$this->spawnPoint = $spawnPoint;
		$this->permissions = $permissions;
		$this->visitEnabled = $visitEnabled;
	}

	/**
	 * @return Location
	 */
	public function getSpawnPoint() : Location{
		return $this->spawnPoint;
	}

	public static function fromArray(string $name, array $islandData) : Island{
		return new Island($name, $islandData["coOps"], Utils::decodeLocation($islandData["spawnPoint"]), $islandData["permissions"], $islandData["visibleEnabled"]);
	}

	public function toArray() : array{
		return [
			"coOps" => $this->coOps,
			"spawnPoint" => Utils::encodeLocation($this->spawnPoint),
			"permissions" => $this->permissions,
			"visitEnabled" => $this->visitEnabled
		];
	}
}