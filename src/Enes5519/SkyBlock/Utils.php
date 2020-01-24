<?php

namespace Enes5519\SkyBlock\utils;

use pocketmine\level\Location;

class Utils{
	public static function encodeLocation(Location $location) : string{
		static $separator = ":";
		return $location->getFloorX() . $separator . $location->getFloorY() . $separator . $location->getFloorZ() .
			$separator . $location->yaw . $separator . $location->pitch . $separator . $location->level->getFolderName();
	}

	public static function decodeLocation(string $encodedLoc) : Location{
		$exp = explode(":", $encodedLoc);
		return new Location((int) $exp[0], (int) $exp[1], (int) $exp[2], (float) $exp[3], (float) $exp[4], self::getLevel($exp[5]));
	}

	public static function deleteDir(string $dir) : void{
		foreach(scandir($dir) as $file){
			if($file == '.' or $file == '..'){
				continue;
			}

			if(is_dir($dir . DIRECTORY_SEPARATOR . $file)){
				self::deleteDir($dir . DIRECTORY_SEPARATOR . $file);
			}else{
				unlink($dir . DIRECTORY_SEPARATOR . $file);
			}
		}

		rmdir($dir);
	}
}