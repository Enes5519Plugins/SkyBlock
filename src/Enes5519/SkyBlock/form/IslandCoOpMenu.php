<?php

declare(strict_types=1);

namespace Enes5519\SkyBlock\form;

use pocketmine\form\Form;
use pocketmine\form\FormValidationException;
use pocketmine\Player;

class IslandCoOpMenu implements Form{
	/** @var MenuOption[] */
	private $options = [];

	public function __construct(){
		$this->options = [
			new MenuOption( "Ortak Ekle"),
			new MenuOption( "Ortak Kaldır"),
			new MenuOption( "Ada İzinleri"),
			new MenuOption("Ortaklıklar")
		];
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
		}
	}

	private function onSubmit(Player $player, int $data){
		switch($data){
			case 0:
				$player->sendForm(new IslandCoOpAddForm($player));
				break;
			case 1:
				$player->sendForm(new IslandCoOpRemoveForm($player));
				break;
			case 2:
				$player->sendForm(new IslandCoOpPermissionsForm($player));
				break;
			case 3:
				$player->sendForm(new IslandCoOpOthersForm($player));
				break;
		}
	}

	/**
	 * @inheritDoc
	 */
	public function jsonSerialize(){
		return [
			"type" => "form",
			"title" => "Ortaklar",
			"content" => "",
			"buttons" => $this->options
		];
	}
}