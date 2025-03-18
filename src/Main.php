<?php

declare(strict_types=1);

namespace lokiPM\PerWorldAlwaysTime;

use pocketmine\plugin\PluginBase;
use pocketmine\world\World;
use pocketmine\scheduler\Task;
use pocketmine\utils\Config;

class Main extends PluginBase {

    /** @var array */
    private $worldTimes = [];

    public function onEnable(): void {
        $this->saveDefaultConfig();
        $this->loadWorldTimes();
        $this->getScheduler()->scheduleRepeatingTask(new SetWorldTimeTask($this), 20); // Alle 20 Ticks (1 Sekunde)
    }

    private function loadWorldTimes(): void {
        $config = $this->getConfig();
        $this->worldTimes = $config->get("worlds", []);
    }

    public function getWorldTimes(): array {
        return $this->worldTimes;
    }
}

class SetWorldTimeTask extends Task {

    /** @var Main */
    private $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function onRun(): void {
        $worldTimes = $this->plugin->getWorldTimes();
        foreach ($worldTimes as $worldName => $time) {
            $world = $this->plugin->getServer()->getWorldManager()->getWorldByName($worldName);
            if ($world instanceof World) {
                $world->setTime($time === "day" ? 1000 : 13000); // 1000 = Tag, 13000 = Nacht
                $world->stopTime(); // Stoppt die Zeit, damit sie nicht weiterläuft
            }
        }
    }
}