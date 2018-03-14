<?php
declare(strict_types = 1);

/**
 * An abstract class for resources embedded in a web page (images, scripts, whatever)
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
 | Purpose: Abstract class to be extended          |
 +-------------------------------------------------+
*/

namespace AWonderPHP\FileResource;

/**
 * An abstract class for resources embedded in a web page (images, scripts, whatever)
 */
abstract class FileResource
{
    /**
     * The MIME type of the resource
     *
     * @var null|string
     */
    protected $mime = null;

    /**
     * Algorithm : checksum
     *
     * The checksum is either hex or base64 encoded. Example:
     * sha256:708c26ff77c1fa15ac9409a5cbe946fe50ce203a73c9b300960f2adb79e48c04
     * For performance in generating integrity tag I recommend this property be base64 encoded
     *
     * @var null|string
     */
    protected $checksum = null;
    
    /**
     * Hashing algorithms that are currently supported by browsers for use with
     * the integrity attribute
     *
     * @var array
     */
    protected $validIntegrityAlgo = array('sha256', 'sha384', 'sha512');

    /**
     * Filesystem location, only applicable when a local resource
     *
     * @var null|string
     */
    protected $filepath = null;
    
    /**
     * The crossorigin attribute if any
     *
     * @var null|string
     */
    protected $crossorigin = null;
    
    /**
     * Current valid crossorigin arguments
     *
     * @var array
     */
    protected $validCrossOrigin = array('anonymous', 'use-credentials');

    /**
     * Modification date of file - may not necessarily match the modification date of the actual
     * file as seen by the filesystem. I recommend ISO 8601 in 'Y-m-d\TH:i:sO' - aka date('c')
     *
     * @var null|string
     */
    protected $lastmod = null;

    // subset from parse_url

    /**
     * The protocol scheme. Should be null, http, or https
     *
     * @var null|string
     */
    protected $urlscheme = null;

    /**
     * The host name. Internationalized names should be in punycode
     *
     * @var null|string
     */
    protected $urlhost = null;

    /**
     * The url path. Should start with a forward slash /
     *
     * @var null|string
     */
    protected $urlpath = null;

    /**
     * The query string. The part of a URL that comes after the ?
     *
     * @var null|string
     */
    protected $urlquery = null;
    
    /**
     * Tests whether or not a specified $prefix is valid. Classes that extend may wish to
     * throw an exception on failure.
     *
     * @param null|string $prefix The prefix to test
     *
     * @return bool True on valid, False on invalid
     */
    protected function validatePrefix($prefix)
    {
        if (is_null($prefix)) {
            return true;
        }
        if (strlen($prefix) > 0) {
            if ($prefix[0] !== "/") {
                return false;
            }
        }
        $testurl = 'http://example.org' . $prefix . '/path/file.html';
        if (filter_var($testurl, FILTER_VALIDATE_URL) === false) {
            return false;
        }
        return true;
    }
    
    /**
     * Converts a string to a UNIX timestamp returning null on failure
     * or if string is a relative time string (e.g. +1 week)
     *
     * @param null|string $dateString The date string to be converted
     *
     * @return null|int The UNIX seconds from epoch
     */
    protected function stringToTimestamp($dateString)
    {
        $now = time();
        $then = $now - 31557600;
        if (! is_string($dateString)) {
            return null;
        }
        if (! $return = strtotime($dateString, $now)) {
            return null;
        }
        if ($return > $now) {
            return null;
        }
        if ($return < 0) {
            return null;
        }
        if (! $test = strtotime($dateString, $then)) {
            return null;
        }
        if ($return === $test) {
            return $return;
        }
        return null;
    }
    
    /**
     * Return the mime type
     *
     * @return null|string
     */
    public function getMimeType()
    {
        if (is_null($this->mime)) {
            return null;
        }
        return trim(strtolower($this->mime));
    }

    /**
     * Return the checksum
     *
     * @return null|string
     */
    public function getChecksum()
    {
        return $this->checksum;
    }
    
