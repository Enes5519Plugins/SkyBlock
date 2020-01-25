<?php

namespace Enes5519\SkyBlock\utils;

use Enes5519\SkyBlock\SkyBlock;
use pocketmine\level\Position;

class Utils{
	public const ENCODE_SEPARATOR = ':';

	public static function encodePosition(Position $position) : string{
		return $position->getFloorX() . self::ENCODE_SEPARATOR . $position->getFloorY() . self::ENCODE_SEPARATOR . $position->getFloorZ() . self::ENCODE_SEPARATOR . $position->level->getFolderName();
	}

	public static function decodePosition(string $encodedPos) : Position{
		$exp = explode(self::ENCODE_SEPARATOR, $encodedPos);
		return new Position((int) $exp[0], (int) $exp[1], (int) $exp[2], SkyBlock::getLevel($exp[3]));
	}

	public static function deleteDir(string $dir) : void{
		foreach(scandir($dir) as $file){
			if($file === '.' or $file === '..'){
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