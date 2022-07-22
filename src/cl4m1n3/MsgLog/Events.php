<?php

namespace cl4m1n3\MsgLog;

use pocketmine\event\Listener;
use pocketmine\event\player\{PlayerJoinEvent, PlayerChatEvent};
use cl4m1n3\MsgLog\Main;

class Events implements Listener{
    
    public function onJoin(PlayerJoinEvent $event) : void{
        Main::getInstance()->onCreateData($event->getPlayer()->getName());
    }
    public function onChat(PlayerChatEvent $event) : void{
        if(Main::getInstance()->st->get("own_record") == true){
            Main::getInstance()->recordMessage($event->getPlayer()->getName(), $event->getMessage());
        }
    }
} 