<?php

namespace Enes5519\SkyBlock\form;

use Enes5519\SkyBlock\SkyBlock;
use pocketmine\form\Form;
use pocketmine\form\FormValidationException;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class IslandKickMenu implements Form{

	/** @var MenuOption[] */
	private $options = [];

	public function __construct(Player $player){
		foreach(SkyBlock::getAPI()->getProvider()->getIsland($player->getLowerCaseName())->getSpawnPoint()->getLevel()->getPlayers() as $sbPlayer){
			if($sbPlayer->getId() !== $player->getId()){
				$this->options[] = (new MenuOption(TextFormat::RED . $sbPlayer->getName()))->setExtra($sbPlayer);
			}
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
		}
	}

	private function onSubmit(Player $player, int $data){
		/** @var Player $kickPlayer */
		$kickPlayer = $this->options[$data]->getExtra();
		if($kickPlayer->getLevel()->getId() === SkyBlock::getAPI()->getProvider()->getSkyBlockPlayer($player->getLowerCaseName())->getIsland()->getSpawnPoint()->getLevel()->getId()){
			$kickPlayer->teleport($kickPlayer->getServer()->getDefaultLevel()->getSpawnLocation());
			$kickPlayer->sendMessage(SkyBlock::PREFIX . $player->getName() . ' sizi adanızdan tekmeledi!');
			$player->sendMessage(SkyBlock::PREFIX . "Oyuncu adadan tekmelendi!");
		}
	}

	/**
	 * @inheritDoc
	 */
	public function jsonSerialize(){
		return [
			"type" => "form",
			"title" => "Adandan Oyuncu Tekmele",
			"content" => "",
			"buttons" => $this->options
		];
	}
}