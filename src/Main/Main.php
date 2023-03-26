<?php

declare(strict_types=1);

namespace Main;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use jojoe77777\FormAPI\SimpleForm;

class Main extends PluginBase{

    public function onEnable(): void {
        $this->saveDefaultConfig();
        $this->getLogger()->info(TextFormat::GREEN . "BroadcastPlugin wurde aktiviert!");
    }

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool {
        if($cmd->getName() === "broadcast"){
            if(!$sender instanceof Player){
                $sender->sendMessage(TextFormat::RED . "Dieser Befehl kann nur ingame ausgeführt werden!");
                return true;
            }
            if(!$sender->hasPermission("broadcast.use")){
                $sender->sendMessage(TextFormat::RED . "Du hast keine Rechte diesen Befehl auszuführen!");
                return true;
            }
            $this->openBroadcastForm($sender);
            return true;
        }
        return false;
    }

    public function openBroadcastForm(Player $player): void {
        $form = new SimpleForm(function(Player $player, $data): void {
            if($data !== null){
                $this->broadcast($player, $data);
            }
        });
        $form->setTitle("Broadcast");
        $form->setContent("Geben Sie eine Nachricht ein, die Sie verbreiten möchten:");
        $form->addInput("", "Beispiel: Willkommen auf dem Server!");
        $form->sendToPlayer($player);
    }

    public function broadcast(Player $player, string $message): void {
        $config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        $prefix = $config->get("prefix");
        $broadcastMessage = $prefix . $message;
        $this->getServer()->broadcastMessage($broadcastMessage);
    }
}
