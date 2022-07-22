<?php

namespace cl4m1n3\MsgLog\commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use cl4m1n3\MsgLog\Main;
use pocketmine\player\Player;
use pocketmine\Server;

class msglog extends Command{
    
    private $nick;
    
    public function __construct(){
        parent::__construct("msglog", "получить отправленные сообщения игрока");
    }
    public function execute(CommandSender $sender, string $commandLabel, array $args){
        if($sender instanceof Player){
            if(isset($args[1])){
                if(is_numeric($args[1]) && $args[1] > 0){
                    if($player = Server::getInstance()->getPlayerByPrefix($args[0])){
                        $this->nick = $player->getName();
                        if(Main::getInstance()->cm->get(strtolower($this->nick)) > 0){
                            if($args[1] <= Main::getInstance()->cm->get(strtolower($this->nick))){
                                Main::getInstance()->onGetMessageByID($sender, $this->nick, $args[1]);
                            }else{
                                $sender->sendMessage("§cID сообщения не совпадает: §7сообщения с таким ID не существует.");
                            }
                        }else{
                            $sender->sendMessage("§cID сообщения не совпадает: §7этот игрок еще ни разу не отправлял сообщения в чат.");
                        }
                    }else{
                        if(!Main::getInstance()->cm->exists(strtolower($args[0]))){
                            $sender->sendMessage("§cИгрок не в сети или никогда не был на сервере!\n§7Чтобы посмотреть сообщения игрока, который оффлайн, введите его полный ник.");
                        }else{
                            if(Main::getInstance()->cm->get(strtolower($args[0])) > 0){
                                if($args[1] <= Main::getInstance()->cm->get(strtolower($args[0]))){
                                    Main::getInstance()->onGetMessageByID($sender, $args[0], $args[1]);
                                }else{
                                    $sender->sendMessage("§cID сообщения не совпадает: §7сообщения с таким ID не существует.");
                                }
                            }else{
                                $sender->sendMessage("§cID сообщения не совпадает: §7этот игрок еще ни разу не отправлял сообщения в чат.");
                            }
                        }
                    }
                }else{
                    $sender->sendMessage("§cID сообщения должен быть положительным числом!");
                }
            }elseif(isset($args[0])){
                if($player = Server::getInstance()->getPlayerByPrefix($args[0])){
                    $this->nick = $player->getName();
                    if(Main::getInstance()->cm->get(strtolower($this->nick)) > 0){
                        Main::getInstance()->onGetMessages($sender, $this->nick);
                    }else{
                         $sender->sendMessage("§cЭтот игрок еще не отправлял сообщения в чат!");
                    }
                }else{
                    if(!Main::getInstance()->cm->exists(strtolower($args[0]))){
                        $sender->sendMessage("§cИгрок не в сети или никогда не был на сервере!\n§7Чтобы посмотреть сообщения игрока, который оффлайн, введите его полный ник.");
                    }else{
                        if(Main::getInstance()->cm->get(strtolower($args[0])) > 0){
                            Main::getInstance()->onGetMessages($sender, $args[0]);
                        }else{
                            $sender->sendMessage("§cЭтот игрок еще не отправлял сообщения в чат!");
                        }
                    }
                }
            }else{
                Main::getInstance()->onSettings($sender);
            }
        }else{
            Main::getInstance()->getLogger()->info("§cДанную команду нельзя использовать от имени консоли!");
        }
    }
}