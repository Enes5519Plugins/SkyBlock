<?php

declare(strict_types=1);

namespace Enes5519\SkyBlock\form;

use pocketmine\form\Form;
use pocketmine\form\FormValidationException;
use pocketmine\Player;

class ModalForm implements Form{
	/** @var string */
	private $title;
	/** @var string */
	private $content;
	/** @var string */
	private $yesButtonText = "gui.yes";
	/** @var string */
	private $noButtonText = "gui.no";
	/** @var \Closure */
	private $onSubmit;

	public function __construct(string $title, string $content, \Closure $onSubmit){
		$this->title = $title;
		$this->content = $content;
		$this->onSubmit = $onSubmit;
	}

	/**
	 * @param string $yesButtonText
	 */
	public function setYesButtonText(string $yesButtonText) : void{
		$this->yesButtonText = $yesButtonText;
	}

	/**
	 * @param string $noButtonText
	 */
	public function setNoButtonText(string $noButtonText) : void{
		$this->noButtonText = $noButtonText;
	}

	/**
	 * @inheritDoc
	 */
	public function handleResponse(Player $player, $data) : void{
		if(!is_bool($data)){
			throw new FormValidationException("Expected bool, got " . gettype($data));
		}

		($this->onSubmit)($player, $data);
	}

	/**
	 * @inheritDoc
	 */
	public function jsonSerialize(){
		return [
			"type" => "modal",
			"title" => $this->title,
			"content" => $this->content,
			"button1" => $this->yesButtonText,
			"button2" => $this->noButtonText
		];
	}
}