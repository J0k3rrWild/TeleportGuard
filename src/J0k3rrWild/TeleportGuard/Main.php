<?php

declare(strict_types=1);

namespace J0k3rrWild\TeleportGuard;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;
use J0k3rrWild\TeleportGuard\Commands\Tp;
use J0k3rrWild\TeleportGuard\Commands\Tpa;


class Main extends PluginBase implements Listener{

public $unregister = array("tp", "teleport"); 

    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
        @mkdir($this->getDataFolder());
        @mkdir($this->getDataFolder()."players/");
        $this->saveResource( "warps.json");

        //Unregister
        foreach($this->unregister as $disable){
            $commandMap = $this->getServer()->getCommandMap();
            $command = $commandMap->getCommand($disable);
            $command->setLabel($disable."_disabled");
            $command->unregister($commandMap);
            }
            
            $commandMap->register("tp", new Commands\Tp($this));


            //Register
            $this->getCommand("tp")->setExecutor(new Commands\Tp($this));
            $this->getCommand("teleport")->setExecutor(new Commands\Tp($this));
            $this->getCommand("tpa")->setExecutor(new Commands\Tpa($this));
            $this->getCommand("warp")->setExecutor(new Commands\Warp($this));
       
           


        $this->getLogger()->info(TF::GREEN."[TeleportGuard] > Plugin oraz konfiguracja została załadowana pomyślnie");

    }

    public function onJoin(PlayerJoinEvent $e){
        $cfgPath = $this->getDataFolder()."players/". strtolower($e->getPlayer()->getName(). "/temp.yml");
        if(is_file($cfgPath)){
            $cfg = new Config($this->getDataFolder()."players/". strtolower($e->getPlayer()->getName()) . "/temp.yml", Config::YAML);
            $cfg->set("TpaCooldown", false);
            $cfg->set("Requests", "");
            $cfg->save();

            $playerData = fopen($this->getDataFolder()."players/". strtolower($e->getPlayer()->getName()). "/temp.json", "w");
            $data = "[]";
            fwrite($playerData, $data);
            fclose($playerData);
        }else{
         @mkdir($this->getDataFolder()."players/". strtolower($e->getPlayer()->getName()));
         $cfg = new Config($this->getDataFolder()."players/". strtolower($e->getPlayer()->getName()) . "/temp.yml", Config::YAML);
         $cfg->set("TpaCooldown", false);
         $cfg->set("Requests", "");
         $cfg->save();

         $playerData = fopen($this->getDataFolder()."players/". strtolower($e->getPlayer()->getName()). "/temp.json", "w");
         $data = "[]";
         fwrite($playerData, $data);
         fclose($playerData);

         
        }
        
    }
}
