<?php

namespace RedCraftPE;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\utils\TextFormat;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\utils\Config;
use pocketmine\math\Vector3;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use RedCraftPE\Skyblock;
use muqsit\invmenu\{InvMenu, InvMenuHandler};

class crates extends PluginBase implements Listener {

  public function onEnable(): void {

    $this->getServer()->getPluginManager()->registerEvents($this, $this);
    if(!file_exists($this->getDataFolder() . "crates.yml")){

      @mkdir($this->getDataFolder());
      $this->saveResource("crates.yml");
      $this->crates = new Config($this->getDataFolder() . "crates.yml", Config::YAML);
      $this->crates->set("Crates", []);
    } else {

      $this->crates = new Config($this->getDataFolder() . "crates.yml", Config::YAML);
    }
    $this->crates->save();
    $this->crates->reload();
    
    if(!InvMenuHandler::isRegistered()){
      InvMenuHandler::register($this);
    }
    $this->menu = InvMenu::create(InvMenu::TYPE_CHEST);
  }
  public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {

    switch(strtolower($command->getName())) {
      case "crates":

        if (!$args) {

          //Crate GUI below here:
          
          $crateArray = $this->crates->get("Crates");
          $skyKeys = $crateArray[$sender->getName()]["Sky Keys"];
          $voidKeys = $crateArray[$sender->getName()]["Void Keys"];
          $kingKeys = $crateArray[$sender->getName()]["King Keys"];
          $lordKeys = $crateArray[$sender->getName()]["Lord Keys"];
          
          $skyLore = Array(TextFormat::BLUE . $sender->getName() . ": " . TextFormat::WHITE . $skyKeys . TextFormat::BLUE . " Sky Keys");
          $skyCrate = Item::get(Item::CHEST)->setCustomName(TextFormat::BLUE . "The Sky Crate")->setLore($skyLore);
          
          $voidLore = Array(TextFormat::DARK_BLUE . $sender->getName() . ": " . TextFormat::WHITE . $voidKeys . TextFormat::DARK_BLUE . " Void Keys");
          $voidCrate = Item::get(Item::CHEST)->setCustomName(TextFormat::DARK_BLUE . "The Void Crate")->setLore($voidLore);
          
          $kingLore = Array(TextFormat::RED . $sender->getName() . ": " . TextFormat::WHITE . $kingKeys . TextFormat::RED . " King Keys");
          $kingCrate = Item::get(Item::CHEST)->setCustomName(TextFormat::RED . "The King Crate")->setLore($kingLore);
          
          $lordLore = Array(TextFormat::GOLD . $sender->getName() . ": " . TextFormat::WHITE . $lordKeys . TextFormat::GOLD . " Lord Keys");
          $lordCrate = Item::get(Item::CHEST)->setCustomName(TextFormat::GOLD . "The Lord Crate")->setLore($lordLore);
          
          $this->menu->setName(TextFormat::YELLOW . "Cube" . TextFormat::BLUE . "X" . TextFormat::GREEN . " SkyBlock " . TextFormat::YELLOW . "Crates");
          $this->menu->readonly();
          $this->menu->setListener([$this, "onTransaction"]);
          $this->menu->getInventory()->setItem(2, $skyCrate);
          $this->menu->getInventory()->setItem(4, $voidCrate);
          $this->menu->getInventory()->setItem(6, $kingCrate);
          $this->menu->getInventory()->setItem(22, $lordCrate);
          $this->menu->send($sender);
          return true;
        } elseif ($args[0] === "buy") {

          $api = $this->getServer()->getPluginManager()->getPlugin("CubeXSkyblock");
          if (!$api) {

            $sender->sendMessage(TextFormat::RED . "Crates are not available for purchase at this time!");
            return true;
          }

          $cratesArray = $this->crates->get("Crates", []);

          if (!$args[1]) {

            $sender->sendMessage(TextFormat::WHITE . "Usage: /crate buy <crate>");
            return true;
          } else {

            switch($args[1]) {

              case "sky":

                $tokens = $api->getTokens($sender->getName());
                if ($tokens < 1) {

                  $sender->sendMessage(TextFormat::RED . "You do not have enough tokens to purchase this crate!");
                  return true;
                } else {

                  $api->subTokens($sender->getName(), 1);
                  $sender->sendMessage(TextFormat::GREEN . "You have successfully bought the Sky Crate Key for 1 token.");
                  $cratesArray[$sender->getName()]["Sky Keys"] = $cratesArray[$sender->getName()]["Sky Keys"] + 1;
                  $this->crates->set("Crates", $cratesArray);
                  $this->crates->save();
                  return true;
                }
              break;
              case "void":

                $tokens = $api->getTokens($sender->getName());
                if ($tokens < 2) {

                  $sender->sendMessage(TextFormat::RED . "You do not have enough tokens to purchase this crate!");
                  return true;
                } else {

                  $api->subTokens($sender->getName(), 2);
                  $sender->sendMessage(TextFormat::GREEN . "You have successfully bought the Void Crate Key for 2 tokens.");
                  $cratesArray[$sender->getName()]["Void Keys"] = $cratesArray[$sender->getName()]["Void Keys"] + 1;
                  $this->crates->set("Crates", $cratesArray);
                  $this->crates->save();
                  return true;
                }
              break;
              case "king":

                $tokens = $api->getTokens($sender->getName());
                if ($tokens < 4) {

                  $sender->sendMessage(TextFormat::RED . "You do not have enough tokens to purchase this crate!");
                  return true;
                } else {

                  $api->subTokens($sender->getName(), 4);
                  $sender->sendMessage(TextFormat::GREEN . "You have successfully bought the King Crate Key for 4 tokens.");
                  $cratesArray[$sender->getName()]["King Keys"] = $cratesArray[$sender->getName()]["King Keys"] + 1;
                  $this->crates->set("Crates", $cratesArray);
                  $this->crates->save();
                  return true;
                }
              break;
              case "lord":

                $tokens = $api->getTokens($sender->getName());
                if ($tokens < 8) {

                  $sender->sendMessage(TextFormat::RED . "You do not have enough tokens to purchase this crate!");
                  return true;
                } else {

                  $api->subTokens($sender->getName(), 8);
                  $sender->sendMessage(TextFormat::GREEN . "You have successfully bought the Sky Crate Key for 8 tokens.");
                  $cratesArray[$sender->getName()]["Lord Keys"] = $cratesArray[$sender->getName()]["Lord Keys"] + 1;
                  $this->crates->set("Crates", $cratesArray);
                  $this->crates->save();
                  return true;
                }
              break;
            }
          }
        } elseif ($args[0] === "keys") {

          $cratesArray = $this->crates->get("Crates", []);

          $skyKeys = $cratesArray[$sender->getName()]["Sky Keys"];
          $voidKeys = $cratesArray[$sender->getName()]["Void Keys"];
          $kingKeys = $cratesArray[$sender->getName()]["King Keys"];
          $lordKeys = $cratesArray[$sender->getName()]["Lord Keys"];

          $sender->sendMessage(TextFormat::GREEN . "Your Keys: \n" . TextFormat::BLUE . "Sky Keys: " . $skyKeys . "\n" . TextFormat::DARK_BLUE . "Void Keys: " . $voidKeys . "\n" . TextFormat::RED . "King Keys: " . $kingKeys . "\n" . TextFormat::GOLD . "Lord Keys: " . $lordKeys);
          return true;
        }
      break;
    }
    return false;
  }
  public function onJoin(PlayerJoinEvent $event) {

    $cratesArray = $this->crates->get("Crates", []);
    $player = $event->getPlayer();

    if (array_key_exists($player->getName(), $cratesArray)) {

      return;
    } else {

      $cratesArray[$player->getName()] = Array("Sky Keys" => 0, "Void Keys" => 0, "King Keys" => 0, "Lord Keys" => 0);
      $this->crates->set("Crates", $cratesArray);
      $this->crates->save();
      return;
    }
  }
  public function onTransaction(Player $sender, Item $itemClickedOn, Item $itemClickedWith, SlotChangeAction $inventoryAction): bool {
  
    if ($itemClickedOn->getName() === TextFormat::BLUE . "The Sky Crate") {
      
      $this->menu->getInventory()->setItem(1, Item::get(223,0,1));
      $this->menu->getInventory()->setItem(3, Item::get(223,0,1));
      $this->menu->getInventory()->setItem(10, Item::get(223,0,1));
      $this->menu->getInventory()->setItem(11, Item::get(223,0,1));
      $this->menu->getInventory()->setItem(12, Item::get(223,0,1));
      usleep(200000);
      $this->menu->getInventory()->setItem(0, Item::get(223,0,1));
      $this->menu->getInventory()->setItem(9, Item::get(223,0,1));
      $this->menu->getInventory()->setItem(18, Item::get(223,0,1));
      $this->menu->getInventory()->setItem(19, Item::get(223,0,1));
      $this->menu->getInventory()->setItem(20, Item::get(223,0,1));
      $this->menu->getInventory()->setItem(21, Item::get(223,0,1));
      $this->menu->getInventory()->setItem(22, Item::get(223,0,1));
      $this->menu->getInventory()->setItem(4, Item::get(223,0,1));
      $this->menu->getInventory()->setItem(13, Item::get(223,0,1));
      usleep(200000);
      $this->menu->getInventory()->setItem(5, Item::get(223,0,1));
      $this->menu->getInventory()->setItem(14, Item::get(223,0,1));
      $this->menu->getInventory()->setItem(23, Item::get(223,0,1));
      usleep(200000);
      $this->menu->getInventory()->setItem(6, Item::get(223,0,1));
      $this->menu->getInventory()->setItem(15, Item::get(223,0,1));
      $this->menu->getInventory()->setItem(24, Item::get(223,0,1));
      usleep(200000);
      $this->menu->getInventory()->setItem(7, Item::get(223,0,1));
      $this->menu->getInventory()->setItem(16, Item::get(223,0,1));
      $this->menu->getInventory()->setItem(25, Item::get(223,0,1));
      usleep(200000);
      $this->menu->getInventory()->setItem(8, Item::get(223,0,1));
      $this->menu->getInventory()->setItem(17, Item::get(223,0,1));
      $this->menu->getInventory()->setItem(26, Item::get(223,0,1));
      usleep(200000);
      $this->menu->getInventory()->clearAll();
      return true;
    }
    if ($itemClickedOn->getName() === TextFormat::DARK_BLUE . "The Void Crate") {
      
      $this->menu->getInventory()->setItem(3, Item::get(231,0,1));
      $this->menu->getInventory()->setItem(5, Item::get(231,0,1));
      $this->menu->getInventory()->setItem(12, Item::get(231,0,1));
      $this->menu->getInventory()->setItem(13, Item::get(231,0,1));
      $this->menu->getInventory()->setItem(14, Item::get(231,0,1));
      usleep(200000);
      $this->menu->getInventory()->setItem(2, Item::get(231,0,1));
      $this->menu->getInventory()->setItem(6, Item::get(231,0,1));
      $this->menu->getInventory()->setItem(11, Item::get(231,0,1));
      $this->menu->getInventory()->setItem(15, Item::get(231,0,1));
      $this->menu->getInventory()->setItem(20, Item::get(231,0,1));
      $this->menu->getInventory()->setItem(21, Item::get(231,0,1));
      $this->menu->getInventory()->setItem(22, Item::get(231,0,1));
      $this->menu->getInventory()->setItem(23, Item::get(231,0,1));
      $this->menu->getInventory()->setItem(24, Item::get(231,0,1));
      usleep(200000);
      $this->menu->getInventory()->setItem(1, Item::get(231,0,1));
      $this->menu->getInventory()->setItem(10, Item::get(231,0,1));
      $this->menu->getInventory()->setItem(19, Item::get(231,0,1));
      $this->menu->getInventory()->setItem(7, Item::get(231,0,1));
      $this->menu->getInventory()->setItem(16, Item::get(231,0,1));
      $this->menu->getInventory()->setItem(25, Item::get(231,0,1));
      usleep(200000);
      $this->menu->getInventory()->setItem(0, Item::get(231,0,1));
      $this->menu->getInventory()->setItem(9, Item::get(231,0,1));
      $this->menu->getInventory()->setItem(18, Item::get(231,0,1));
      $this->menu->getInventory()->setItem(8, Item::get(231,0,1));
      $this->menu->getInventory()->setItem(17, Item::get(231,0,1));
      $this->menu->getInventory()->setItem(26, Item::get(231,0,1));
      usleep(200000);
      $this->menu->getInventory()->clearAll();
      return true;
    }
    if ($itemClickedOn->getName() === TextFormat::RED . "The King Crate") {
      
      $this->menu->getInventory()->setItem(5, Item::get(234,0,1));
      $this->menu->getInventory()->setItem(7, Item::get(234,0,1));
      $this->menu->getInventory()->setItem(14, Item::get(234,0,1));
      $this->menu->getInventory()->setItem(15, Item::get(234,0,1));
      $this->menu->getInventory()->setItem(16, Item::get(234,0,1));
      usleep(200000);
      $this->menu->getInventory()->setItem(4, Item::get(234,0,1));
      $this->menu->getInventory()->setItem(13, Item::get(234,0,1));
      $this->menu->getInventory()->setItem(22, Item::get(234,0,1));
      $this->menu->getInventory()->setItem(23, Item::get(234,0,1));
      $this->menu->getInventory()->setItem(24, Item::get(234,0,1));
      $this->menu->getInventory()->setItem(25, Item::get(234,0,1));
      $this->menu->getInventory()->setItem(26, Item::get(234,0,1));
      $this->menu->getInventory()->setItem(8, Item::get(234,0,1));
      $this->menu->getInventory()->setItem(17, Item::get(234,0,1));
      usleep(200000);
      $this->menu->getInventory()->setItem(3, Item::get(234,0,1));
      $this->menu->getInventory()->setItem(12, Item::get(234,0,1));
      $this->menu->getInventory()->setItem(21, Item::get(234,0,1));
      usleep(200000);
      $this->menu->getInventory()->setItem(2, Item::get(234,0,1));
      $this->menu->getInventory()->setItem(11, Item::get(234,0,1));
      $this->menu->getInventory()->setItem(20, Item::get(234,0,1));
      usleep(200000);
      $this->menu->getInventory()->setItem(1, Item::get(234,0,1));
      $this->menu->getInventory()->setItem(10, Item::get(234,0,1));
      $this->menu->getInventory()->setItem(19, Item::get(234,0,1));
      usleep(200000);
      $this->menu->getInventory()->setItem(0, Item::get(234,0,1));
      $this->menu->getInventory()->setItem(9, Item::get(234,0,1));
      $this->menu->getInventory()->setItem(18, Item::get(234,0,1));
      usleep(200000);
      $this->menu->getInventory()->clearAll();
      return true;
    }
    if ($itemClickedOn->getName() === TextFormat::GOLD . "The Lord Crate") {
      
      $this->menu->getInventory()->setItem(21, Item::get(224,0,1));
      $this->menu->getInventory()->setItem(23, Item::get(224,0,1));
      $this->menu->getInventory()->setItem(12, Item::get(224,0,1));
      $this->menu->getInventory()->setItem(13, Item::get(224,0,1));
      $this->menu->getInventory()->setItem(14, Item::get(224,0,1));
      usleep(200000);
      $this->menu->getInventory()->setItem(2, Item::get(224,0,1));
      $this->menu->getInventory()->setItem(11, Item::get(224,0,1));
      $this->menu->getInventory()->setItem(20, Item::get(224,0,1));
      $this->menu->getInventory()->setItem(3, Item::get(224,0,1));
      $this->menu->getInventory()->setItem(4, Item::get(224,0,1));
      $this->menu->getInventory()->setItem(5, Item::get(224,0,1));
      $this->menu->getInventory()->setItem(6, Item::get(224,0,1));
      $this->menu->getInventory()->setItem(15, Item::get(224,0,1));
      $this->menu->getInventory()->setItem(24, Item::get(224,0,1));
      usleep(200000);
      $this->menu->getInventory()->setItem(1, Item::get(224,0,1));
      $this->menu->getInventory()->setItem(10, Item::get(224,0,1));
      $this->menu->getInventory()->setItem(19, Item::get(224,0,1));
      $this->menu->getInventory()->setItem(7, Item::get(224,0,1));
      $this->menu->getInventory()->setItem(16, Item::get(224,0,1));
      $this->menu->getInventory()->setItem(25, Item::get(224,0,1));
      usleep(200000);
      $this->menu->getInventory()->setItem(0, Item::get(224,0,1));
      $this->menu->getInventory()->setItem(9, Item::get(224,0,1));
      $this->menu->getInventory()->setItem(18, Item::get(224,0,1));
      $this->menu->getInventory()->setItem(8, Item::get(224,0,1));
      $this->menu->getInventory()->setItem(17, Item::get(224,0,1));
      $this->menu->getInventory()->setItem(26, Item::get(224,0,1));
      usleep(200000);
      $this->menu->getInventory()->clearAll();
      return true;
    }
    return false;
  }
}
