<?php

class testCacheInterface implements Crecket\DependencyManager\CacheAdapterInterface
{

    public $cache;

    public function __construct($location)
    {
        $this->cache = new \Doctrine\Common\Cache\FilesystemCache(ROOT . $location);
    }

    /**
     * @param $tag
     * @return mixed
     */
    public function contains($tag)
    {
        return $this->cache->contains($tag);
    }

    /**
     * @param $tag
     * @return mixed
     */
    public function fetch($tag)
    {
        return $this->cache->fetch($tag);
    }

    /**
     * @param $tag
     * @param $data
     * @param $expires
     * @return mixed
     */
    public function save($tag, $data, $expires = 0)
    {
        return $this->cache->save($tag, $data, $expires);
    }
}
