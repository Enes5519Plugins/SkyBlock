<?php

declare(strict_types=1);

namespace Enes5519\SkyBlock\form;

class MenuOption implements \JsonSerializable{
	public const IMAGE_TYPE_URL = "url";
	public const IMAGE_TYPE_PATH = "path";

	/** @var string */
	private $text;
	/** @var string */
	private $image, $imageType;

	/** @var mixed */
	private $extra;

	public function __construct(string $text, string $image = "", string $imageType = self::IMAGE_TYPE_URL){
		$this->text = $text;
		$this->image = $image;
		$this->imageType = $imageType;
	}

	public function getExtra(){
		return $this->extra;
	}

	public function setExtra($extra) : MenuOption{
		$this->extra = $extra;

		return $this;
	}

	public function jsonSerialize(){
		$json = ["text" => $this->text];

		if($this->image !== ""){
			$json["image"] = [
				"data" => $this->image,
				"type" => $this->imageType
			];
		}

		return $json;
	}
}