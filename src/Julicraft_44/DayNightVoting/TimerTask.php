<?php
namespace Julicraft_44\DayNightVoting;

use pocketmine\scheduler\Task;
use Julicraft_44\DayNightVoting\Main;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class TimerTask extends Task {
    
    public function __construct(Main $main, string $playername) {
        $this->main = $main;
        $this->playername = $playername;
    }
    
    public function onRun(int $currentTick) {
        $player = $this->getOwner()->getServer()->getPlayer($this->playername);
        $totalday = $this->getOwner()->dayVote;
        $totalnight = $this->getOwner()->nightVote;
        if($player instanceof Player) {
            
            if($this->getOwner()->dayVote > $this->getOwner()->nightVote) {
                foreach($this->getOwner()->getServer()->getOnlinePlayers() as $p) {
                    $p->getLevel()->setTime($this->getOwner()->getConfig()->get("setDayTime"));
                }
                $msg = $this->getOwner()->getConfig()->get("daytime-win");
                $msg = str_replace("%total", "$totalday", $msg);
                $this->getOwner()->getServer()->broadcastMessage($msg);
                $this->getOwner()->dayVote = 0;
                $this->getOwner()->nightVote = 0;
                
            } elseif($this->getOwner()->dayVote < $this->getOwner()->nightVote) {
                foreach($this->getOwner()->getServer()->getOnlinePlayers() as $p) {
                    $p->getLevel()->setTime($this->getOwner()->getConfig()->get("setNightTime"));
                }
                $msg = $this->getOwner()->getConfig()->get("nighttime-win");
                $msg = str_replace("%total", "$totalnight", $msg);
                $this->getOwner()->getServer()->broadcastMessage($msg);
                $this->getOwner()->dayVote = 0;
                $this->getOwner()->nightVote = 0;
                
            } elseif ($this->getOwner()->dayVote === $this->getOwner()->nightVote){
                $this->getOwner()->getServer()->broadcastMessage($this->getOwner()->getConfig()->get("tie-win"));
                $this->getOwner()->dayVote = 0;
                $this->getOwner()->nightVote = 0;
                
            } else {
                $this->getOwner()->getServer()->broadcastMessage($this->getOwner()->getConfig()->get("error"));
                $this->getOwner()->dayVote = 0;
                $this->getOwner()->nightVote = 0;
            }
        }
    }
    
    public function getOwner():Main {
        return $this->main;
    }

     
}

