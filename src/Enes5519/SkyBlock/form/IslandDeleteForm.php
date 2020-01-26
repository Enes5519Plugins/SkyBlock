<?php

namespace Enes5519\SkyBlock\form;

use Enes5519\SkyBlock\provider\DataProvider;
use Enes5519\SkyBlock\SkyBlock;
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
			$error = SkyBlock::getAPI()->getProvider()->deleteIsland($player);
			if($error === DataProvider::ERROR_NONE){
				$player->sendMessage(SkyBlock::PREFIX . "Adanız silindi!");
			}else{
				$player->sendMessage(SkyBlock::PREFIX . "Adanız zaten bulunmamakta.");
			}
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