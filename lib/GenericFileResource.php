<?php
declare(strict_types = 1);

/**
 * A generic class that extends the FileResource abstract class. This class is primarily
 * intended to facilitate unit testing but it can also be used if the the resulting object
 * meets the needs of your web application.
 *
 * @package AWonderPHP/FileResource
 * @author  Alice Wonder <paypal@domblogger.net>
 * @license https://opensource.org/licenses/MIT MIT
 * @link    https://github.com/AliceWonderMiscreations/FileResource
 */
/*
 +--------------------------------------------------------------------+
 |                                                                    |
 | Copyright (C) 2018 Alice Wonder Miscreations                       |
 |  May be used under the terms of the MIT license                    |
 |                                                                    |
 +--------------------------------------------------------------------+
 | Purpose: Generic implementation of the FileResource abstract class |
 +--------------------------------------------------------------------+
*/

namespace AWonderPHP\FileResource;

/**
 * A generic class that extends the FileResource abstract class.
 */
final class GenericFileResource extends FileResource
{
    /**
     * Populated the class properties from an array of parameters
     *
     * @param array $params A key=>value array of properties to assign to the standard
     *                      FileResource class properties
     */
    public function __construct(array $params)
    {
        if (isset($params['mime'])) {
            $this->mime = $params['mime'];
        }
        if (isset($params['checksum'])) {
            $this->checksum = $params['checksum'];
        }
        if (isset($params['filepath'])) {
            $this->filepath = $params['filepath'];
        }
        if (isset($params['crossorigin'])) {
            $this->crossorigin = $params['crossorigin'];
        }
        if (isset($params['lastmod'])) {
            $this->lastmod = $params['lastmod'];
        }
        if (isset($params['urlscheme'])) {
            $this->urlscheme = $params['urlscheme'];
        }
        if (isset($params['urlhost'])) {
            $this->urlhost = $params['urlhost'];
        }
        if (isset($params['urlpath'])) {
            $this->urlpath = $params['urlpath'];
        }
        if (isset($params['urlquery'])) {
            $this->urlquery = $params['urlquery'];
        }
    }
//end of class
}

?>