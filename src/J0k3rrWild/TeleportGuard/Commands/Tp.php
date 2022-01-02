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
use pocketmine\plugin\{PluginOwned, PluginOwnedTrait};
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\Server;
use pocketmine\math\Vector3;


//Main
use J0k3rrWild\TeleportGuard\Main;


class Tp extends Command implements PluginOwned{
    use PluginOwnedTrait;

public $main;

  
    public function __construct(Main $main) {
        parent::__construct("tp");
        $this->setDescription("Teleport main command");
        $this->setUsage("/tp <player> | /tp <player> <target>| /tp <x> <y> <z> | /tp <player> <x> <y> <z>");
        $this->main = $main;
        
    }
    

    public function execute(CommandSender $sender, string $label, array $args) : bool {
        if(!isset($args[0])){ 
            throw new InvalidCommandSyntaxException;
            return false;
    }

        if($sender->hasPermission("teleportguard.tp")){
          $ptarget = $this->main->getServer()->getPlayerExact($args[0]);
          
          if(isset($args[2])){
            if(isset($args[3])){
                if($ptarget){
                 $getx = round(intval($args[1]));
                 $gety = round(intval($args[2]));
                 $getz = round(intval($args[3]));
                }else{
                   $sender->sendMessage(TF::RED."[TeleportGuard] > Player {$args[0]} not found");
                   return true;
                }
            }else{
               $getx = round(intval($args[0]));
               $gety = round(intval($args[1]));
               $getz = round(intval($args[2]));
            }
           
            if(!isset($args[3])){
                $level = $sender->getWorld();
                $vect = new Vector3($getx, $gety, $getz, $level);
                $sender->teleport($vect);
            }else{
                $level = $ptarget->getWorld();
                $vect = new Vector3($getx, $gety, $getz, $level);
                $ptarget->teleport($vect);
            }
        


            if($ptarget){
               $ptarget->sendMessage(TF::GREEN."[TeleportGuard] > Teleported player to X:{$getx} Y:{$gety} Z:{$getz}");
               $sender->sendMessage(TF::GREEN."[TeleportGuard] > Teleported player {$ptarget->getName()} to X:{$getx} Y:{$gety} Z:{$getz}");
               return true;
            }else{
               $sender->sendMessage(TF::GREEN."[TeleportGuard] > Teleported to X:{$getx} Y:{$gety} Z:{$getz}");
               return true;
            }

               }

          if($ptarget){
           if(!isset($args[1])){
               if(!($sender instanceof Player)) return false;
                    
                    $getx = round($ptarget->getPosition()->getX());
                    $gety = round($ptarget->getPosition()->getY());
                    $getz = round($ptarget->getPosition()->getZ());
                    $level = $ptarget->getWorld();
                    $tp = new Vector3($getx, $gety, $getz, $level);
                    $sender->teleport($tp);
                    $sender->sendMessage(TF::GREEN."[TeleportGuard] > Teleported to player {$args[0]}");
                    return true;

                }
           
             }else{
                $sender->sendMessage(TF::RED."[TeleportGuard] > Player {$args[0]} not found");
                return true;
             }
            $ttarget = $this->main->getServer()->getPlayerExact($args[1]);
            if($ttarget){

                $tgetx = round($ttarget->getPosition()->getX());
                $tgety = round($ttarget->getPosition()->getY());
                $tgetz = round($ttarget->getPosition()->getZ());

                $tlevel = $ttarget->getWorld();

                $tp = new Vector3($tgetx, $tgety, $tgetz, $tlevel);
                $ttarget->teleport($tp);
                $sender->sendMessage(TF::GREEN."[TeleportGuard] > Teleported {$args[0]} to player {$args[1]}");
                $ttarget->sendMessage(TF::GREEN."[TeleportGuard] > You are teleported to player {$args[0]}");
                return true;
            }else{
                $sender->sendMessage(TF::RED."[TeleportGuard] > Player {$args[1]} not found");
                return true;
            }

           
       }else{
           $sender->sendMessage(TF::RED."You don't have permission to use this command");
       }      
    
 
    return true;
    }
}