<?php

declare(strict_types=1);

namespace J0k3rrWild\TeleportGuard;


use pocketmine\utils\TextFormat as TF;
use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\Player;
use pocketmine\level\Position;



//Main
use J0k3rrWild\TeleportGuard\Main;


class Tp extends PluginCommand implements CommandExecutor{

public $main;

  
    public function __construct(Main $main) {
        parent::__construct("tp", $main);
        $this->setDescription("Teleportuje do celu");
        $this->setUsage("/tp <player> | /tp <player> <target>| /tp <x> <y> <z> | /tp <player> <x> <y> <z>");
        $this->main = $main;
        
    }
    

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
        if(!isset($args[0])) return false;

        if($sender->hasPermission("teleportguard.tp")){
          $ptarget = $this->main->getServer()->getPlayer($args[0]);
          
          if($ptarget){
           if(!isset($args[1])){
               if(!($sender instanceof Player)) return false;
                    
                    $getx = round($ptarget->getX());
                    $gety = round($ptarget->getY());
                    $getz = round($ptarget->getZ());
                    $level = $ptarget->getLevel();
                    $tp = new Position($getx, $gety, $getz, $level);
                    $sender->teleport($tp);
                    $sender->sendMessage(TF::GREEN."[MeetMate] > Teleportowano do gracza {$args[0]}");
                    return true;

                }
             }else{
                $sender->sendMessage(TF::RED."[MeetMate] > Nie wykryto gracza {$args[0]}");
                return true;
             }
            $ttarget = $this->main->getServer()->getPlayer($args[1]);
            if($ttarget){

                $tgetx = round($ttarget->getX());
                $tgety = round($ttarget->getY());
                $tgetz = round($ttarget->getZ());

                $tlevel = $ttarget->getLevel();

                $tp = new Position($tgetx, $tgety, $tgetz, $tlevel);
                $ttarget->teleport($tp);
                $sender->sendMessage(TF::GREEN."[MeetMate] > Teleportowano {$args[0]} do gracza {$args[1]}");
                $ttarget->sendMessage(TF::GREEN."[MeetMate] > Teleportowano cie do gracza {$args[0]}");
                return true;
            }else{
                $sender->sendMessage(TF::RED."[MeetMate] > Nie wykryto gracza {$args[1]}");
            }

           
       }      
    
 
    return true;
    }
}