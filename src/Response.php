<?php

Namespace Crecket\DependencyManager;

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
     * @param $input_file_data
     * @throws Exception
     */
    public function __construct($options, $input_file_data = false)
    {
        $this->options = $options;

        if ($input_file_data !== false) {

            if (is_array($input_file_data)) {

                // custom file list entered
                $file_list = $input_file_data;

            } else {

                if (!isset($_SESSION['crecket_dependency_manager'][$input_file_data])) {

                    // Check if secret is set and if it matches the private key
                    throw new Exception('Invalid secret', 'The secret key was not found or invalid.', 403);

                } else {

                    // retrieve file list from session
                    $file_list = $_SESSION['crecket_dependency_manager'][$input_file_data]['files'];

                    // minify option
                    $this->minify = $_SESSION['crecket_dependency_manager'][$input_file_data]['minify'];

                    // set secret key
                    $this->secret = $input_file_data;
                }

            }

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
                $file_list = $_SESSION['crecket_dependency_manager'][$this->secret]['files'];

                // minify option
                $this->minify = $_SESSION['crecket_dependency_manager'][$this->secret]['minify'];

            }
        }

        // Check if minify code is enabled, overrules minify options in the session
        if (!empty($this->options['minify'])) {
            $this->minify = true;
        }

        // check if custom interface is given
        if (empty($this->options['CacheObject'])) {

            // Required option if no custom interface is given
            if (empty($this->options['CacheLocation'])) {
                throw new Exception('Caching error', 'Missing caching target location for default cache interface');
            }

            // Create cache element
            $this->cache = new \Doctrine\Common\Cache\FilesystemCache($this->options['CacheLocation']);

            // Interface for default cache setup
            if (!empty($this->options['CacheNameSpace'])) {
                $this->cache->setNamespace('crecket_dependency_loader');
            }

        } else {

            // check if a custom cache object is set
            if (!$this->options['CacheObject'] instanceof CacheAdapterInterface) {
                throw new Exception('Caching error', 'Custom cache interface does not implement the Crecket\\DependencyManager\\CacheAdapterInterface');
            }

            // set the cache object to this namespace
            $this->cache = $this->options['CacheObject'];

        }

        // Parse file list
        $this->file_data = $this->fileList($file_list);
    }

    /**
     * @param bool $send_headers
     * @return array|false|mixed
     */
    public function getResult($send_headers = true)
    {
        // Get the file contents
        $contents = $this->getCollection();

        // Check the caching headers
        $this->setHeaders();

        if ($send_headers) {
            // Send headers
            Utilities::sendHeaders();
        }

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
                $contents = csscrush_string($contents, array(
                    'minify' => $this->minify
                ));
            }

            // if minfiy is true and type is js, run through JS minifier
            if ($this->minify && isset($this->response_type['js'])) {
                // minify js
                $contents = Minifier::minify($contents, array('flaggedComments' => false));
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

            // Retrieve file info for file
            $fileinfo = $this->fileInfo($file);

            if ($fileinfo !== false) {

                // Verify the response type
                if ($this->response_type !== false && !isset($this->response_type[$fileinfo['file_type']])) {
                    Utilities::statusCode(500, 'Internal Server Error');
                    throw new Exception('File not supported', 'Error 500: The following file isn\'t the correct type for this request: ' . htmlspecialchars($fileinfo));
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
                throw new Exception('Not Found', '404 File not found: ' . htmlspecialchars($file));

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
        $path = (($file[0] === "/") ? Constant('ROOT') . $file : Constant('ROOT') . "/" . $file);
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
            Utilities::statusCode(200, 'OK');
        }
        return $this;
    }

}
