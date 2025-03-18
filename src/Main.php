<?php

declare(strict_types=1);

namespace lokiPM\PerWorldAlwaysTime;

use pocketmine\plugin\PluginBase;
use pocketmine\world\World;
use pocketmine\scheduler\Task;

class Main extends PluginBase {

    private $worldTimes = [];

    public function onEnable(): void {
        $this->saveDefaultConfig();
        $this->loadWorldTimes();
        $this->getScheduler()->scheduleRepeatingTask(new SetWorldTimeTask($this), 20);
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

    private $plugin;

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function onRun(): void {
        $worldTimes = $this->plugin->getWorldTimes();
        $allWorldsTime = $worldTimes['$allWorlds'] ?? null; // Korrigierte Zeile
        $worldManager = $this->plugin->getServer()->getWorldManager();

        foreach ($worldManager->getWorlds() as $world) {
            $worldName = $world->getFolderName();
            $time = $worldTimes[$worldName] ?? $allWorldsTime;

            if ($time !== null) {
                $world->setTime($time === "day" ? 1000 : 18000);
                $world->stopTime();
            }
        }
    }
}