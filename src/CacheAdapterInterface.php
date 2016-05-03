<?php

namespace Crecket\DependencyManager;

interface CacheAdapterInterface
{
    /**
     * @param $tag
     * @return mixed
     */
    public function contains($tag);

    /**
     * @param $tag
     * @return mixed
     */
    public function fetch($tag);

    /**
     * @param $tag
     * @param $data
     * @param $expires
     * @return mixed
     */
    public function save($tag, $data, $expires = 0);
}