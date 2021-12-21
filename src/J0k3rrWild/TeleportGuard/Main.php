<?php

declare(strict_types=1);

namespace J0k3rrWild\TeleportGuard;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;
use J0k3rrWild\TeleportGuard\Tp;


class Main extends PluginBase implements Listener{

public $unregister = array("tp"); 

    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
        @mkdir($this->getDataFolder());
        @mkdir($this->getDataFolder()."players/");

        //Unregister
        foreach($this->unregister as $disable){
            $commandMap = $this->getServer()->getCommandMap();
            $command = $commandMap->getCommand($disable);
            $command->setLabel($disable."_disabled");
            $command->unregister($commandMap);
            }
            $commandMap->register("Tp", new Tp($this));


            //Register
            $this->getCommand("tp")->setExecutor(new Tp($this));


        $this->getLogger()->info(TF::GREEN."[TeleportGuard] > Plugin oraz konfiguracja została załadowana pomyślnie");

    }

    public function onJoin(PlayerJoinEvent $e){
        $cfgPath = $this->getDataFolder()."players/". strtolower($e->getPlayer()->getName());
        if(is_file($cfgPath)){
            return;
        }else{
         @mkdir($this->getDataFolder()."players/". strtolower($e->getPlayer()->getName()));
        //  $cfg = new Config($this->getDataFolder()."players/". strtolower($e->getPlayer()->getName()) . "/homes.yml", Config::YAML);
         
        }
        
    }
}
