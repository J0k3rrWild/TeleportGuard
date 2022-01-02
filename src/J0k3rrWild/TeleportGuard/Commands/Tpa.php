<?php

declare(strict_types=1);

namespace J0k3rrWild\TeleportGuard\Commands;


use pocketmine\utils\TextFormat as TF;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\player\Player;
use pocketmine\level\Position;
use pocketmine\scheduler\Task;
use pocketmine\scheduler\TaskScheduler;
use J0k3rrWild\EssentialsMate\Commands\Tasks\TpaDelayed;
use pocketmine\utils\Config;
use pocketmine\plugin\{PluginOwned, PluginOwnedTrait};
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\Server;
use pocketmine\math\Vector3;

//Main
use J0k3rrWild\TeleportGuard\Main;


class Tpa extends Command implements PluginOwned{
    use PluginOwnedTrait;

public $main;

  
    public function __construct(Main $main) {
        parent::__construct("tpa");
        $this->setDescription("Tpa main command");
        $this->setUsage("/tpa <player> | /tpa accept <player>| /tpa deny <player> | /tpa here <player>");
        $this->main = $main;
        
    }
    

    public function execute(CommandSender $sender, string $label, array $args) : bool {

        if(!($sender instanceof Player)){
            $sender->sendMessage(TF::RED."[TeleportGuard] > You cant use that in console!");
            return true;
        }
        
        if(!isset($args[0])){ 
            throw new InvalidCommandSyntaxException;
            return false;
        }

        $target = $this->main->getServer()->getPlayerExact($args[0]);

        $cfg = new Config($this->main->getDataFolder()."players/". strtolower($sender->getName()) . "/temp.yml", Config::YAML);
        
       if($sender === $target){
           $sender->sendMessage(TF::RED."[TeleportGuard] > You cant send this command to yourself!");
           return true;
       }

     if((strtolower($args[0]) === "here")){
            if(!isset($args[1])){
                throw new InvalidCommandSyntaxException;
                return false;
            }
            $target = $this->main->getServer()->getPlayerExact($args[1]);
            if($target){


                if($cfg->get("TpaCooldown") === true){
                    $sender->sendMessage(TF::RED."[TeleportGuard] > You must wait 15 sec to use again this command!");
                    return true;
                }

                    $cfg->set("TpaCooldown", true);
                    $cfg->save();
                    
                    $jcfg = $this->main->getDataFolder()."players/". strtolower($target->getName()) . "/temp.json";
                    
                    $json = file_get_contents($jcfg);
                    $deco = json_decode($json, true);
                    array_push($deco, $sender->getName()."_here"); 
                    file_put_contents($jcfg, json_encode($deco)); 

                    $sender->sendMessage(TF::GREEN."[TeleportGuard] > You send teleport question to you to {$target->getName()}");
                    $target->sendMessage(TF::GREEN."[TeleportGuard] > {$sender->getName()} want you to teleport TO HIM, type in 20 seconds the command tpa accept/deny {$sender->getName()} to answer");
                    $task = new Tasks\TpaDelayed($this, $cfg, $target, $sender, $args[0]); 
                    $this->main->getScheduler()->scheduleDelayedTask($task,20*20); // Counted in ticks (1 second = 20 ticks)
                    return true;
                    
                
            }else{
                $sender->sendMessage(TF::RED."[TeleportGuard] > Niepoprawny nick lub gracz {$args[1]} jest offine");
                return true;
            }

     }

       
     if(!isset($args[1])){   
        if($target){


            if($cfg->get("TpaCooldown") === true){
                $sender->sendMessage(TF::RED."[TeleportGuard] > Musisz odczekać 15 sekund przed następnym zapytaniem!");
                return true;
            }

                $cfg->set("TpaCooldown", true);
                $cfg->save();
                
                $jcfg = $this->main->getDataFolder()."players/". strtolower($target->getName()) . "/temp.json";
                
                $json = file_get_contents($jcfg);
                $deco = json_decode($json, true);
                array_push($deco, $sender->getName()); 
                file_put_contents($jcfg, json_encode($deco)); 

                $sender->sendMessage(TF::GREEN."[TeleportGuard] > Wysłałeś zapytanie o teleport do {$target->getName()}");
                $target->sendMessage(TF::GREEN."[TeleportGuard] > {$sender->getName()} want to teleport TO YOU, type tpa accept/deny {$sender->getName()} to answer");
                $task = new Tasks\TpaDelayed($this, $cfg, $target, $sender); 
                $this->main->getScheduler()->scheduleDelayedTask($task,20*20); // Counted in ticks (1 second = 20 ticks)
                return true;
                
            
        }else{
            $sender->sendMessage(TF::RED."[TeleportGuard] > Incorrect nick or player {$args[0]} is offline");
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

                $sender->sendMessage(TF::GREEN."[TeleportGuard] > Question from {$target->getName()} has been rejected");
                if($target){
                    $target->sendMessage(TF::RED."[TeleportGuard] > {$sender->getName()} rejected your teleport question");
                    return true;
                }else{
                    return true;
                }
            }
            if(in_array($target->getName()."_here", $deco)){

                
                $new = array_diff($deco, array($target->getName()."_here")); 
                file_put_contents($jcfg, json_encode($new)); 

                $sender->sendMessage(TF::GREEN."[TeleportGuard] > Question from {$target->getName()} has been rejected");
                if($target){
                    $target->sendMessage(TF::RED."[TeleportGuard] > {$sender->getName()} rejected your teleport question");
                    return true;
                }else{
                    return true;
                }
            }
           

        }

        
        if((strtolower($args[0]) === "accept") && isset($args[1])){
                $target = $this->main->getServer()->getPlayerExact($args[1]);
                $jcfg = $this->main->getDataFolder()."players/". strtolower($sender->getName()) . "/temp.json";
              if($target){
                $json = file_get_contents($jcfg);
                $deco = json_decode($json, true);
                if((in_array($target->getName(), $deco)) || (in_array($target->getName()."_here", $deco))){
                 if(!(in_array($target->getName()."_here", $deco))){
                    $getx = round($sender->getPosition()->getX());
                    $gety = round($sender->getPosition()->getY());
                    $getz = round($sender->getPosition()->getZ());
                    $level = $sender->getWorld();
                    $tp = new Vector3($getx, $gety, $getz, $level);
                    $target->teleport($tp);
                    $sender->sendMessage(TF::GREEN."[TeleportGuard] > Teleported to you {$target->getName()}");
                    $target->sendMessage(TF::GREEN."[TeleportGuard] > Teleported to {$sender->getName()}");
                    return true;
                 }else{
                    $getx = round($target->getPosition()->getX());
                    $gety = round($target->getPosition()->getY());
                    $getz = round($target->getPosition()->getZ());
                    $level = $target->getWorld();
                    $tp = new Vector3($getx, $gety, $getz, $level);
                    $sender->teleport($tp);
                    $target->sendMessage(TF::GREEN."[TeleportGuard] > Teleported to you {$target->getName()}");
                    $sender->sendMessage(TF::GREEN."[TeleportGuard] > Teleported to {$sender->getName()}");
                    return true;

                 }
                }else{
                  $sender->sendMessage(TF::RED."[TeleportGuard] > You answered the player's {$target->getName()} request too late or entered an incorrect nickname");
                  return true;
                }
              }else{
                  $sender->sendMessage(TF::RED."[TeleportGuard] > Player {$args[1]} is offline or incorrect nick");
                  return true;
              }
        }else{
            throw new InvalidCommandSyntaxException;
            return false;
        
        }


        return true;
    }
}