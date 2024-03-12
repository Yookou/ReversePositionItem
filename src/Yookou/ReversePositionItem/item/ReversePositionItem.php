<?php

namespace Yookou\ReversePositionItem\item;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\player\Player;
use pocketmine\world\Position;
use RuntimeException;
use Yookou\ReversePositionItem\Main;

class ReversePositionItem implements Listener {
	private array $time = [];

	public function __construct(private Main $plugin) {
	}

	public function onEntityDamageByEntity(EntityDamageByEntityEvent $event) : void {
		$victim = $event->getEntity();
		$damager = $event->getDamager();

		if (!($victim instanceof Player || $damager instanceof Player)) {
			return;
		}

		$itemConfig = StringToItemParser::getInstance()->parse($this->plugin->getConfig()->getNested("item.name-item"));
		if (!$itemConfig instanceof Item) {
			throw new RuntimeException("Item not found in ReversePositionItem.php");
		}

		$item = $damager->getInventory()->getItemInHand();

		if ($item->getTypeId() !== $itemConfig->getTypeId()) {
			return;
		}
		$damagerName = $damager->getName();

		if (!isset($this->time[$damagerName]) || $this->time[$damagerName] - time() <= 0) {
			$this->time[$damagerName] = time() + Main::getInstance()->getConfig()->getNested("item.cooldown");

			$damagerPos = $damager->getPosition();

			$this->onTeleport($damager, $victim->getPosition(), "damager-reverse-message", $victim->getName());
			$this->onTeleport($victim, $damagerPos, "victim-reverse-message", $damagerName);
		} else {
			$time = $this->time[$damagerName] - time();
			$damager->sendPopup(str_replace("{time}", $time, $this->plugin->getConfig()->get("cooldown-message")));
		}
	}

	private function onTeleport(Player $player, Position $position, string $message, string $name) : void {
		$player->teleport($position);
		$player->sendMessage(str_replace("{name}", $name, $this->plugin->getConfig()->get($message)));
	}
}
