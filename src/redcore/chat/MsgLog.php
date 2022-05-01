<?php

namespace redcore\chat;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\event\Listener;
use pocketmine\event\player\{PlayerLoginEvent, PlayerChatEvent};
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class MsgLog extends PluginBase implements Listener
{
    
    protected function onEnable() : void 
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        @mkdir($this->getDataFolder() . "players/");
    }
    public function onChat(PlayerChatEvent $event)
    {
        $msg = $event->getMessage();
        $nick = strtolower($event->getPlayer()->getName());
        $this->setMSG($nick, $msg);
    }
    public function createData(PlayerLoginEvent $event)
    {
        $nick = strtolower($event->getPlayer()->getName());
        $this->log = new Config($this->getDataFolder() . "players/{$nick}.yml", Config::YAML, array("countmsg" => 0));
    }
    public function setMSG($nick, $arg)
    {
        $n = $this->log->get("countmsg");
        $this->log->set(strtolower($n + 1), $arg);
        $this->log->save();
        $this->log->set("countmsg", $n + 1);
        $this->log->save();
    }
    public function getMSG($nick, $sender, $arg0, $arg1)
    {
        $n = $this->log->get("countmsg");
        if($arg1 <= $n){
            $sender->sendMessage("§fPlayer Messages §e{$nick}:\n§fTotal messages: §e{$n}\n\n");
            for($i = $arg0; $i <= $arg1; $i++){
                $msg = $this->log->get(strtolower($i));
                $sender->sendMessage("§7[§b{$i}§7]: §f{$msg}");
            }
        }else{
            $sender->sendMessage("§cThe number of messages you set exceeds the number of player messages. \nEnter the maximum number no more than §f{$n}!");
        }
    }
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool
    {
        if($command->getName() == "getmsg"){
            if(isset($args[2])){
                if(is_file($this->getDataFolder() . "players/{$args[0]}.yml")){
                    if(is_numeric($args[1]) && is_numeric($args[2])){
                        if($args[1] > 0 && $args[2] > 0){
                        	if($args[1] < $args[2]){
                        	    if($args[2] - $args[1] <=50){
                                    $this->getMSG($args[0], $sender, $args[1], $args[2]);
                                }else{
                                    $sender->sendMessage("§cYou can view a maximum of 50 messages");
                                }
                            }else{
                            	$sender->sendMessage("§cThe number «from» must not exceed the number «to»!");
                            }
                            return true;
                        }else{
                            $sender->sendMessage("§cArguments 2 §cand 3 cannot be negative or equal to 0!");
                            return true;
                        }
                    }else{
                        $sender->sendMessage("§cArguments 2 and 3 must be a number!");
                        return true;
                    }
                }else{
                    $sender->sendMessage("§cThis player has not played on the server yet");
                    return true;
                }
            }else{
                $sender->sendMessage("§cUse correctly: /getmsg [player's nickname] [from N number] [up to N number]");
                return true;
            }
        }
    }
}