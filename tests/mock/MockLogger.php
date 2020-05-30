<?php

/**
 * Mock Fluent Logger
 */
class MockLogger implements Fluent\Logger\LoggerInterface
{
    private $key;
    private $log;


    private static $forceException = false;

    /**
     * send a message to specified fluentd.
     *
     * @param string $tag
     * @param array  $data
     * @return bool
     *
     * @api
     */
    public function post($tag, array $data)
    {
        if (self::$forceException) {
            throw new \Exception('forced error');
        }

        $this->key = $tag;
        $this->log = $data;
    }

    /**
     * send a message to specified fluentd.
     *
     * @param Entity $entity
     * @return bool
     */
    public function post2(Fluent\Logger\Entity $entity)
    {
        return true;
    }

    public function getLastKey()
    {
        return $this->key;
    }

    public function getLastLog()
    {
        return $this->log;
    }

    public static function forceException($condition = false)
    {
        $default = self::$forceException;
        self::$forceException = $condition;

        return $default;
    }
}
