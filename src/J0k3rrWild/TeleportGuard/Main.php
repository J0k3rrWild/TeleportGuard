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
use J0k3rrWild\TeleportGuard\Commands\Home;


class Main extends PluginBase implements Listener{

public $unregister = array("tp", "teleport"); 

    public function onEnable(): void{
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
        $server = $this->getServer();

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
            
            // $commandMap->register("tp", new Commands\Tp($this));


            //Register
            $server->getCommandMap()->register("tp", new Commands\Tp($this));
            $server->getCommandMap()->register("tpa", new Commands\Tpa($this));
            $server->getCommandMap()->register("warp", new Commands\Warp($this));
            $server->getCommandMap()->register("home", new Commands\Home($this));
       
           


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
         $playerData2 = fopen($this->getDataFolder()."players/". strtolower($e->getPlayer()->getName()). "/homes.json", "w");
         $data = "[]";
         
         fwrite($playerData2, $data);
         fclose($playerData2);
         fwrite($playerData, $data);
         fclose($playerData);
         

         
        }
        
    }
}
