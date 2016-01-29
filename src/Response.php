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

    /**
     * Response constructor.
     * @param $options
     * @throws \Exception
     */
    public function __construct($options)
    {

        if (empty($options['Cache'])) {
            throw new \Exception('Missing caching target location');
        }

        $this->cache = new FilesystemCache($_SERVER['DOCUMENT_ROOT'] . $options['Cache']);
        $this->cache->setNamespace('dependency_loader');

        if (!isset($_GET['files'])) {
            return false;
        }

        $this->file_data = $this->fileList($_GET['files']);

        if (isset($_GET['minify'])) {
            $this->minify = true;
        }

        if (isset($options['Secret']) && $options['Secret'] !== false) {
            // Check if secret is set and if it matches the private key
            if (!isset($_GET['secret']) || $_GET['secret'] !== md5($_GET['files'] . $options['Secret'])) {
                throw new \Exception('Invalid request');
            }
        }

        $contents = $this->getCollection();

        $this->setHeaders($contents['last_modified']);

        echo $contents['contents'];
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
                $contents .= $file->getFile();
            }

            if (isset($_GET['minify'])) {
                if (isset($this->response_type['css'])) {
                    $minifier = new CSS($contents);
                    $contents = $minifier->minify();
                } else if (isset($this->response_type['js'])) {
                    $contents = Minifier::minify($contents, array('flaggedComments' => false));
                }

            }

            $data = array(
                'contents' => $contents,
                'last_modified' => microtime(true),
            );

            $this->cache->save($this->file_data['hash'], $data, 3600);
            return $data;
        }
    }

    /**
     * @param $string
     * @return array
     * @throws \Exception
     */
    private function fileList($string)
    {
        $list = explode(",", $string);
        $hash = "";
        $file_list = array();

        foreach ($list as $file) {

            $fileinfo = $this->fileInfo($file);

            if ($fileinfo !== false) {

                if ($this->response_type !== false && !isset($this->response_type[$fileinfo['file_type']])) {
                    Utilities::statusCode(500, 'Internal Server Error');
                    throw new \Exception('The following file isn\'t the correct type for this request: ' . $fileinfo['path']);
                }

                $newResponse = $this->newResponse($fileinfo);

                if ($newResponse === false) {
                    Utilities::statusCode(500, 'Internal Server Error');
                    throw new \Exception('The following file is not supported: ' . $fileinfo['path']);
                }

                $file_list[] = $newResponse;
                $hash .= $fileinfo['path'] . $fileinfo['last_edit'];
            } else {
                Utilities::statusCode(404, 'Not Found');
                throw new \Exception('404 File not found');
            }

        }

        if ($this->minify === true) {
            $hash .= "minify"; // Make sure the server sees a difference between minified and not-minified version
        }

        return array('hash' => md5($hash), 'list' => $file_list);
    }


    /**
     * @param $path
     * @return array|bool
     */
    private function fileInfo($path)
    {
        $path = ($path[0] === "/") ? $_SERVER['DOCUMENT_ROOT'] . $path : $path;

        if (file_exists($path)) {
            return array(
                'path' => $path,
                'last_edit' => filemtime($path),
                'file_type' => pathinfo($path, PATHINFO_EXTENSION)
            );
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
     * @param $last_modified
     * @return $this
     */
    public function setHeaders($last_modified)
    {
        $eTag = md5($last_modified . $this->file_data['hash']);
        $checkModifiedSince = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false;
        $checkETag = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? $_SERVER['HTTP_IF_NONE_MATCH'] : false;

        if (
            ($checkModifiedSince && strtotime($checkModifiedSince) == $last_modified) ||
            $checkETag == $eTag
        ) {
            Utilities::statusCode(304, 'Not Modified');
            $this->modified = false;
        } else {
            Utilities::setHeader('Cache-Control', 'max-age=600, must-revalidate');
            Utilities::setHeader('Last-Modified', gmdate('D, d M Y H:i:s', $last_modified) . ' GMT');
            Utilities::setHeader('ETag', $eTag);
            $this->modified = true;
        }

        return $this;
    }

}