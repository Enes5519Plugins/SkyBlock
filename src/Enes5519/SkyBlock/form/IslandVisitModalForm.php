<?php

declare(strict_types=1);

namespace Enes5519\SkyBlock\form;

use Enes5519\SkyBlock\SkyBlock;
use pocketmine\form\Form;
use pocketmine\form\FormValidationException;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class IslandVisitModalForm implements Form{

	/** @var Player */
	private $visiting;

	public function __construct(Player $visiting){
		$this->visiting = $visiting;
	}

	/**
	 * @inheritDoc
	 */
	public function handleResponse(Player $player, $data) : void{
		if(!is_bool($data)){
			throw new FormValidationException("Expected bool, got " . gettype($data));
		}

		$this->onSubmit($player, $data);
	}

	public function onSubmit(Player $player, bool $data) : void{
		if($data){
			if($this->visiting->isClosed()){
				return;
			}

			$this->visiting->teleport(SkyBlock::getAPI()->getProvider()->getIsland($player->getLowerCaseName())->getSpawnPoint());
			$this->visiting->sendMessage(SkyBlock::PREFIX . "Adaya ışınlandınız!");
		}else{
			$this->visiting->sendMessage(SkyBlock::PREFIX . $player->getName() . " sizi adasına istemedi!");
		}
	}

	/**
	 * @inheritDoc
	 */
	public function jsonSerialize(){
		return [
			"type" => "modal",
			"title" => "Ada Ziyaret",
			"content" => $this->visiting->getName() . " adanızı ziyaret etmek istiyor.",
			"button1" => TextFormat::GREEN . "Kabul Et",
			"button2" => TextFormat::RED . "Reddet"
		];
	}
}