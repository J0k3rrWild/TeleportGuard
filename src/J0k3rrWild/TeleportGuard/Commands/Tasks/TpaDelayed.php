<?php 

namespace J0k3rrWild\TeleportGuard\Commands\Tasks; 

use pocketmine\scheduler\Task;
use pocketmine\level\particle\FloatingTextParticle; 
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use J0k3rrWild\TeleportGuard\Commands\Tpa;
use pocketmine\utils\TextFormat as TF;
use pocketmine\utils\Config;

class TpaDelayed extends Task{



    public function __construct(Tpa $plugin, $cfg, $target, $sender, $arg = NULL){ 
       $this->plugin = $plugin; 
       $this->target = $target;
       $this->sender = $sender;
       $this->arg = $arg;
  
       
    } 


    public function onRun(int $tick){ 

      $cfg = new Config($this->plugin->main->getDataFolder()."players/". strtolower($this->sender->getName()) . "/temp.yml", Config::YAML);
      $cfg->set("TpaCooldown", false);
      $cfg->save();
      
      $jcfg = $this->plugin->main->getDataFolder()."players/". strtolower($this->target->getName()) . "/temp.json";
                
      $json = file_get_contents($jcfg);
      $deco = json_decode($json, true);



      if(strtolower($this->arg) === "here"){
        
         $new = array_diff($deco, array($this->sender->getName()."_here")); 
         file_put_contents($jcfg, json_encode($new)); 
         return true;
      
      }else{
          
         $new = array_diff($deco, array($this->sender->getName())); 
         file_put_contents($jcfg, json_encode($new)); 
      }
     
      
     }
        

}