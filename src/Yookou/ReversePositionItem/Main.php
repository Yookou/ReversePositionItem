<?php

namespace Yookou\ReversePositionItem;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use Yookou\ReversePositionItem\item\ReversePositionItem;

class Main extends PluginBase {
	use SingletonTrait;

	protected function onLoad() : void {
		self::setInstance($this);
		$this->saveDefaultConfig();
	}

	protected function onEnable() : void {
		$this->getServer()->getPluginManager()->registerEvents(new ReversePositionItem($this), $this);
	}
}
