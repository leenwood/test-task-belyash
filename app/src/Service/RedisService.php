<?php

namespace App\Service;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class RedisService
{
    public function __construct(
        private ContainerBagInterface $containerBag
    )
    {
    }


    /**
     * @return RedisService
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getRedisAdapter(): RedisAdapter
    {
        $cache = new RedisAdapter(
            RedisAdapter::createConnection($this->containerBag->get('redis_url'))
        );

        return $cache;
    }

}