<?php

declare(strict_types=1);

namespace Enes5519\SkyBlock\form;

use Enes5519\SkyBlock\SkyBlock;
use pocketmine\form\Form;
use pocketmine\form\FormValidationException;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class IslandVisitForm implements Form{

	/** @var MenuOption[] */
	private $options = [];

	public function __construct(Player $player){
		foreach($player->getServer()->getOnlinePlayers() as $online){
			if($online->getId() !== $player->getId()){
				$this->options[] = (new MenuOption(TextFormat::YELLOW . $online->getName()))->setExtra($online);
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
		/** @var Player $visit */
		$visit = $this->options[$data]->getExtra();

		if($visit->isClosed()){
			$player->sendMessage(SkyBlock::PREFIX . "Oyuncu şu anda çevrimdışı.");
			return;
		}

		$visitIsland = SkyBlock::getAPI()->getProvider()->getIsland($visit->getLowerCaseName());
		if($visitIsland->isVisitEnabled()){
			$visit->sendForm(new ModalForm('Ada Ziyaret', $player->getName() . ' adanızı ziyaret etmek istiyor?', function(Player $visit, bool $data) use($player){
				if($data){
					if(!$player->isClosed()){
						$player->teleport(SkyBlock::getAPI()->getProvider()->getIsland($visit->getLowerCaseName())->getSpawnPoint());
						$player->sendMessage(SkyBlock::PREFIX . "Adaya ışınlandınız!");
					}
				}else{
					$player->sendMessage(SkyBlock::PREFIX . $visit->getName() . " sizi adasına istemedi!");
				}
			}));
		}else{
			$player->sendMessage(SkyBlock::PREFIX . "Oyuncunun adası ziyaretlere kapalı!");
		}
	}

	/**
	 * @inheritDoc
	 */
	public function jsonSerialize(){
		return [
			"type" => "form",
			"title" => "Ziyaret Et",
			"content" => "",
			"buttons" => $this->options
		];
	}
}