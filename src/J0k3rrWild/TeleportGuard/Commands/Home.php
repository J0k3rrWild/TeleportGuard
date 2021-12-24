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


class Home extends PluginCommand implements CommandExecutor{

public $main;
public $homes;
  
    public function __construct(Main $main) {
        parent::__construct("home", $main);
        $this->setDescription("Ustawia home");
        $this->setUsage("/home <name> | /home remove <name>| /home set <name> | /home list <name>");
        $this->main = $main;
        
    }
    

    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args) : bool {
        
        if(!($sender instanceof Player)){
            $sender->sendMessage(TF::RED."[MeetMate] > Nie możesz używać tej komendy w konsoli!");
            return true;
        }

        if(!isset($args[0])) return false; 

        if((strtolower($args[0]) === "add" || strtolower($args[0]) === "set")){
            if(!isset($args[1])){
                return false;
            }
            
                $getx = round($sender->getX());
                $gety = round($sender->getY());
                $getz = round($sender->getZ());
                $level = $sender->getLevel()->getName();
                $name = strtolower($args[1]);

                $cfg = $this->main->getDataFolder()."players/". strtolower($sender->getPlayer()->getName()). "/homes.json";
                $json = file_get_contents($cfg);
                $deco = json_decode($json, true);


                if(array_key_exists($name, $deco)){
                $sender->sendMessage(TF::RED."[MeetMate] > Home o nazwie {$name} już istnieje!");
                return true;
                }
                
                $data = array("X" => $getx, "Y" => $gety, "Z" => $getz, "World" => $level);
                
                $deco[$name] = $data;
                file_put_contents($cfg, json_encode($deco)); 
                $sender->sendMessage(TF::GREEN."[MeetMate] > Home o nazwie {$name} został utworzony na X:{$getx} Y:{$gety} Z:{$getz} world: {$level}");
                return true;

                


        
        }
        if(strtolower($args[0]) === "list"){
        
            $cfg = $this->main->getDataFolder()."players/". strtolower($sender->getPlayer()->getName()). "/homes.json";
            $json = file_get_contents($cfg);
            $deco = json_decode($json, true);
       
            foreach($deco as $name => $full){
                $this->homes[] .= $name;

            }
            if(($this->homes === NULL) || (count($this->homes) === 0)){
                $sender->sendMessage(TF::GREEN."[MeetMate] > Aktualnie nie masz żadnych ustawionych homów");
                return true;
            }
            

            $list = implode(", ", $this->homes);
            $sender->sendMessage(TF::GREEN."Dostępne homy: ".$list);
            
            foreach($deco as $name => $full){
               $this->homes = array_diff($this->homes, array($name));

            }
            return true;

        
        }
        if((strtolower($args[0]) === "del" || strtolower($args[0]) === "delete")){
            if(!isset($args[1])){
                return false;
            }
                $cfg = $this->main->getDataFolder()."players/". strtolower($sender->getPlayer()->getName()). "/homes.json";
                $json = file_get_contents($cfg);
                $deco = json_decode($json, true);
                $name = strtolower($args[1]);  

                if(!array_key_exists($name, $deco)){
                    $sender->sendMessage(TF::RED."[MeetMate] > Home o nazwie {$name} nie istnieje");
                    return true;
                }
                
                
                    
                     unset($deco[$name]);
                     
                     file_put_contents($cfg, json_encode($deco)); 
                     $sender->sendMessage(TF::GREEN."[MeetMate] > Home o nazwie {$name} został usunięty");
                     
                    return true;
            }else{
        
    


                $cfg = $this->main->getDataFolder()."players/". strtolower($sender->getPlayer()->getName()). "/homes.json";
                $json = file_get_contents($cfg);
                $deco = json_decode($json, true);
                $name = strtolower($args[0]);

                

                if(!array_key_exists($name, $deco)){
                    $sender->sendMessage(TF::RED."[MeetMate] > Home o nazwie {$name} nie istnieje");
                    return true;
                }
                

                    $x = $deco[$name]["X"];
                    $y = $deco[$name]["Y"];
                    $z = $deco[$name]["Z"];
                    $world = $deco[$name]["World"];
                    
                    $tp = new Position($x, $y, $z, $this->main->getServer()->getLevelByName($world));
                    $sender->teleport($tp);

                    $sender->sendMessage(TF::GREEN."[MeetMate] > Teleportowano na home {$name}");

    
  } 
  return true;
 } 
}