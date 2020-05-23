<?php

namespace lbreak\Wechat\Mid;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\CacheProvider;

trait CacheTrait
{
    /**
     * @var $cache CacheProvider.
     */
    protected $cache;

    /**
     * 设置缓存驱动.
     */
    public function setCache(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * 获取缓存驱动.
     */
    public function getCache()
    {
        return $this->cache;
    }
}
