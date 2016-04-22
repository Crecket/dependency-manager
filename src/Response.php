<?php

Namespace Crecket\DependencyManager;

use Doctrine\Common\Cache\FilesystemCache;
use MatthiasMullie\Minify\CSS;
use JShrink\Minifier;


final class Response
{

    /**
     * @var mixed
     * File list
     */
    private $file_data;

    /**
     * @var bool
     * Minify enabled or not
     */
    private $minify = false;

    /**
     * @var bool
     * This file request is stored/has been modified
     */
    private $modified = false;

    /**
     * @var bool|object
     * Caching object
     */
    private $cache;

    /**
     * @var bool
     * Location to store new remote files
     */
    private $remote_storage = false;

    /**
     * @var bool|array
     * Contains the current response type, used to prevent js/css mixing in one file
     */
    private $response_type = false;

    /**
     * @var bool|int
     * Oldest timestamp for all listed files
     */
    private $last_modified = false;

    /**
     * @var bool|mixed
     * Secret hash value for the file list
     */
    private $secret = false;

    /**
     * @var array
     * Input options
     */
    private $options;

    /**
     * Response constructor.
     * @param $options
     * @param $input_file_list
     * @throws Exception
     */
    public function __construct($options, $input_file_list = false)
    {
        $this->options = $options;

        if ($input_file_list !== false) {

            // custom file list entered
            $file_list = $input_file_list;

        } else {

            // Check if files hash is set in parameter
            if (!isset($_GET['secret'])) {
                throw new Exception('No secret key', 'Secret key is not set', 403);
            }

            $this->secret = $_GET['secret'];

            if (!isset($_SESSION['crecket_dependency_manager'][$this->secret])) {

                // Check if secret is set and if it matches the private key
                throw new Exception('Invalid secret', 'The secret key was not found or invalid.', 403);

            } else {

                // retrieve file list from session
                $file_list = $_SESSION['crecket_dependency_manager'][$this->secret];

            }
        }

        // Check if minify code is enabled
        if (isset($_GET['minify'])) {
            $this->minify = true;
        }

        // Required option
        if (empty($this->options['Cache'])) {
            throw new Exception('Caching error', 'Missing caching target location');
        }

        // Create cache element
        $this->cache = new FilesystemCache($this->options['Cache']);

        // Required option
        if (empty($this->options['Remote_storage'])) {
            throw new Exception('Remote file error', 'Missing remote file storage location');
        }else if(!file_exists($this->options['Remote_storage'])){
            throw new Exception('Remote file error', 'Storage location not found');
        }

        // set remote file storage location
        $this->remote_storage = $this->options['Remote_storage'];

        // Required option
        if (!empty($this->options['CacheNameSpace'])) {
            $this->cache->setNamespace('crecket_dependency_loader');
        }

        // Parse file list
        $this->file_data = $this->fileList($file_list);

        // Check if file_data is correct
        if ($this->file_data === false) {
            Utilities::sendHeaders();
            exit;
        }

    }

    /**
     * @return array|false|mixed
     */
    public function getResult()
    {
        // Get the file contents
        $contents = $this->getCollection();

        // Send headers
        $this->setHeaders();
        Utilities::sendHeaders();

        return $contents;
    }

    /**
     * @return array|false|mixed
     * @throws \Exception
     */
    public function getCollection()
    {

        if ($this->cache->contains($this->file_data['hash'])) {
            return $this->cache->fetch($this->file_data['hash']);
        } else {
            // File changed or not cached
            $contents = "";
            foreach ($this->file_data['list'] as $file) {
                $contents .= $file->getFile() . "\n"; // New line to avoid comments cutting off code when combining files
            }

            // if result is css, run the file through a auto prefixer
            if (isset($this->response_type['css'])) {
                $contents = csscrush_string($contents, array());
            }

            // check if minify is enabled
            if ($this->minify) {
                // check content type
                if (isset($this->response_type['css'])) {
                    // minify cs
                    $minifier = new CSS($contents);
                    $contents = $minifier->minify();
                } else if (isset($this->response_type['js'])) {
                    // minify js
                    $contents = Minifier::minify($contents, array('flaggedComments' => false));
                }
            }

            // store in cache
            $this->cache->save($this->file_data['hash'], $contents, 60 * 60 * 24 * 30);

            return $contents;
        }
    }

