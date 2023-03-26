<?php

namespace Main;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener {

    public function onEnable(): void {
        // Register listener
        $this->getServer()->getPluginManager()->registerEvents(new CustomUI($this), $this);
        // Create config file
        $this->saveDefaultConfig();
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if ($command->getName() === "broadcast") {
            if (count($args) > 0) {
                $message = implode(" ", $args);
                $this->broadcastMessage($message);
                return true;
            } else {
                $sender->sendMessage("Usage: /broadcast <message>");
                return false;
            }
        }
        return false;
    }

    public function broadcastMessage(string $message) {
        foreach ($this->getServer()->getOnlinePlayers() as $player) {
            $player->sendMessage($this->getConfig()->get("broadcast-prefix") . $message);
        }
    }
}

class CustomUI implements Listener {

    private $plugin;

    public function __construct(BroadcastPlugin $plugin) {
        $this->plugin = $plugin;
    }

    public function onCustomFormResponse(\pocketmine\event\server\DataPacketReceiveEvent $event) {
        $player = $event->getPlayer();
        $packet = $event->getPacket();
        if ($packet instanceof \pocketmine\network\mcpe\protocol\ModalFormResponsePacket) {
            $formData = json_decode($packet->formData, true);
            if ($packet->formId === 1) {
                if (isset($formData[0]) && $formData[0] !== "") {
                    $this->plugin->broadcastMessage($formData[0]);
                    $player->sendMessage("Broadcasted message: " . $formData[0]);
                } else {
                    $player->sendMessage("Please enter a message");
                }
            }
        }
    }

    public function sendCustomUI(Player $player) {
        $uiData = [
            "type" => "custom_form",
            "title" => "Broadcast Message",
            "content" => [
                [
                    "type" => "input",
                    "text" => "Enter message to broadcast:",
                    "default" => ""
                ]
            ]
        ];
        $packet = new \pocketmine\network\mcpe\protocol\ModalFormRequestPacket();
        $packet->formId = 1;
        $packet->formData = json_encode($uiData);
        $player->sendDataPacket($packet);
    }
}

