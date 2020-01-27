<?php

declare(strict_types=1);

namespace Enes5519\SkyBlock\form;

use Enes5519\SkyBlock\SkyBlock;
use pocketmine\form\Form;
use pocketmine\form\FormValidationException;
use pocketmine\Player;

class IslandCoOpRemoveForm implements Form{

	/** @var MenuOption[] */
	private $options = [];

	public function __construct(Player $player){
		$island = SkyBlock::getAPI()->getProvider()->getSkyBlockPlayer($player->getLowerCaseName())->getIsland();

		foreach($island->getCoOps() as $coOpName => $_){
			$this->options[] = (new MenuOption($coOpName))->setExtra($coOpName);
		}
	}

	/**
	 * @inheritDoc
	 */
	public function handleResponse(Player $player, $data): void{
		if(is_int($data)){
			if(!isset($this->options[$data])){
				throw new FormValidationException("Option $data does not exist");
			}
			$this->onSubmit($player, $data);
		}else{
			throw new FormValidationException("Expected int or null, got " . gettype($data));
		}
	}

	private function onSubmit(Player $player, int $data){
		$coOpName = $this->options[$data]->getExtra();

		$player->sendForm(new ModalForm("Ortak Kaldır", $coOpName . " isimli ortağınızı kaldırmak istediğinize emin misiniz?", function(Player $player, bool $data) use($coOpName){
			if($data){
				$sbPlayer = SkyBlock::getAPI()->getProvider()->getSkyBlockPlayer($player->getLowerCaseName());
				$sbPlayer->getIsland()->kickCoOp($coOpName);
			}
		}));
	}

	/**
	 * @inheritDoc
	 */
	public function jsonSerialize(){
		return [
			"type" => "form",
			"title" => "Ortak Kaldır",
			"content" => "",
			"buttons" => $this->options
		];
	}
}