<?php

declare(strict_types=1);

namespace Enes5519\SkyBlock;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;

class EventListener implements Listener{

	/** @var SkyBlock */
	private $api;

	public function __construct(SkyBlock $api){
		$this->api = $api;
	}

	public function onBlockBreak(BlockBreakEvent $event){

	}

	public function onBlockPlace(BlockPlaceEvent $event){

	}

	public function onBlockEvent(){

	}
}