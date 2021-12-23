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



//Main
use J0k3rrWild\TeleportGuard\Main;


class Warp extends PluginCommand implements CommandExecutor{

public $main;
public $warps;
  
    public function __construct(Main $main) {
        $this->main = $main;
        
    }
    

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
        if(!isset($args[0])) return false;
   
        
          if((strtolower($args[0]) === "add" || strtolower($args[0]) === "set")){
           
            if(!isset($args[1])){
                return false;
            }

            if(($sender->hasPermission("teleportguard.warp.set")) || ($sender->hasPermission("teleportguard.admin"))){

                if(!($sender instanceof Player)){
                    $sender->sendMessage(TF::RED."[MeetMate] > Nie możesz użyć tej podkomendy w konsoli");
                    return true;
                }

                $getx = round($sender->getX());
                $gety = round($sender->getY());
                $getz = round($sender->getZ());
                $level = $sender->getLevel()->getName();
                $name = strtolower($args[1]);

                $cfg = $this->main->getDataFolder()."warps.json";
                $json = file_get_contents($cfg);
                $deco = json_decode($json, true);


                if(array_key_exists($name, $deco)){
                $sender->sendMessage(TF::RED."[MeetMate] > Warp o nazwie {$name} już istnieje!");
                return true;
                }
            

                
                
                $data = array("X" => $getx, "Y" => $gety, "Z" => $getz, "World" => $level);
                
                $deco[$name] = $data;
                file_put_contents($cfg, json_encode($deco)); 
                $sender->sendMessage(TF::GREEN."[MeetMate] > Warp o nazwie {$name} został utworzony na X:{$getx} Y:{$gety} Z:{$getz} world: {$level}");
                return true;
           

          }else{
              $sender->sendMessage(TF::RED."[MeetMate] > Nie masz stosownych uprawnień by użyć tej komendy");
          }

        }

        if(strtolower($args[0]) === "list"){
           if(($sender->hasPermission("teleportguard.warp.list")) || ($sender->hasPermission("teleportguard.admin"))){
            $cfg = $this->main->getDataFolder()."warps.json";
            $json = file_get_contents($cfg);
            $deco = json_decode($json, true);
       
            foreach($deco as $name => $full){
                $this->warps[] .= $name;

            }
            if(($this->warps === NULL) || (count($this->warps) === 0)){
                $sender->sendMessage(TF::GREEN."[MeetMate] > Aktualnie nie ma żadnych ustawionych warpów");
                return true;
            }
            

            $list = implode(", ", $this->warps);
            $sender->sendMessage(TF::GREEN."Dostępne warpy: ".$list);
            
            foreach($deco as $name => $full){
               $this->warps = array_diff($this->warps, array($name));

            }
            return true;
           }else{
               $sender->sendMessage(TF::RED."[MeetMate] > Nie masz stosownych uprawnień by wyświelić liste");
               return true;
           }

        }


        if((strtolower($args[0]) === "del" || strtolower($args[0]) === "delete")){
            if(!isset($args[1])){
                return false;
            }

            if(($sender->hasPermission("teleportguard.warp.set")) || ($sender->hasPermission("teleportguard.admin"))){
                $cfg = $this->main->getDataFolder()."warps.json";
                $json = file_get_contents($cfg);
                $deco = json_decode($json, true);
                $name = strtolower($args[1]);  

                if(!array_key_exists($name, $deco)){
                    $sender->sendMessage(TF::RED."[MeetMate] > Warp o nazwie {$name} nie istnieje");
                    return true;
                }
                
                
                    
                     unset($deco[$name]);
                     
                     file_put_contents($cfg, json_encode($deco)); 
                     $sender->sendMessage(TF::GREEN."[MeetMate] > Warp o nazwie {$name} został usunięty");
                     
                    return true;
                 


            }
            
        
        }else{
            if(!($sender instanceof Player)){
                $sender->sendMessage(TF::RED."[MeetMate] > Nie możesz użyć tej podkomendy w konsoli");
                return true;
            }

            $cfg = $this->main->getDataFolder()."warps.json";
            $json = file_get_contents($cfg);
            $deco = json_decode($json, true);
            $name = strtolower($args[0]);

            

            if(!array_key_exists($name, $deco)){
                $sender->sendMessage(TF::RED."[MeetMate] > Warp o nazwie {$name} nie istnieje");
                return true;
            }
            if(($sender->hasPermission("teleportguard.warp.{$name}")) || ($sender->hasPermission("teleportguard.admin"))){

                $x = $deco[$name]["X"];
                $y = $deco[$name]["Y"];
                $z = $deco[$name]["Z"];
                $world = $deco[$name]["World"];
                
                $tp = new Position($x, $y, $z, $this->main->getServer()->getLevelByName($world));
                $sender->teleport($tp);

                $sender->sendMessage(TF::GREEN."[MeetMate] > Teleportowano na warp {$name}");
            }else{
                $sender->sendMessage(TF::RED."[MeetMate] > Nie masz uprawnień by użyć tego warpa");
            }
        }


       return true;
    }
}