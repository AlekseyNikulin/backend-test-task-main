<?php

declare(strict_types = 1);

namespace Raketa\BackendTestTask\Infrastructure;

use Redis;

class ConnectorFacade
{
    public string $host;
    public int $port = 6379;
    public ?string $password = null;
    public ?int $dbIndex = null;
    public Connector $connector;

    private static ?Redis $redis = null;

    public function __construct(string $host, int $port, ?string $password, ?int $dbIndex)
    {
        $this->host = $host;
        $this->port = $port;
        $this->password = $password;
        $this->dbIndex = $dbIndex;
    }

    protected function build(): void
    {
        self::$redis = self::$redis ?: new Redis();

        $isConnected = self::$redis->isConnected();

        if (!$isConnected) {
            $isConnected = self::$redis->connect(
                $this->host,
                $this->port,
            );
        }

        if ($isConnected) {
            self::$redis->auth($this->password);
            self::$redis->select($this->dbIndex);
            $this->connector = new Connector(self::$redis);
        }
    }
}
