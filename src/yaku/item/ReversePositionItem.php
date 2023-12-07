<?php

namespace yaku\item;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\item\StringToItemParser;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\world\Position;
use yaku\Main;

class ReversePositionItem implements Listener {

    public $coordsx;
    public $coordsy;
    public $coordsz;
    public $coordsx2;
    public $coordsy2;
    public $coordsz2;
    private array $time = [];
    public function onTap(EntityDamageByEntityEvent $event): void {
        $victim = $event->getEntity();
        $damager = $event->getDamager();

        if (!$victim instanceof Player) {
            return;
        }
        if (!$damager instanceof Player) {
            return;
        }
        $item = $damager->getInventory()->getItemInHand();


        $configitem = Main::getInstance()->getConfig()->getNested("item.name-item");
        $itemname = StringToItemParser::getInstance()->parse($configitem);
        if (is_null($itemname)) {
            $itemname = VanillaItems::BLAZE_ROD();
        }

        if ($item->getName() !== $itemname->getName()) {
            $damager->sendMessage("§ce");
            return;
        }
        $named = $damager->getName();

        if ((empty($this->time[$named])) || ($this->time[$named] < time())) {
            $this->time[$named] = time() + Main::getInstance()->getConfig()->getNested("item.cooldown");
        } else {
            $time = $this->time[$named] - time();
            $damager->sendPopup(str_replace("{time}", $time, Main::getInstance()->getConfig()->get("cooldown-message")));
            return;
        }
        $this->coordsx = $victim->getPosition()->getX();
        $this->coordsy = $victim->getPosition()->getY();
        $this->coordsz = $victim->getPosition()->getZ();

        $this->coordsx2 = $damager->getPosition()->getX();
        $this->coordsy2 = $damager->getPosition()->getY();
        $this->coordsz2 = $damager->getPosition()->getZ();

        $damager->teleport(new Position($this->coordsx, $this->coordsy, $this->coordsz, $victim->getWorld()));
        $victim->teleport(new Position($this->coordsx2, $this->coordsy2, $this->coordsz2, $damager->getWorld()));



        $victim->sendMessage(str_replace("{damager}", $named, Main::getInstance()->getConfig()->get("victim-reverse-message")));

        $namev = $victim->getName();
        $victim->sendMessage(str_replace("{victim}", $namev, Main::getInstance()->getConfig()->get("damager-reverse-message")));
    }
}
