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


class Warp extends Command implements PluginOwned{
    use PluginOwnedTrait;


public $main;
public $warps;
  
    public function __construct(Main $main) {
        parent::__construct("warp");
        $this->setDescription("Warp main command");
        $this->setUsage("/warp <name> | /warp set <name>| /warp del <name> | /warp list");
        $this->main = $main;
        
    }
    

    public function execute(CommandSender $sender, string $label, array $args) : bool {
        if(!isset($args[0])){ 
            throw new InvalidCommandSyntaxException;
            return false;
        }
   
        
          if((strtolower($args[0]) === "add" || strtolower($args[0]) === "set")){
           
            if(!isset($args[1])){ 
                throw new InvalidCommandSyntaxException;
                return false;
            }

            if(($sender->hasPermission("teleportguard.warp.set")) || ($sender->hasPermission("teleportguard.admin"))){

                if(!($sender instanceof Player)){
                    $sender->sendMessage(TF::RED."[TeleportGuard] > You can't use that in console!");
                    return true;
                }

                $getx = round($sender->getPosition()->getX());
                $gety = round($sender->getPosition()->getY());
                $getz = round($sender->getPosition()->getZ());
                $level = $sender->getWorld()->getFolderName();
                $name = strtolower($args[1]);

                $cfg = $this->main->getDataFolder()."warps.json";
                $json = file_get_contents($cfg);
                $deco = json_decode($json, true);


                if(array_key_exists($name, $deco)){
                $sender->sendMessage(TF::RED."[TeleportGuard] > Warp with name {$name} already exists!");
                return true;
                }
            

                
                
                $data = array("X" => $getx, "Y" => $gety, "Z" => $getz, "World" => $level);
                
                $deco[$name] = $data;
                file_put_contents($cfg, json_encode($deco)); 
                $sender->sendMessage(TF::GREEN."[TeleportGuard] > Warp with name {$name} has been created on X:{$getx} Y:{$gety} Z:{$getz} world: {$level}");
                return true;
           

          }else{
              $sender->sendMessage(TF::RED."[TeleportGuard] > You don't have permission to use this command");
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
                $sender->sendMessage(TF::GREEN."[TeleportGuard] > There are currently no warps set");
                return true;
            }
            

            $list = implode(", ", $this->warps);
            $sender->sendMessage(TF::GREEN."Warps: ".$list);
            
            foreach($deco as $name => $full){
               $this->warps = array_diff($this->warps, array($name));

            }
            return true;
           }else{
               $sender->sendMessage(TF::RED."[TeleportGuard] > You don't have permission to use this command");
               return true;
           }

        }


        if((strtolower($args[0]) === "del" || strtolower($args[0]) === "delete")){
            if(!isset($args[1])){ 
                throw new InvalidCommandSyntaxException;
                return false;
            }

            if(($sender->hasPermission("teleportguard.warp.set")) || ($sender->hasPermission("teleportguard.admin"))){
                $cfg = $this->main->getDataFolder()."warps.json";
                $json = file_get_contents($cfg);
                $deco = json_decode($json, true);
                $name = strtolower($args[1]);  

                if(!array_key_exists($name, $deco)){
                    $sender->sendMessage(TF::RED."[TeleportGuard] > Warp with name {$name} already exists");
                    return true;
                }
                
                
                    
                     unset($deco[$name]);
                     
                     file_put_contents($cfg, json_encode($deco)); 
                     $sender->sendMessage(TF::GREEN."[TeleportGuard] > Warp with name {$name} has been deleted");
                     
                    return true;
                 


            }
            
        
        }else{
            if(!($sender instanceof Player)){
                $sender->sendMessage(TF::RED."[TeleportGuard] > You can't use that in console!");
                return true;
            }

            $cfg = $this->main->getDataFolder()."warps.json";
            $json = file_get_contents($cfg);
            $deco = json_decode($json, true);
            $name = strtolower($args[0]);

            

            if(!array_key_exists($name, $deco)){
                $sender->sendMessage(TF::RED."[TeleportGuard] > Warp with name {$name} not exists");
                return true;
            }
            if(($sender->hasPermission("teleportguard.warp.{$name}")) || ($sender->hasPermission("teleportguard.admin"))){

                $x = $deco[$name]["X"];
                $y = $deco[$name]["Y"];
                $z = $deco[$name]["Z"];
                $world = $deco[$name]["World"];
                $level = Server::getInstance()->getWorldManager()->getWorldByName($world);
                $tp = new Vector3($x, $y, $z, $level);
                $sender->teleport($tp);

                $sender->sendMessage(TF::GREEN."[TeleportGuard] > Teleported to warp {$name}");
            }else{
                $sender->sendMessage(TF::RED."[TeleportGuard] > You don't have permission to use this warp");
            }
        }


       return true;
    }
}