<?php

namespace Main;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\Player;
use pocketmine\utils\TextFormat;
use jojoe77777\FormAPI\SimpleForm;

class Main extends PluginBase{

    public function onEnable(): void {
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
        $this->getLogger()->info(TextFormat::GREEN . "Das BroadcastPlugin wurde aktiviert!");
    }

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool {
        if($cmd->getName() == "broadcast"){
            if($sender instanceof Player){
                if($sender->hasPermission("broadcast.use")){
                    $this->openBroadcastForm($sender);
                    return true;
                }else{
                    $sender->sendMessage(TextFormat::RED . "Du hast keine Rechte zu diesem Befehl!");
                    return true;
                }
            }
        }
        return false;
    }

    public function openBroadcastForm(Player $player){
        $form = new SimpleForm(function(Player $player, $data){
            if($data !== null){
                $this->broadcast($player, $data);
            }
        });
        $form->setTitle("Broadcast");
        $form->setContent("Geben Sie eine Nachricht ein, die Sie verbreiten möchten:");
        $form->addInput("", "Beispiel: Willkommen auf dem Server!");
        $form->sendToPlayer($player);
        return $form;
    }

    public function broadcast(Player $player, $message){
        $config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
        $prefix = $config->get("prefix");
        $broadcastMessage = $prefix . $message;
        $this->getServer()->broadcastMessage($broadcastMessage);
    }
}







