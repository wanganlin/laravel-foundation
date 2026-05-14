<?php

declare(strict_types=1);

namespace Juling\Foundation\Logging;

use Monolog\Logger;

class SLSLogger
{
    public function __invoke(array $config): Logger
    {
        $handler = new SLSHandler($config);

        $logger = new Logger('sls');
        $logger->pushHandler($handler);

        return $logger;
    }
}
