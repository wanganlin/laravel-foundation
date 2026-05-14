<?php

declare(strict_types=1);

namespace Juling\Foundation\Plugins;

interface PluginsContract
{
    public function getInfo(): array;

    public function install(): bool;

    public function uninstall(): bool;

    public function upgrade(): bool;

    public function enable(): bool;

    public function disable(): bool;

    public function getConfig(): array;

    public function setConfig(array $config): bool;
}
