<?php

declare(strict_types=1);

namespace Enes5519\SkyBlock;

use pocketmine\block\BlockIds;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\inventory\InventoryPickupItemEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerBucketFillEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\Player;

class EventListener implements Listener{

	/** @var SkyBlock */
	private $api;
	/** @var array */
	private $worldOfServer;

	public const TILE_BLOCKS = [
		BlockIds::STANDING_BANNER => true,
		BlockIds::WALL_BANNER => true,
		BlockIds::CHEST => true,
		BlockIds::TRAPPED_CHEST => true,
		BlockIds::ENCHANTMENT_TABLE => true,
		BlockIds::ENDER_CHEST => true,
		BlockIds::FLOWER_POT_BLOCK => true,
		BlockIds::FURNACE => true,
		BlockIds::BURNING_FURNACE => true,
		BlockIds::ITEM_FRAME_BLOCK => true,
		BlockIds::SIGN_POST => true,
		BlockIds::WALL_SIGN => true
	];

	public function __construct(SkyBlock $api, array $worldsOfServer){
		$this->api = $api;
		$this->worldOfServer = array_flip($worldsOfServer);
	}

	/**
	 * @param PlayerBucketFillEvent $event
	 * @ignoreCancelled true
	 */
	public function onFillBucket(PlayerBucketFillEvent $event) : void{
		$event->setCancelled($this->onIslandEvents($event->getPlayer(), IslandPermission::INTERACT));
	}

	/**
	 * @param BlockBreakEvent $event
	 * @ignoreCancelled true
	 */
	public function onBlockBreak(BlockBreakEvent $event) : void{
		$event->setCancelled($this->onIslandEvents($event->getPlayer(), IslandPermission::BLOCK_BREAK));
	}

	/**
	 * @param BlockPlaceEvent $event
	 * @ignoreCancelled true
	 */
	public function onBlockPlace(BlockPlaceEvent $event) : void{
		$event->setCancelled($this->onIslandEvents($event->getPlayer(), IslandPermission::BLOCK_PLACE));
	}

	/**
	 * @param PlayerInteractEvent $event
	 * @ignoreCancelled true
	 */
	public function onPlayerInteract(PlayerInteractEvent $event) : void{
		$block = $event->getBlock();
		if(isset(self::TILE_BLOCKS[$block->getId()])){
			$event->setCancelled($this->onIslandEvents($event->getPlayer(), IslandPermission::INTERACT));
		}
	}

	/**
	 * @param InventoryPickupItemEvent $event
	 * @ignoreCancelled true
	 */
	public function onPickupItem(InventoryPickupItemEvent $event) : void{
		$holder = $event->getInventory()->getHolder();
		if($holder instanceof Player and !$holder->isClosed()){
			$event->setCancelled($this->onIslandEvents($holder, IslandPermission::PICKUP_ITEM));
		}
	}

	public function onIslandEvents(Player $player, int $perm) : bool{
		if(!$player->hasPermission(SkyBlock::PERMISSION_OP)){
			$levelName = $player->getLevel()->getFolderName();
			if(!isset($this->worldOfServer[$levelName]) and $levelName !== $player->getLowerCaseName()){
				$island = $this->api->getProvider()->getIsland($levelName);

				return !$island->getPermission()->hasPermission($perm) or !$island->isCoOp($player->getLowerCaseName());
			}
		}

		return false;
	}
}