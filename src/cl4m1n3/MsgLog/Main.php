<?php

namespace cl4m1n3\MsgLog;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\Server;
use cl4m1n3\MsgLog\Events;
use pocketmine\player\Player;
use cl4m1n3\MsgLog\commands\msglog;

class Main extends PluginBase{
    
    private \SQLite3 $db;
    private static $instance;
    
    static function getInstance() : Main{
        return self::$instance;
    }
    protected function onLoad() : void{
        self::$instance = $this;
        Server::getInstance()->getCommandMap()->register("msglog", new msglog());
        @mkdir($this->getDataFolder() . "data");
        @mkdir($this->getDataFolder() . "plugin");
        $this->db = new \SQLite3($this->getDataFolder() . "data/messages.db");
        $this->st = new Config($this->getDataFolder() . "plugin/settings.yml", Config::YAML, array("own_record" => true));
        $this->cm = new Config($this->getDataFolder() . "data/countmessages.json", Config::JSON);
    }
    protected function onEnable() : void{
        Server::getInstance()->getPluginManager()->registerEvents(new Events(), $this);
    }
    public function onCreateData(string $nick) : void{
        $this->db->exec("CREATE TABLE IF NOT EXISTS '$nick'(Number INTEGER PRIMARY KEY, Date TEXT DEFAULT none, Message TEXT DEFAULT none, Position TEXT DEFAULT none, World TEXT DEFAULT none, Ip TEXT DEFAULT none)");
        if(!$this->cm->exists(strtolower($nick))){
            $this->cm->set(strtolower($nick), 0);
            $this->cm->save();
        }
    }
    public function recordMessage(string $nick, $msg) : void{
        $player = Server::getInstance()->getPlayerByPrefix($nick);
        $date = date("d.m.20y | h:i:s");
        $xyz = $player->getPosition()->asVector3();
        $x = round($xyz->getX());
        $y = round($xyz->getY());
        $z = round($xyz->getZ());
        $pos = "{$x}, {$y}, {$z}";
        $world = $player->getWorld()->getFolderName();
        $ip = $player->getNetworkSession()->getIp();
        $this->db->exec("INSERT INTO '$nick'(Date, Message, Position, World, Ip) VALUES('$date', '$msg', '$pos', '$world', '$ip')");
        $this->cm->set(strtolower($nick), $this->cm->get(strtolower($nick)) + 1);
        $this->cm->save();
        
    }
    public function onGetMessages($player, string $arg){
        if(Server::getInstance()->getPluginManager()->getPlugin("FormAPI")){
            $form = Server::getInstance()->getPluginManager()->getPlugin("FormAPI")->createCustomForm(function (Player $player, array $data = null){
                if($data === null){
                    return true;
                }
            });
            $form->setTitle("§8Все сообщения игрока §3{$arg}");
            $form->addLabel("§fФормат: §7[ID] (дата): сообщение\n\n\n");
            for($i = $this->cm->get(strtolower($arg)); $i >= 1; $i--){
                if($table = $this->db->query("SELECT * FROM '$arg' WHERE Number = '$i'")->fetchArray()){
                    $form->addLabel("§7[§b{$table["Number"]}§7] (§e{$table["Date"]}§7): §f{$table["Message"]}");
                }
            }
            $form->sendToPlayer($player);
            return $form;
        }else{
            $player->sendMessage("§cВыполнение команды невозможно: отсутствует плагин FormAPI");
        }
    }
    public function onGetMessageByID($player, string $arg, int $id){
        if(Server::getInstance()->getPluginManager()->getPlugin("FormAPI")){
            $form = Server::getInstance()->getPluginManager()->getPlugin("FormAPI")->createCustomForm(function (Player $player, array $data = null){
                if($data === null){
                    return true;
                }
            });
            $form->setTitle("§8Информация о сообщении игрока §3{$arg}");
            if($table = $this->db->query("SELECT * FROM '$arg' WHERE Number = '$id'")->fetchArray()){
                $form->addLabel("§fСообщение: §b{$table["Message"]}\n§fID: §e{$id}\n§fДата отправки: §b{$table["Date"]}\n§fБыло отправлено на позиции (§7X, Y, Z)§f: §b{$table["Position"]}\n§fОтправлено в мире: §b{$table["World"]}\n§fОтправлено с IP: §b{$table["Ip"]}");
            }
            $form->sendToPlayer($player);
            return $form;
        }else{
            $player->sendMessage("§cВыполнение команды невозможно: отсутствует плагин FormAPI");
        }
    }
    public function onSettings($player){
        if(Server::getInstance()->getPluginManager()->getPlugin("FormAPI")){
            $form = Server::getInstance()->getPluginManager()->getPlugin("FormAPI")->createCustomForm(function (Player $player, array $data = null){
                if($data === null){
                    return true;
                }
                switch($data[0]){
                    case true:
                        if($this->st->get("own_record") != true){
                            $this->st->set("own_record", true);
                            $this->st->save();
                            $player->sendMessage("§aЗапись по умолчанию успешно включена!");
                        }
                    break; 
                    case false:
                        if($this->st->get("own_record") != false){
                           $this->st->set("own_record", false);
                            $this->st->save();
                            $player->sendMessage("§aЗапись по умолчанию успешно выключена! Плагин §fMsgLog §aбольше не записывает сообщения");
                        }
                    break;
                }
            });
            $form->setTitle("§l§8MsgLog");
            $form->addToggle("§fЗапись сообщений по умолчанию §7(выключайте только в том случае, если вы используете функцию §3recordMessage() §7в другом плагине)", $this->st->get("own_record"));
            $form->sendToPlayer($player);
            return $form;
        }else{
            $player->sendMessage("§cВыполнение команды невозможно: отсутствует плагин FormAPI");
        }
    }
} 