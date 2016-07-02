<?php

namespace Infrastructure\Log;

use Domain\Log\LoggerInterface;
use Domain\Log\LogLevel;

class Logger implements LoggerInterface
{
    public function emergency($message, array $context = array())
    {
        $arguments = array_unshift($context, $message);
        \Application_Monitor_Log::proto(LogLevel::EMERGENCY, $arguments);
    }

    public function alert($message, array $context = array())
    {
        $arguments = array_unshift($context, $message);
        \Application_Monitor_Log::proto(LogLevel::ALERT, $arguments);
    }

    public function critical($message, array $context = array())
    {
        $arguments = array_unshift($context, $message);
        \Application_Monitor_Log::proto(LogLevel::CRITICAL, $arguments);
    }

    public function error($message, array $context = array())
    {
        $arguments = array_unshift($context, $message);
        \Application_Monitor_Log::proto(LogLevel::ERROR, $arguments);
    }

    public function warning($message, array $context = array())
    {
        $arguments = array_unshift($context, $message);
        \Application_Monitor_Log::proto(LogLevel::WARNING, $arguments);
    }

    public function notice($message, array $context = array())
    {
        $arguments = array_unshift($context, $message);
        \Application_Monitor_Log::proto(LogLevel::NOTICE, $arguments);
    }

    public function info($message, array $context = array())
    {
        $arguments = array_unshift($context, $message);
        \Application_Monitor_Log::proto(LogLevel::INFO, $arguments);
    }

    public function debug($message, array $context = array())
    {
        $arguments = array_unshift($context, $message);
        \Application_Monitor_Log::proto(LogLevel::DEBUG, $arguments);
    }

    public function log($level, $message, array $context = array())
    {
        $arguments = array_unshift($context, $message);
        \Application_Monitor_Log::proto($level, $arguments);
    }
}
