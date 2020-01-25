<?php

declare(strict_types=1);

namespace Enes5519\SkyBlock\form;

use Enes5519\SkyBlock\SkyBlock;
use pocketmine\form\Form;
use pocketmine\form\FormValidationException;
use pocketmine\Player;

class IslandMenu implements Form{

	/** @var MenuOption[] */
	private $options;

	public function __construct(Player $player){
		$island = SkyBlock::getAPI()->getProvider()->getIsland($player->getLowerCaseName());
		$this->options = [
			new MenuOption('Adana Işınlan'),
			new MenuOption('Ada Doğma Noktası Ayarla'),
			new MenuOption('Ortak Menüsü'),
			new MenuOption('Ziyaret: ' . ($island->isVisitEnabled() ? "AÇIK" : "KAPALI")),
			new MenuOption('Ziyaret Et'),
			new MenuOption('Oyuncu Tekmele'),
			new MenuOption('Ada Sil')
		];
	}

	private function onSubmit(Player $player, int $data) : void{
		switch($data){
			case 0:
				$island = SkyBlock::getAPI()->getProvider()->getIsland($player->getLowerCaseName());
				$player->teleport($island->getSpawnPoint());
				$player->sendMessage(SkyBlock::PREFIX . "Adanıza ışınlandınız!");
				break;
			case 1:
				$island = SkyBlock::getAPI()->getProvider()->getIsland($player->getLowerCaseName());
				if($player->getLevel()->getId() !== $island->getSpawnPoint()->getLevel()->getId()){
					$player->sendMessage(SkyBlock::PREFIX . "Doğma noktası ayarlamak için adanızda olmalısınız!");
				}else{
					$island->setSpawnPoint($player->asPosition());
					$player->sendMessage(SkyBlock::PREFIX . "Doğma noktası ayarlandı!");
				}
				break;
			case 2:
				// TODO : ORTAK UI
				break;
			case 3:
				$island = SkyBlock::getAPI()->getProvider()->getIsland($player->getLowerCaseName());
				$island->setVisitEnabled(!$island->isVisitEnabled());
				$player->sendMessage(SkyBlock::PREFIX . "Ada ziyareti " . ($island->isVisitEnabled() ? "açıldı." : "kapandı."));
				break;
			case 4:
				$player->sendForm(new IslandVisitForm($player));
				break;
			case 5:
				$player->sendForm(new IslandKickMenu($player));
				break;
			case 6:
				$player->sendForm(new IslandDeleteForm($player));
				break;
		}
	}

	public function handleResponse(Player $player, $data) : void{
		if(is_int($data)){
			if(!isset($this->options[$data])){
				throw new FormValidationException("Option $data does not exist");
			}
			$this->onSubmit($player, $data);
		}else{
			throw new FormValidationException("Expected int or null, got " . gettype($data));
		}
	}

	/**
	 * @inheritDoc
	 */
	public function jsonSerialize(){
		return [
			"type" => "form",
			"title" => "SkyBlock Menü",
			"content" => "",
			"buttons" => $this->options
		];
	}
}