    /**
     * Returns null or the value to use with a crossorigin attribute
     *
     * @return null|string
     */
    public function getCrossOrigin()
    {
        if (! is_string($this->crossorigin)) {
            return null;
        }
        $crossorigin = trim(strtolower($this->crossorigin));
        if (! in_array($crossorigin, $this->validCrossOrigin)) {
            throw new \InvalidArgumentException(
                sprintf(
                    '"%s" is not a valid crossorigin attribute',
                    $crossorigin
                )
            );
        }
        return $crossorigin;
    }
    
    /**
     * Returns the filepath to the resource or null if the property is not defined
     *
     * @return null|string
     */
    public function getFilePath()
    {
        return $this->filepath;
    }
    
    /**
     * Validates the file matches the checksum
     *
     * @return null|boolean Returns null if the file can not be found or any
     *                      other reason that prevents an actual verification
     *                      from being performed. If verification can be
     *                      performed, returns True if verified, False if it
     *                      does not verify.
     */
    public function validateFile()
    {
        if ((is_null($this->filepath)) || (is_null($this->checksum))) {
            return null;
        }
        if (! file_exists($this->filepath)) {
            return null;
        }
        list($algo, $hash) = explode(':', $this->checksum, 2);
        if (! in_array($algo, hash_algos())) {
            return null;
        }
        if (ctype_xdigit($hash)) {
            $raw = hex2bin($hash);
        } else {
            $raw = base64_decode($hash);
        }
        $filehash = hash_file($algo, $this->filepath, true);
        if ($raw === $filehash) {
            return true;
        }
        return false;
    }

    /**
     * Returns the URI to the resource. For http the checksum MUST exist so
     * that an integrity attribute will exist.
     *
     * @param null|string $prefix A path to put at the beginning of the object urlpath property
     *
     * @return null|string
     */
    public function getSrcAttribute($prefix = null)
    {
        if (! $this->validatePrefix($prefix)) {
            return null;
        }
        if (is_null($prefix)) {
            $prefix = '';
        }
        $return = '';
        if ((! is_null($this->urlscheme)) && (! is_null($this->urlhost))) {
            $scheme = strtolower($this->urlscheme);
            if (! in_array($scheme, array('http', 'https'))) {
                trigger_error("Remote resources should only be served with HTTPS or (deprecated) HTTP with an integrity attribute, src attribute not generated.", E_USER_NOTICE);
                return null;
            }
            if ($scheme === 'http') {
                if (is_null($this->checksum)) {
                    trigger_error("Remote resources are not safe over HTTP without a usable integrity attribute, src attribute not generated.", E_USER_NOTICE);
                    return null;
                }
                list($algo, $checksum) = explode(':', $this->checksum);
                if (! in_array($algo, $this->validIntegrityAlgo)) {
                    trigger_error("Remote resources are not safe over HTTP without a usable integrity attribute, src attribute not generated.", E_USER_NOTICE);
                    return null;
                }
                trigger_error("Use of HTTP for remote resources is dangerous and deprecated and may not be supported in future versions.", E_USER_NOTICE);
            }
            $return = $scheme . '://' . $this->urlhost;
        }
        if (! is_null($this->urlpath)) {
            $return = $return . $prefix . $this->urlpath;
        }
        if (! is_null($this->urlquery)) {
            $return = $return . '?' . $this->urlquery;
        }
        if (strlen($return) === 0) {
            return null;
        }
        return $return;
    }
    
    /**
     * Returns null or the value to use with a script node integrity attribute
     *
     * @return null|string
     */
    public function getIntegrityAttribute()
    {
        if (is_null($this->checksum)) {
            return null;
        }
        list($algo, $checksum) = explode(':', $this->checksum);
        if (! in_array($algo, $this->validIntegrityAlgo)) {
            return null;
        }
        if (ctype_xdigit($checksum)) {
            $checksum = hex2bin($checksum);
            $checksum = base64_encode($checksum);
        }
        return $algo . '-' . $checksum;
    }

    /**
     * Returns the UNIX timestamp from the lastmod property
     *
     * @return null|int
     */
    public function getTimestamp()
    {
        return $this->stringToTimestamp($this->lastmod);
    }
// end of abstract class
}

?>