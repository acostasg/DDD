<?php

namespace Domain\Exception;

use Domain\Log\LoggerInterface;

class Exception extends \Exception
{
    /**
     * @param LoggerInterface $logger
     * @param string $logLevel
     * @param array $context
     * @param \Exception|null $previous
     */
    public function __construct(LoggerInterface $logger, $logLevel, array $context, \Exception $previous = null)
    {
        $context = array_merge($context, array(
            'exception' => get_class($this)
        ));

        $message = get_class($this) . '|' . implode('|', $context);
        $logger->log($logLevel, $message, $context);

        parent::__construct($message, 0, $previous);
    }
}