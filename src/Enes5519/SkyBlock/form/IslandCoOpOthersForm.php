<?php

declare(strict_types=1);

namespace Enes5519\SkyBlock\form;

use Enes5519\SkyBlock\SkyBlock;
use pocketmine\form\Form;
use pocketmine\form\FormValidationException;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class IslandCoOpOthersForm implements Form{

	/** @var MenuOption[] */
	private $options = [];
	/** @var string */
	private $content = '';

	public function __construct(Player $player){
		$coOps = SkyBlock::getAPI()->getProvider()->getSkyBlockPlayer($player->getLowerCaseName())->getCoOps();

		foreach($coOps as $coOp){
			$this->options[] = (new MenuOption($coOp))->setExtra($coOp);
		}

		if(empty($this->options)){
			$this->content = TextFormat::RED . 'Hiç bir ortaklığınız bulunmamaktadır!';
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
		$coOpName = $this->options[$data]->getExtra();
		$modal = new ModalForm('Ortaklık', $coOpName . ' adasına gitmek mi yoksa ayrılmak mı istiyorsunuz?', function(Player $player, bool $data) use($coOpName){
			$island = SkyBlock::getAPI()->getProvider()->getIsland($coOpName);
			if($data){
				$player->teleport($island->getSpawnPoint());
				$player->sendMessage(SkyBlock::PREFIX . 'Ortağınızın adasına ışınlandınız.');
			}else{
				$island->kickCoOp($player->getLowerCaseName());
				$player->sendMessage(SkyBlock::PREFIX . 'Ortaklıktan ayrıldınız.');
			}
		});
		$modal->setYesButtonText(TextFormat::GREEN . 'Işınlan');
		$modal->setNoButtonText(TextFormat::RED . 'Ayrıl');
		$player->sendForm($modal);
	}

	/**
	 * @inheritDoc
	 */
	public function jsonSerialize(){
		return [
			"type" => "form",
			"title" => "Ortaklıklar",
			"content" => $this->content,
			"buttons" => $this->options
		];
	}
}