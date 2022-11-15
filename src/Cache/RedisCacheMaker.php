<?php

namespace App\Cache;

use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class RedisCacheMaker
{
    private \Redis $redisConnection;
    private RedisCache $cache;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->redisConnection = RedisAdapter::createConnection($parameterBag->get('redis_url'));
    }


    public function makeCacheRequest(string $namespace = '', int $lifetime = 0): void
    {
        $this->cache = new RedisCache($this->redisConnection, $namespace, $lifetime);
    }

    public function checkIfCacheExists(): bool
    {
        return $this->cache->getItem()->isHit();
    }

    public function saveToCache(mixed $item): void
    {
        $cachedPlace = $this->cache->getItem();
        $cachedPlace->set($item);
        $this->cache->save($cachedPlace);
    }

    public function getFromCache(): mixed
    {
        if ($this->cache->hasItem()) {
            return $this->cache->getItem()->get();
        } else {
            return null;
        }
    }

}