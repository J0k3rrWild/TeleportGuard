<?php

declare(strict_types=1);

namespace J0k3rrWild\TeleportGuard\Commands;


use pocketmine\utils\TextFormat as TF;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\level\Position;
use pocketmine\scheduler\Task;
use pocketmine\scheduler\TaskScheduler;
use J0k3rrWild\EssentialsMate\Commands\Tasks\TpaDelayed;
use pocketmine\utils\Config;

//Main
use J0k3rrWild\TeleportGuard\Main;


class Tpa extends PluginCommand implements CommandExecutor{

public $main;

  
    public function __construct(Main $main) {
        parent::__construct("tpa", $main);
        $this->setDescription("Wysyła zapytanie o teleport do gracza");
        $this->setUsage("/tpa <player> | /tpa accept <player>| /tpa deny <player> | /tpa here <player>");
        $this->main = $main;
        
    }
    

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {

        if(!($sender instanceof Player)){
            $sender->sendMessage(TF::RED."[MeetMate] > Nie możesz używać tej komendy w konsoli!");
            return true;
        }
        
        if(!isset($args[0])) return false;

        $target = $this->main->getServer()->getPlayer($args[0]);

        $cfg = new Config($this->main->getDataFolder()."players/". strtolower($sender->getName()) . "/temp.yml", Config::YAML);
        
       if($sender === $target){
           $sender->sendMessage(TF::RED."[MeetMate] > Nie możesz wysłać zapytania do samego siebie!");
           return true;
       }

       
     if(!isset($args[1])){   
        if($target){


            if($cfg->get("TpaCooldown") === true){
                $sender->sendMessage(TF::RED."[MeetMate] > Musisz odczekać 15 sekund przed następnym zapytaniem!");
                return true;
            }

                $cfg->set("TpaCooldown", true);
                $cfg->save();
                
                $jcfg = $this->main->getDataFolder()."players/". strtolower($target->getName()) . "/temp.json";
                
                $json = file_get_contents($jcfg);
                $deco = json_decode($json, true);
                array_push($deco, $sender->getName()); 
                file_put_contents($jcfg, json_encode($deco)); 

                $sender->sendMessage(TF::GREEN."[MeetMate] > Wysłałeś zapytanie o teleport do {$target->getName()}");
                $target->sendMessage(TF::GREEN."[MeetMate] > {$sender->getName()} chce się DO CIEBIE teleportować, wpisz w ciągu 15 sekund komende tpa accept/deny {$sender->getName()} (lub pierwsze znaki nicku) by odpowiedzieć");
                $task = new Tasks\TpaDelayed($this, $cfg, $target, $sender); 
                $this->main->getScheduler()->scheduleDelayedTask($task,15*20); // Counted in ticks (1 second = 20 ticks)
                return true;
                
            
        }else{
            $sender->sendMessage(TF::RED."[MeetMate] > Niepoprawny nick lub gracz {$args[0]} jest offine");
            return false;
        }
     }

        if((strtolower($args[0]) === "deny") && isset($args[1])){
            $target = $this->main->getServer()->getPlayer($args[1]);
            $jcfg = $this->main->getDataFolder()."players/". strtolower($sender->getName()) . "/temp.json";
            $json = file_get_contents($jcfg);
            $deco = json_decode($json, true);
            if(in_array($target->getName(), $deco)){

                
                $new = array_diff($deco, array($target->getName())); 
                file_put_contents($jcfg, json_encode($new)); 

                $sender->sendMessage(TF::GREEN."[MeetMate] > Odrzucono zapytanie od {$target->getName()}");
                if($target){
                    $target->sendMessage(TF::RED."[MeetMate] > {$sender->getName()} odrzucił twoje zapytanie o teleport");
                    return true;
                }else{
                    return true;
                }
            }
            if(in_array($target->getName()."_here", $deco)){

                
                $new = array_diff($deco, array($target->getName()."_here")); 
                file_put_contents($jcfg, json_encode($new)); 

                $sender->sendMessage(TF::GREEN."[MeetMate] > Odrzucono zapytanie od {$target->getName()}");
                if($target){
                    $target->sendMessage(TF::RED."[MeetMate] > {$sender->getName()} odrzucił twoje zapytanie o teleport");
                    return true;
                }else{
                    return true;
                }
            }
           

        }

        if((strtolower($args[0]) === "here") && isset($args[1])){
            $target = $this->main->getServer()->getPlayer($args[1]);
            if($target){


                if($cfg->get("TpaCooldown") === true){
                    $sender->sendMessage(TF::RED."[MeetMate] > Musisz odczekać 15 sekund przed następnym zapytaniem!");
                    return true;
                }
    
                    $cfg->set("TpaCooldown", true);
                    $cfg->save();
                    
                    $jcfg = $this->main->getDataFolder()."players/". strtolower($target->getName()) . "/temp.json";
                    
                    $json = file_get_contents($jcfg);
                    $deco = json_decode($json, true);
                    array_push($deco, $sender->getName()."_here"); 
                    file_put_contents($jcfg, json_encode($deco)); 
    
                    $sender->sendMessage(TF::GREEN."[MeetMate] > Wysłano zapytanie o teleport do ciebie dla {$target->getName()}");
                    $target->sendMessage(TF::GREEN."[MeetMate] > {$sender->getName()} chce byś ty DO NIEGO się teleportował, wpisz w ciągu 15 sekund komende tpa accept/deny {$sender->getName()} (lub pierwsze znaki nicku) by odpowiedzieć");
                    $task = new Tasks\TpaDelayed($this, $cfg, $target, $sender, $args[0]); 
                    $this->main->getScheduler()->scheduleDelayedTask($task,15*20); // Counted in ticks (1 second = 20 ticks)
                    return true;
                    
                
            }else{
                $sender->sendMessage(TF::RED."[MeetMate] > Niepoprawny nick lub gracz {$args[1]} jest offine");
                return true;
            }

        }
 
        if((strtolower($args[0]) === "accept") && isset($args[1])){
                $target = $this->main->getServer()->getPlayer($args[1]);
                $jcfg = $this->main->getDataFolder()."players/". strtolower($sender->getName()) . "/temp.json";
              if($target){
                $json = file_get_contents($jcfg);
                $deco = json_decode($json, true);
                if((in_array($target->getName(), $deco)) || (in_array($target->getName()."_here", $deco))){
                 if(!(in_array($target->getName()."_here", $deco))){
                    $getx = round($sender->getX());
                    $gety = round($sender->getY());
                    $getz = round($sender->getZ());
                    $level = $sender->getLevel();
                    $tp = new Position($getx, $gety, $getz, $level);
                    $target->teleport($tp);
                    $sender->sendMessage(TF::GREEN."[MeetMate] > Teleportowano do ciebie {$target->getName()}");
                    $target->sendMessage(TF::GREEN."[MeetMate] > Teleportowano do {$sender->getName()}");
                    return true;
                 }else{
                    $getx = round($target->getX());
                    $gety = round($target->getY());
                    $getz = round($target->getZ());
                    $level = $target->getLevel();
                    $tp = new Position($getx, $gety, $getz, $level);
                    $sender->teleport($tp);
                    $target->sendMessage(TF::GREEN."[MeetMate] > Teleportowano do ciebie {$target->getName()}");
                    $sender->sendMessage(TF::GREEN."[MeetMate] > Teleportowano do {$sender->getName()}");
                    return true;

                 }
                }else{
                  $sender->sendMessage(TF::RED."[MeetMate] > Odpowiedziałeś na zapytanie gracza {$target->getName()} zbyt późno bądź wpisałeś niepoprawny nick.");
                  return true;
                }
              }else{
                  $sender->sendMessage(TF::RED."[MeetMate] > Gracz {$args[1]} nie jest już online bądź wpisałeś niepoprawny nick.");
                  return true;
              }
        }else{
            return false;
        }


        return true;
    }
}