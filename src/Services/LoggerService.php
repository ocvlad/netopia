<?php

namespace App\Services;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class LoggerService
{
    private $logger;

    public function __construct(string $logName = 'app', string $logFile = '/var/www/html/logs/app.log')
    {
        $this->logger = new Logger($logName);
        $this->logger->pushHandler(new StreamHandler($logFile, Logger::DEBUG));
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function info(string $message, array $context = [])
    {
        $this->logger->info($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function warning(string $message, array $context = [])
    {
        $this->logger->warning($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function error(string $message, array $context = [])
    {
        $this->logger->error($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function debug(string $message, array $context = [])
    {
        $this->logger->debug($message, $context);
    }
}
