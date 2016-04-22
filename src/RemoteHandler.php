<?php

namespace Crecket\DependencyManager;

class RemoteHandler
{

    private $cache;

    private $hash;

    private $storage_folder;

    private $file_name;

    private $file_contents;

    private $location;

    public function __construct($location, $storage_folder, $cache)
    {
        // set location
        $this->location = $location;

        // set file name
        $this->file_name = basename($this->location);

        // set storage location for new files
        $this->storage_folder = $storage_folder;

        // generate hash value for location
        $this->hash = hash('sha256', $this->location);

        // get cache object
        $this->cache = $cache;
    }

    /**
     * Get file contents from remote location
     */
    public function getContents()
    {
        // check cache first
        if($this->cache->contains($this->hash)){
            return $this->cache->fetch($this->hash);
        }

        $this->file_contents = file_get_contents($this->location);
    }

    /**
     * @return bool
     * Store file contents in local file
     */
    public function storeContents()
    {
        if (file_put_contents($this->storage_folder . $this->file_name, $this->file_contents) !== false) {
            // return storage path
            return $this->storage_folder . $this->file_name;
        }
        return false;
    }

}