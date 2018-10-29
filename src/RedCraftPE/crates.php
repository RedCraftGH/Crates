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
  }
  public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {

    switch(strtolower($command->getName())) {
      case "crates":

        if (!$args) {

          //Crate GUI below here:
          
          $crateArray = $this->crates->get("Crates");
          $skyKeys = $crateArray[$sender->getName()]["Sky Keys"];
          $voidKeys = $crateArray[$sender->getName()]["Sky Keys"];
          $kingKeys = $crateArray[$sender->getName()]["Sky Keys"];
          $lordKeys = $crateArray[$sender->getName()]["Sky Keys"];
          
          $skyLore = Array(TextFormat::BLUE . $sender->getName() . ": " . TextFormat::WHITE . $skyKeys . TextFormat::BLUE . " Sky Keys");
          $skyCrate = Item::get(Item::CHEST)->setCustomName(TextFormat::BLUE . "The Sky Crate")->setLore($skyLore);
          
          $voidLore = Array(TextFormat::DARK_BLUE . $sender->getName() . ": " . TextFormat::WHITE . $voidKeys . TextFormat::DARK_BLUE . " Void Keys");
          $voidCrate = Item::get(Item::CHEST)->setCustomName(TextFormat::DARK_BLUE . "The Void Crate")->setLore($voidLore);
          
          $kingLore = Array(TextFormat::RED . $sender->getName() . ": " . TextFormat::WHITE . $kingKeys . TextFormat::RED . " King Keys");
          $kingCrate = Item::get(Item::CHEST)->setCustomName(TextFormat::RED . "The King Crate")->setLore($kingLore);
          
          $lordLore = Array(TextFormat::GOLD . $sender->getName() . ": " . TextFormat::WHITE . $lordKeys . TextFormat::GOLD . " Lord Keys");
          $lordCrate = Item::get(Item::CHEST)->setCustomName(TextFormat::GOLD . "The Lord Crate")->setLore($lordLore);
          
          $menu = InvMenu::create(InvMenu::TYPE_CHEST);
          $menu->setName(TextFormat::YELLOW . "Cube" . TextFormat::BLUE . "X" . TextFormat::GREEN . " SkyBlock " . TextFormat::YELLOW . "Crates");
          $menu->readonly();
          $menu->getInventory()->setItem(2, $skyCrate);
          $menu->getInventory()->setItem(4, $voidCrate);
          $menu->getInventory()->setItem(6, $kingCrate);
          $menu->getInventory()->setItem(22, $lordCrate);
          $menu->send($sender);
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
  public function onInteract(PlayerInteractEvent $event) {

    $block = $event->getBlock();
    $player = $event->getPlayer();
    $cratesArray = $this->crates->get("Crates", []);

    $skyKeys = $cratesArray[$player->getName()]["Sky Keys"];
    $voidKeys = $cratesArray[$player->getName()]["Void Keys"];
    $kingKeys = $cratesArray[$player->getName()]["King Keys"];
    $lordKeys = $cratesArray[$player->getName()]["Lord Keys"];
    if ($player->getLevel()->getName() === "Skyblock" && $block->getX() === 7 && $block->getY() === 27 && $block->getZ() === 6 && $block->getID() === 54) {

      $event->setCancelled(true);
      if ($skyKeys < 1) {

        $player->sendMessage(TextFormat::RED . "You do not have a Sky Crate Key.");
        return;
      } else {

        for ($i = 1; $i <= 10; $i++) {

          usleep(200000);

          $rand = rand(1,10);
          if ($rand === 1) {

            $player->addTitle(TextFormat::WHITE . "You win: 5 Diamonds", "", 5, 20, 5);

            if ($i === 10) {

              $player->addTitle(TextFormat::WHITE . "You win: 5 Diamonds", "", 20, 20, 20);
              $player->getInventory()->addItem(Item::get(264,0,5));
            }
          } elseif ($rand === 2) {

            $player->addTitle(TextFormat::RED . "You win: 5 Diamonds", "", 5, 20, 5);

            if ($i === 10) {

              $player->addTitle(TextFormat::RED . "You win: 5 Diamonds", "", 20, 20, 20);
              $player->getInventory()->addItem(Item::get(264,0,5));
            }
          } elseif ($rand === 3) {

            $player->addTitle(TextFormat::BLUE . "You win: 5 Diamonds", "", 5, 20, 5);

            if ($i === 10) {

              $player->addTitle(TextFormat::BLUE . "You win: 5 Diamonds", "", 20, 20, 20);
              $player->getInventory()->addItem(Item::get(264,0,5));
            }
          } elseif ($rand === 4) {

            $player->addTitle(TextFormat::GOLD . "You win: 5 Diamonds", "", 5, 20, 5);

            if ($i === 10) {

              $player->addTitle(TextFormat::GOLD . "You win: 5 Diamonds", "", 20, 20, 20);
              $player->getInventory()->addItem(Item::get(264,0,5));
            }
          } elseif ($rand === 5) {

            $player->addTitle(TextFormat::LIGHT_PURPLE . "You win: 5 Diamonds", "", 5, 20, 5);

            if ($i === 10) {

              $player->addTitle(TextFormat::LIGHT_PURPLE . "You win: 5 Diamonds", "", 20, 20, 20);
              $player->getInventory()->addItem(Item::get(264,0,5));
            }
          } elseif ($rand === 6) {

            $player->addTitle(TextFormat::GREEN . "You win: 5 Diamonds", "", 5, 20, 5);

            if ($i === 10) {

              $player->addTitle(TextFormat::GREEN . "You win: 5 Diamonds", "", 20, 20, 20);
              $player->getInventory()->addItem(Item::get(264,0,5));
            }
          } elseif ($rand === 7) {

            $player->addTitle(TextFormat::AQUA . "You win: 5 Diamonds", "", 5, 20, 5);

            if ($i === 10) {

              $player->addTitle(TextFormat::AQUA . "You win: 5 Diamonds", "", 20, 20, 20);
              $player->getInventory()->addItem(Item::get(264,0,5));
            }
          } elseif ($rand === 8) {

            $player->addTitle(TextFormat::YELLOW . "You win: 5 Diamonds", "", 5, 20, 5);

            if ($i === 10) {

              $player->addTitle(TextFormat::YELLOW . "You win: 5 Diamonds", "", 20, 20, 20);
              $player->getInventory()->addItem(Item::get(264,0,5));
            }
          } elseif ($rand === 9) {

            $player->addTitle(TextFormat::DARK_RED . "You win: 5 Diamonds", "", 5, 20, 5);

            if ($i === 10) {

              $player->addTitle(TextFormat::DARK_RED . "You win: 5 Diamonds", "", 20, 20, 20);
              $player->getInventory()->addItem(Item::get(264,0,5));
            }
          } elseif ($rand === 10) {

            $player->addTitle(TextFormat::DARK_GREEN . "You win: 5 Diamonds", "", 5, 20, 5);

            if ($i === 10) {

              $player->addTitle(TextFormat::DARK_GREEN . "You win: 5 Diamonds", "", 20, 20, 20);
              $player->getInventory()->addItem(Item::get(264,0,5));
            }
          }
        }
        $cratesArray[$player->getName()]["Sky Keys"] = $cratesArray[$player->getName()]["Sky Keys"] - 1;
        $this->crates->set("Crates", $cratesArray);
        $this->crates->save();
        return;
      }
    }
  }
}
