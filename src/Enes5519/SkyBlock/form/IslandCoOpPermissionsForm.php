<?php

declare(strict_types=1);

namespace Enes5519\SkyBlock\form;

use Enes5519\SkyBlock\IslandPermission;
use Enes5519\SkyBlock\SkyBlock;
use pocketmine\form\Form;
use pocketmine\form\FormValidationException;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class IslandCoOpPermissionsForm implements Form{

	/** @var MenuOption[] */
	private $options = [];

	public function __construct(Player $player){
		$permission = SkyBlock::getAPI()->getProvider()->getIsland($player->getLowerCaseName())->getPermission();

		$this->options = [
			new MenuOption("Blok Yerleştirme: " . $this->getText($permission->hasPermission(IslandPermission::BLOCK_PLACE))),
			new MenuOption("Blok Kırma: " . $this->getText($permission->hasPermission(IslandPermission::BLOCK_BREAK))),
			new MenuOption("Sandık Açma vs: " . $this->getText($permission->hasPermission(IslandPermission::INTERACT))),
			new MenuOption("Yerden Eşya Alma: " . $this->getText($permission->hasPermission(IslandPermission::PICKUP_ITEM)))
		];
	}

	private function getText(bool $bool) : string{
		return $bool ? TextFormat::GREEN . 'AÇIK' : TextFormat::RED . 'KAPALI';
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
		$permission = SkyBlock::getAPI()->getProvider()->getIsland($player->getLowerCaseName())->getPermission();
		$permission->changePermission($data);
		$player->sendMessage(SkyBlock::PREFIX . 'Ada izni değiştirildi.');
	}

	/**
	 * @inheritDoc
	 */
	public function jsonSerialize(){
		return [
			"type" => "form",
			"title" => "Ada İzinleri",
			"content" => "",
			"buttons" => $this->options
		];
	}

}