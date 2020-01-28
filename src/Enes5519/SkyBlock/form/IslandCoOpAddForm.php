<?php

declare(strict_types=1);

namespace Enes5519\SkyBlock\form;

use Enes5519\SkyBlock\SkyBlock;
use pocketmine\form\Form;
use pocketmine\form\FormValidationException;
use pocketmine\Player;

class IslandCoOpAddForm implements Form{

	/** @var MenuOption[] */
	private $options = [];

	public function __construct(Player $player){
		$island = SkyBlock::getAPI()->getProvider()->getSkyBlockPlayer($player->getLowerCaseName())->getIsland();

		foreach($player->getServer()->getOnlinePlayers() as $coOp){
			if(!$island->isCoOp($coOp->getLowerCaseName()) and $coOp->getId() !== $player->getId()){
				$this->options[] = (new MenuOption($coOp->getName()))->setExtra($coOp);
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
		/** @var Player $coOp */
		$coOp = $this->options[$data]->getExtra();

		if($coOp->isClosed()){
			$player->sendMessage(SkyBlock::PREFIX . $coOp->getName() . ' oyundan ayrılmış...');
		}else{
			$modal = new ModalForm('Ortaklık İsteği', $player->getName() . ' isimli oyuncu sizi adasına ortak eklemek istiyor.', function(Player $coOp, bool $data) use($player){
				if($data){
					SkyBlock::getAPI()->getProvider()->getIsland($player->getLowerCaseName())->addCoOp($coOp->getLowerCaseName());
					$player->sendMessage(SkyBlock::PREFIX . $coOp->getName() . ' artık ortağınız!');
				}else{
					$player->sendMessage(SkyBlock::PREFIX . $coOp->getName() . ' ortaklık teklifini reddetti!');
				}
			});
			$coOp->sendForm($modal);
			$player->sendMessage(SkyBlock::PREFIX . $coOp->getName() . ' isimli oyuncuya ortaklık teklifiniz gönderildi!');
		}
	}

	/**
	 * @inheritDoc
	 */
	public function jsonSerialize(){
		return [
			"type" => "form",
			"title" => "Ortak Ekle",
			"content" => "",
			"buttons" => $this->options
		];
	}
}