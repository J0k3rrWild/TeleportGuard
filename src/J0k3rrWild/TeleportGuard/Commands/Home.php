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


class Home extends Command implements PluginOwned{
    use PluginOwnedTrait;

public $main;
public $homes;
  
    public function __construct(Main $main) {
        parent::__construct("home");
        $this->setDescription("Home main command");
        $this->setUsage("/home <name> | /home remove <name>| /home set <name> | /home list");
        $this->main = $main;
        
    }
    

    public function execute(CommandSender $sender, string $label, array $args) : bool {
        
        if(!($sender instanceof Player)){
            $sender->sendMessage(TF::RED."[TeleportGuard] > You can't use that in console!");
            return true;
        }

        if(!isset($args[0])){ 
            throw new InvalidCommandSyntaxException;
            return false;
        }
  
        if((strtolower($args[0]) === "add" || strtolower($args[0]) === "set")){
            if(!isset($args[1])){ 
                throw new InvalidCommandSyntaxException;
                return false;
        }
            
                $getx = round($sender->getPosition()->getX());
                $gety = round($sender->getPosition()->getY());
                $getz = round($sender->getPosition()->getZ());
                $level = $sender->getWorld()->getFolderName();
                $name = strtolower($args[1]);

                $cfg = $this->main->getDataFolder()."players/". strtolower($sender->getName()). "/homes.json";
                $json = file_get_contents($cfg);
                $deco = json_decode($json, true);


                if(array_key_exists($name, $deco)){
                $sender->sendMessage(TF::RED."[TeleportGuard] > Home with name {$name} already exists!");
                return true;
                }
                
                $data = array("X" => $getx, "Y" => $gety, "Z" => $getz, "World" => $level);
                
                $deco[$name] = $data;
                file_put_contents($cfg, json_encode($deco)); 
                $sender->sendMessage(TF::GREEN."[TeleportGuard] > Home with name {$name} has been created on X:{$getx} Y:{$gety} Z:{$getz} world: {$level}");
                return true;

                


        
        }
        if(strtolower($args[0]) === "list"){
        
            $cfg = $this->main->getDataFolder()."players/". strtolower($sender->getName()). "/homes.json";
            $json = file_get_contents($cfg);
            $deco = json_decode($json, true);
       
            foreach($deco as $name => $full){
                $this->homes[] .= $name;

            }
            if(($this->homes === NULL) || (count($this->homes) === 0)){
                $sender->sendMessage(TF::GREEN."[TeleportGuard] > You dont have any homes");
                return true;
            }
            

            $list = implode(", ", $this->homes);
            $sender->sendMessage(TF::GREEN."Your homes: ".$list);
            
            foreach($deco as $name => $full){
               $this->homes = array_diff($this->homes, array($name));

            }
            return true;

        
        }
        if((strtolower($args[0]) === "del" || strtolower($args[0]) === "delete")){
            if(!isset($args[1])){
                return false;
            }
                $cfg = $this->main->getDataFolder()."players/". strtolower($sender->getName()). "/homes.json";
                $json = file_get_contents($cfg);
                $deco = json_decode($json, true);
                $name = strtolower($args[1]);  

                if(!array_key_exists($name, $deco)){
                    $sender->sendMessage(TF::RED."[TeleportGuard] > Home with name {$name} not exists");
                    return true;
                }
                
                
                    
                     unset($deco[$name]);
                     
                     file_put_contents($cfg, json_encode($deco)); 
                     $sender->sendMessage(TF::GREEN."[TeleportGuard] > Home with name {$name} has been deleted");
                     
                    return true;
            }else{
        
    


                $cfg = $this->main->getDataFolder()."players/". strtolower($sender->getName()). "/homes.json";
                $json = file_get_contents($cfg);
                $deco = json_decode($json, true);
                $name = strtolower($args[0]);

                

                if(!array_key_exists($name, $deco)){
                    $sender->sendMessage(TF::RED."[TeleportGuard] > Home with name {$name} already exists");
                    return true;
                }
                

                    $x = $deco[$name]["X"];
                    $y = $deco[$name]["Y"];
                    $z = $deco[$name]["Z"];
                    $world = $deco[$name]["World"];
                    

                    $level = Server::getInstance()->getWorldManager()->getWorldByName($world);
                    $vect = new Vector3($x, $y, $z, $level);
                    $sender->teleport($vect);

                    $sender->sendMessage(TF::GREEN."[TeleportGuard] > Teleported to home {$name}");

    
  } 
  return true;
 } 
}