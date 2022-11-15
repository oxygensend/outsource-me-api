<?php

namespace App\Cache;

use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\Cache\Marshaller\MarshallerInterface;
use Symfony\Component\Cache\Traits\RedisClusterProxy;
use Symfony\Component\Cache\Traits\RedisProxy;

/** Decorated RedisAdapter */
class RedisCache extends RedisAdapter
{
    private string $key;

    public function __construct(\Predis\ClientInterface|\RedisCluster|RedisClusterProxy|RedisProxy|\Redis|\RedisArray $redis, string $namespace = '', int $defaultLifetime = 0, MarshallerInterface $marshaller = null)
    {
        $this->key = $namespace;

        parent::__construct($redis, $namespace, $defaultLifetime, $marshaller);
    }

    public function getItem(mixed $key = null): CacheItem
    {
        return $key ? parent::getItem($key) : parent::getItem($this->key);
    }

    public function hasItem(mixed $key = null): bool
    {
        return $key ? parent::hasItem($key) : parent::hasItem($this->key);
    }


}