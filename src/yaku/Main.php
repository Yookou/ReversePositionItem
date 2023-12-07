<?php

namespace yaku;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use yaku\item\ReversePositionItem;

class Main extends PluginBase {

    use SingletonTrait;
   public function onEnable(): void {
       self::setInstance($this);
       $this->saveDefaultConfig();

       $this->getServer()->getPluginManager()->registerEvents(new ReversePositionItem(), $this);
   }

}
