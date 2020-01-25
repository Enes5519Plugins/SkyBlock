<?php

declare(strict_types=1);

namespace Enes5519\SkyBlock;

use pocketmine\block\BlockIds;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\Player;

class EventListener implements Listener{

	/** @var SkyBlock */
	private $api;
	/** @var array */
	private $worldOfServer;

	public const TILE_BLOCKS = [
		BlockIds::CHEST => true, // TODO
		BlockIds::CHEST => true,
		BlockIds::CHEST => true,
		BlockIds::CHEST => true,
		BlockIds::CHEST => true,
		BlockIds::CHEST => true,
		BlockIds::CHEST => true,
		BlockIds::CHEST => true,
		BlockIds::CHEST => true,
	];

	public function __construct(SkyBlock $api, array $worldsOfServer){
		$this->api = $api;
		$this->worldOfServer = array_flip($worldsOfServer);
	}

	/**
	 * @param BlockBreakEvent $event
	 * @ignoreCancelled true
	 */
	public function onBlockBreak(BlockBreakEvent $event){
		$event->setCancelled($this->onBlockEvent($event->getPlayer(), IslandPermission::BLOCK_BREAK));
	}

	/**
	 * @param BlockPlaceEvent $event
	 * @ignoreCancelled true
	 */
	public function onBlockPlace(BlockPlaceEvent $event){
		$event->setCancelled($this->onBlockEvent($event->getPlayer(), IslandPermission::BLOCK_PLACE));
	}

	public function onPlayerInteract(PlayerInteractEvent $event){
		$block = $event->getBlock(); // TODO
			$event->setCancelled($this->onBlockEvent($event->getPlayer(), IslandPermission::INTERACT));
	}

	public function onBlockEvent(Player $player, int $perm) : bool{
		if(!$player->hasPermission(SkyBlock::PERMISSION_OP)){
			$levelName = $player->getLevel()->getFolderName();
			if(!isset($this->worldOfServer[$levelName]) /*and $levelName !== $player->getLowerCaseName()*/){
				$island = $this->api->getProvider()->getIsland($levelName);
				return !$island->getPermission()->hasPermission($perm) or !$island->isCoOp($player->getLowerCaseName());
			}
		}

		return false;
	}
}