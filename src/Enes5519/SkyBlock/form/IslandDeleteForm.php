<?php

namespace Enes5519\SkyBlock\form;

use pocketmine\form\Form;
use pocketmine\form\FormValidationException;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class IslandDeleteForm implements Form{

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
		if(!$data){
			// TODO : Delete Island
		}
	}

	/**
	 * @inheritDoc
	 */
	public function jsonSerialize(){
		return [
			"type" => "modal",
			"title" => "Ada Sil",
			"content" => "Adanızı silmek istediğinize emin misiniz?",
			"button1" => TextFormat::GREEN . "Hayır",
			"button2" => TextFormat::RED . "Evet"
		];
	}
}