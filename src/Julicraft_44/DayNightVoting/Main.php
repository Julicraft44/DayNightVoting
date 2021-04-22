<?php
namespace Julicraft_44\DayNightVoting;


use pocketmine\plugin\PluginBase;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;


class Main extends PluginBase {
    
    public $dayVote = 0;
    public $nightVote = 0;
    
    public function onEnable() {
        $this->saveDefaultConfig();
        $this->getLogger()->info("DayNightVoting enabled");
    }
    
    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool {
        switch($cmd->getName()) {
            case "timevote":
                if($sender instanceof Player) {
                    if($sender->hasPermission("daynightvoting.use")) {
                        $this->getServer()->broadcastMessage($this->getConfig()->get("timevote-start-1"));
                        $this->getServer()->broadcastMessage($this->getConfig()->get("timevote-start-2"));
                        foreach($this->getServer()->getOnlinePlayers() as $p) {
                            $this->registerDayVoteForm($p);
                        }
                            $task = new TimerTask($this, $sender->getName());
                            $this->getScheduler()->scheduleDelayedTask($task, $this->getConfig()->get("voteTime")); //1200 = 1min
                            
                    } else {
                        $sender->sendMessage($this->getConfig()->get("no-perm"));
                    }
                } else {
                    $sender->sendMessage($this->getConfig()->get("no-player"));
                }
                break;
        }
        return true;
    }
    
    public function registerDayVoteForm($player) {
        $form = $this->getServer()->getPluginManager()->getPlugin("FormAPI")->createSimpleForm(function (Player $player, $data) {
           $result = $data; 
           if($result === null) {
               return true;
           }
           switch($result) {
               case 0:
                   $player->sendMessage($this->getConfig()->get("choose-day"));
                   $this->dayVote = $this->dayVote + 1;
                   $msg = $this->getConfig()->get("total-day");
                   $msg = str_replace("%total", "$this->dayVote", $msg);
                   $player->sendMessage($msg);
                   break;
               case 1:
                   $player->sendMessage($this->getConfig()->get("choose-night"));
                   $this->nightVote = $this->nightVote + 1;
                   $msg = $this->getConfig()->get("total-night");
                   $msg = str_replace("%total", "$this->nightVote", $msg);
                   $player->sendMessage($msg);
           }
        });
        $form->setTitle($this->getConfig()->get("form-title"));
        $form->setContent($this->getConfig()->get("form-content"));
        $form->addButton($this->getConfig()->get("form-button-day"));
        $form->addButton($this->getConfig()->get("form-button-night"));
        $form->sendToPlayer($player);
    }   
}