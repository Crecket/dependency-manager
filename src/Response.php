<?php

Namespace Crecket\DependencyManager;

use Doctrine\Common\Cache\FilesystemCache;
use MatthiasMullie\Minify\CSS;
use JShrink\Minifier;


final class Response
{

    private $file_data;
    private $minify = false;
    private $modified = false;
    private $cache;
    private $response_type = false;
    private $last_modified = false;
    public $options;

    /**
     * Response constructor.
     * @param $options
     * @throws \Exception
     */
    public function __construct($options)
    {
        $this->options = $options;

        // Required option
        if (empty($this->options['Cache'])) {
            die('Missing caching target location');
        }

        // Create cache element
        $this->cache = new FilesystemCache(Constant('ROOT') . $this->options['Cache']);
        $this->cache->setNamespace('crecket_dependency_loader');

        // Check if files variable is set
        if (!isset($_GET['files'])) {
            return false;
        }

        // Parse file list
        $this->file_data = $this->fileList($_GET['files']);

        // Check if file_data is correct
        if ($this->file_data === false) {
            Utilities::sendHeaders();
            exit;
        }

        // Check if minify code is enabled
        if (isset($_GET['minify'])) {
            $this->minify = true;
        }

        // Check if secret is set and if it matches the private key
        if (isset($this->options['Secret']) && $options['Secret'] !== false) {
            if (!isset($_GET['secret']) || $_GET['secret'] !== hash('sha256', $_GET['files'] . $this->options['Secret'])) {
                die('Invalid request');
            }
        }

        // Get the file contents
        $contents = $this->getCollection();

        // Send headers
        $this->setHeaders();
        Utilities::sendHeaders();

        echo $contents;
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
            if (!$this->minify) {
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


            $this->cache->save($this->file_data['hash'], $contents, 60 * 60 * 24 * 30);
            return $contents;
        }
    }

    /**
     * @param $string
     * @return array
     * @throws \Exception
     */
    private function fileList($string)
    {
        // file list to array
        $list = explode(",", $string);
        $hash = "";
        $file_list = array();

        foreach ($list as $file) {

            // Retrieve file info for file
            $fileinfo = $this->fileInfo($file);
            if ($fileinfo !== false) {

                // Verify the response type
                if ($this->response_type !== false && !isset($this->response_type[$fileinfo['file_type']])) {
                    Utilities::statusCode(500, 'Internal Server Error');
                    echo 'The following file isn\'t the correct type for this request: ' . $fileinfo['path'];
                }

                // Check folder whitelist
                if (isset($this->options['DirWhitelist']) && count($this->options['DirWhitelist']) > 0) {
                    $found = false;
                    foreach ($this->options['DirWhitelist'] as $folder) {
                        $tempFolder = (($folder[0] === "/") ? Constant('ROOT') . $folder : Constant('ROOT') . "/" . $folder);
                        if ($tempFolder === dirname($fileinfo['path'])) {
                            $found = true;; // File is located in whitelist
                            break;
                        }
                    }
                    if (!$found) {
                        // File isn't located in whitelisted folder, return 403
                        Utilities::statusCode(403, 'Access Denied');
                        echo 'The following file is not inside a whitelisted folder: ' . $fileinfo['path'];
                        return false;
                    }
                }

                // Create new response object
                $newResponse = $this->newResponse($fileinfo);
                if ($newResponse === false) {
                    // File type isn't supported, return 500 header
                    Utilities::statusCode(500, 'Internal Server Error');
                    echo 'The following file is not supported: ' . $fileinfo['path'];
                    return false;
                }

                // Add response to array
                $file_list[] = $newResponse;
                $hash .= $fileinfo['path'] . $fileinfo['last_edit'];
            } else {
                // File wasn't found, return 404 header
                Utilities::statusCode(404, 'Not Found');
                echo '404 File not found: ';
                echo htmlspecialchars($file);
                return false;
            }
        }

        if ($this->minify) {
            $hash .= "minify"; // Make sure the server sees a difference between minified and not-minified version when caching
        }

        return array('hash' => hash('sha256', $hash), 'list' => $file_list);
    }


    /**
     * @param $path
     * @return array|bool
     */
    private function fileInfo($path)
    {
        $path = (($path[0] === "/") ? Constant('ROOT') . $path : Constant('ROOT') . "/" . $path);
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
                return new Types\Css($file_info, $this->cache);
                break;
            case 'scss':
                $this->response_type = array(
                    'css' => true,
                    'less' => true,
                    'scss' => true
                );
                return new Types\Scss($file_info, $this->cache);
                break;
            case 'less':
                $this->response_type = array(
                    'css' => true,
                    'less' => true,
                    'scss' => true
                );
                return new Types\Less($file_info, $this->cache);
                break;
            case 'js':
                $this->response_type = array(
                    'js' => true
                );
                return new Types\Js($file_info, $this->cache);
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
            Utilities::setHeader('Last-Modified', date('D, d M Y H:i:s', strtotime($lastModifiedDate)) . ' GMT');
            Utilities::setHeader('Expires', date('D, d M Y H:i:s', time() + 60 * 60 * 24 * 90) . ' GMT');
        }
        return $this;
    }

}