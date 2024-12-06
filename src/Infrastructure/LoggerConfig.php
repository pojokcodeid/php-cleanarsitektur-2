<?php

namespace Infrastructure;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class LoggerConfig
{
  public static function createLogger()
  {
    $log = new Logger('api-logger');
    $log->pushHandler(new StreamHandler(__DIR__ . '/../../logs/app.log', Logger::DEBUG));
    return $log;
  }
}
