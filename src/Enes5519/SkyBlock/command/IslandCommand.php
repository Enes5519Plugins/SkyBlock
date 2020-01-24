<?php

declare(strict_types=1);

namespace Enes5519\SkyBlock\command;

use Enes5519\SkyBlock\provider\DataProvider;
use Enes5519\SkyBlock\SkyBlock;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class IslandCommand extends Command{

	public function __construct(){
		parent::__construct("ada", "Ada işlemlerini gerçekleştirir", "/ada");
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args){
		if($sender instanceof Player){
			$provider = SkyBlock::getAPI()->getProvider();
			if(($createIsland = $provider->createIsland($sender)) === DataProvider::ERROR_ALREADY_EXISTS){
				// TODO : UI
			}elseif($createIsland === DataProvider::ERROR_NONE){
				$sender->teleport($provider->getIsland($sender->getLowerCaseName())->getSpawnPoint());
				$sender->sendMessage(SkyBlock::PREFIX . TextFormat::GREEN . 'Adanız oluşturuldu!');
			}else{
				// TODO : Süre yazsın
				$sender->sendMessage(SkyBlock::PREFIX . TextFormat::RED . 'Yeni ada oluşturmadan önce beklemelisin!');
			}
		}else{
			$sender->sendMessage("Bu komut yalnızca oyunda kullanılabilir.");
		}
	}

}