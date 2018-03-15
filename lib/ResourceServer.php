<?php
declare(strict_types = 1);

/**
 * A class for serving files based in PHP from the FileResource object.
 *
 * @package AWonderPHP/FileResource
 * @author  Alice Wonder <paypal@domblogger.net>
 * @license https://opensource.org/licenses/MIT MIT
 * @link    https://github.com/AliceWonderMiscreations/FileResource
 */
/*
 +-------------------------------------------------+
 |                                                 |
 | Copyright (C) 2018 Alice Wonder Miscreations    |
 |  May be used under the terms of the MIT license |
 |                                                 |
 +-------------------------------------------------+
 | Purpose: Wrapper for serving files via PHP      |
 +-------------------------------------------------+
*/

namespace AWonderPHP\FileResource;

/**
 * A class for serving files based in PHP from the FileResource object.
 */

abstract class ResourceServer
{
    /**
     * How long the client should cache the file for. Set by constructor.
     *
     * @var int
     */
    protected $maxage = 0;

    /**
     * Serves a file specified in a FileResource object.
     *
     * @param FileResource $fileResource The FileResource object for what we want to serve.
     *
     * @psalm-suppress RedundantConditionGivenDocblockType
     *
     * @return bool True on success, False on failure
     */
    public function serveFileResource($fileResource): bool
    {
        if (! $fileResource instanceof FileResource) {
            return false;
        }
        $filepath = $fileResource->getFilepath();
        if (is_null($filepath)) {
            return false;
        }
        if (! file_exists($filepath)) {
            return false;
        }
        $mime = $fileResource->getMimeType();
        $ts = $fileResource->getTimestamp();
        $origin = null;
        $crossorigin = $fileResource->getCrossOrigin();
        if (! is_null($crossorigin)) {
            $origin = '*';
        }
      
        $wrapper = new \AWonderPHP\FileResource\FileWrapper($filepath, $mime, $ts, $origin, $this->maxage);
        return $wrapper->sendfile();
    }//end serveFileResource()
}//end class

?>