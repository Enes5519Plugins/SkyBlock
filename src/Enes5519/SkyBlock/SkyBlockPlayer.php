<?php

declare(strict_types=1);

namespace Enes5519\SkyBlock;

use Enes5519\SkyBlock\provider\DataProvider;

class SkyBlockPlayer{

	/** @var string */
	private $name;
	/**
	 * Ortak olduğu adaları listeler
	 * @var array
	 */
	private $coOps;
	/** @var Island|null */
	private $island;
	/**
	 * Ada sildiyse oluşturmak için geçerli olan süre
	 * @var int
	 */
	private $bannedTimestamp;

	public function __construct(string $name, array $data){
		$this->name = $name;
		$this->coOps = $data[DataProvider::PLAYER_CO_OPS] ?? [];

		if(!empty($data[DataProvider::PLAYER_ISLAND])){
			$this->island = new Island($this, $data[DataProvider::PLAYER_ISLAND]);
		}

		$this->bannedTimestamp = $data[DataProvider::PLAYER_BANNED_TIMESTAMP] ?? 0;
	}

	/**
	 * @return string
	 */
	public function getName() : string{
		return $this->name;
	}

	/**
	 * @return array
	 */
	public function getCoOps() : array{
		return $this->coOps;
	}

	public function addCoOp(string $name) : void{
		$this->coOps[] = $name;
		$this->save();
	}

	public function removeCoOp(string $name) : void{
		unset($this->coOps[array_search($name, $this->coOps, true)]);
		$this->save();
	}

	/**
	 * @return Island|null
	 */
	public function getIsland() : ?Island{
		return $this->island;
	}


	/**
	 * @param Island|null $island
	 */
	public function setIsland(?Island $island) : void{
		$this->island = $island;
	}

	/**
	 * @return int
	 */
	public function getBannedTimestamp() : int{
		return $this->bannedTimestamp;
	}

	/**
	 * @param int $bannedTimestamp
	 */
	public function setBannedTimestamp(int $bannedTimestamp) : void{
		$this->bannedTimestamp = $bannedTimestamp;
	}

	public function checkBan() : bool{
		if($this->bannedTimestamp === 0){
			return false;
		}

		if(time() >= $this->bannedTimestamp){
			$this->setBannedTimestamp(0);
			return false;
		}

		return true;
	}

	public function save() : void{
		SkyBlock::getAPI()->getProvider()->saveSkyBlockPlayer($this);
	}

	public function toArray() : array{
		$array = [
			DataProvider::PLAYER_CO_OPS => $this->coOps,
			DataProvider::PLAYER_BANNED_TIMESTAMP => $this->bannedTimestamp
		];

		if($this->island !== null){
			$array[DataProvider::PLAYER_ISLAND] = $this->island->toArray();
		}

		return $array;
	}
}