    /**
     * @param $list
     * @return array
     * @throws \Exception
     */
    private function fileList($list)
    {

        // default values
        $hash = "";
        $file_list = array();

        // loop through file list
        foreach ($list as $file) {

            // check if file is local or remote
            if ($file['local'] === false) {

                // new remote file handler
                $RemoteFile = new RemoteHandler($file['location'], $this->remote_storage, $this->cache);

                // get file contents
                $RemoteFile->getContents();

                // store in local file
                $file['location'] = $RemoteFile->storeContents();

                // Retrieve file info for file
                $fileinfo = $this->fileInfo($file);
            }else{

                // Retrieve file info for file
                $fileinfo = $this->fileInfo($file);

            }

            dump($fileinfo);

            if ($fileinfo !== false) {

                // Verify the response type
                if ($this->response_type !== false && !isset($this->response_type[$fileinfo['file_type']])) {
                    Utilities::statusCode(500, 'Internal Server Error');
                    throw new Exception('File not supported', 'Error 500: The following file isn\'t the correct type for this request: ' . htmlspecialchars($fileinfo['path']));
                }

                // Create new response object
                $newResponse = $this->newResponse($fileinfo);
                if ($newResponse === false) {
                    // File type isn't supported, return 500 header
                    Utilities::statusCode(500, 'Internal Server Error');
                    throw new Exception('File not supported', 'Error 500: The following file is not supported: ' . htmlspecialchars($fileinfo['path']));
                }

                // Add response to array
                $file_list[] = $newResponse;
                $hash .= $fileinfo['path'] . $fileinfo['last_edit'];

            } else {

                // File wasn't found, return 404 header
                Utilities::statusCode(404, 'Not Found');
                throw new Exception('Not Found', '404 File not found: ' . htmlspecialchars($file['location']));

            }

        }

        if ($this->minify) {
            $hash .= "minify"; // Make sure the server sees a difference between minified and not-minified version when caching
        }

        return array('hash' => hash('sha256', $hash), 'list' => $file_list);
    }


    /**
     * @param $file
     * @return array|bool
     */
    private function fileInfo($file)
    {
        $path = (($file['location'][0] === "/") ? Constant('ROOT') . $file['location'] : Constant('ROOT') . "/" . $file['location']);
        if (file_exists($path)) {
            $data = array(
                'path' => $path,
                'last_edit' => filemtime($path),
                'file_type' => pathinfo($path, PATHINFO_EXTENSION)
            );
            if ($this->last_modified === false || $data['last_edit'] < $this->last_modified) {
                $this->last_modified = $data['last_edit'];
            }
            return $data;
        }
        return false;
    }

    /**
     * @param $file_info
     * @return bool|Types\Css|Types\Js|Types\Less|Types\Scss
     */
    private function newResponse($file_info)
    {
        switch ($file_info['file_type']) {
            case 'css':
                $this->response_type = array(
                    'css' => true,
                    'less' => true,
                    'scss' => true
                );
                return new Types\Css($file_info);
                break;
            case 'scss':
                $this->response_type = array(
                    'css' => true,
                    'less' => true,
                    'scss' => true
                );
                return new Types\Scss($file_info);
                break;
            case 'less':
                $this->response_type = array(
                    'css' => true,
                    'less' => true,
                    'scss' => true
                );
                return new Types\Less($file_info);
                break;
            case 'js':
                $this->response_type = array(
                    'js' => true
                );
                return new Types\Js($file_info);
                break;
        }
        return false;
    }

    /**
     * @return $this
     */
    public function setHeaders()
    {
        $lastModifiedDate = $this->last_modified;
        $eTag = hash('sha256', $lastModifiedDate . $this->file_data['hash']);
        $checkModifiedSince = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false;
        $checkETag = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? $_SERVER['HTTP_IF_NONE_MATCH'] : false;

        if (
            ($checkModifiedSince &&
                strtotime($checkModifiedSince) == $lastModifiedDate) ||
            $checkETag == $eTag
        ) {
            Utilities::statusCode(304, 'Not Modified');
            $this->modified = true;
        } else {
            $maxage = 60 * 60 * 24 * 320; // Avoid the hard limit some servers have
            Utilities::setHeader('Cache-Control', 'max-age=' . $maxage . ', must-revalidate');
            Utilities::setHeader('Pragma', 'cache');
            Utilities::setHeader('Last-Modified', date('D, d M Y H:i:s', $lastModifiedDate) . ' GMT');
            Utilities::setHeader('Expires', date('D, d M Y H:i:s', time() + 60 * 60 * 24 * 90) . ' GMT');
        }
        return $this;
    }

}
