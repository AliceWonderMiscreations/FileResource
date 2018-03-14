<?php
declare(strict_types=1);

/**
 * Unit testing for the FileResource abstract class. These unit tests actually load the
 * GenericFileResource class so that test properties can be set. That class does not
 * override any methods in the abstract class or define any new methods.
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
 | Purpose: Unit Testing                           |
 +-------------------------------------------------+
*/

use PHPUnit\Framework\TestCase;

/**
 * Unit testing for the FileResource abstract class.
 */
final class GenericFileResourceTest extends TestCase
{
    /**
     * Make sure that getMimeType returns null when property is set to null
     *
     * @return void
     */
    public function testNullMimeReturnsNull(): void
    {
        $arr = array();
        $a = new \AWonderPHP\FileResource\GenericFileResource($arr);
        $b = $a->getMimeType();
        $this->assertNull($b);
    }
    
    /**
     * Make sure that getMimeType returns lower case when property is set to upper case
     *
     * @return void
     */
    public function testUpperCaseMimeReturnsLower(): void
    {
        $arr = array();
        $arr['mime'] = 'TEXT/PLAIN';
        $a = new \AWonderPHP\FileResource\GenericFileResource($arr);
        $expected = 'text/plain';
        $actual = $a->getMimeType();
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * Make sure white space at beginning or end of mime property gets trimmed
     *
     * @return void
     */
    public function testMimeWithWhitespaceIsTrimmed(): void
    {
        $arr = array();
        $arr['mime'] = ' TEXT/PLAIN ';
        $a = new \AWonderPHP\FileResource\GenericFileResource($arr);
        $expected = 'text/plain';
        $actual = $a->getMimeType();
        $this->assertEquals($expected, $actual);
    }
    
    // checksum validation tests
    
    /**
     * Make sure validateFile() passes with a valid hex hash
     *
     * @return void
     */
    public function testValidatesFromHexHash(): void
    {
        $testchecksum = '2328c729efad8ac17f711a4249d7deb909ecaa05ed116ee7a36e8b6ffee63dc0';
        $arr = array();
        $arr['checksum'] = 'sha256:' . $testchecksum;
        $arr['filepath'] = __DIR__ . '/' . $testchecksum . '.dat';
        $a = new \AWonderPHP\FileResource\GenericFileResource($arr);
        $b = $a->validateFile();
        $this->assertTrue($b);
    }
    
    /**
     * Make sure validateFile() fails with an invalid hex hash
     *
     * @return void
     */
    public function testBadHexHashFails(): void
    {
        $testchecksum = '2328c729efad8ac17f711a4249d7deb909ecaa05ed116ee7a36e8b6ffee63dc0';
        $arr = array();
        $arr['checksum'] = 'sha256:' . str_shuffle($testchecksum);
        $arr['filepath'] = __DIR__ . '/' . $testchecksum . '.dat';
        $a = new \AWonderPHP\FileResource\GenericFileResource($arr);
        $b = $a->validateFile();
        $this->assertFalse($b);
    }
    
    /**
     * Make sure validateFile() passes with a valid base64 hash
     *
     * @return void
     */
    public function testValidatesFromBase64Hash(): void
    {
        $testchecksum = '2328c729efad8ac17f711a4249d7deb909ecaa05ed116ee7a36e8b6ffee63dc0';
        $raw = hex2bin($testchecksum);
        $newtest = base64_encode($raw);
        $arr = array();
        $arr['checksum'] = 'sha256:' . $newtest;
        $arr['filepath'] = __DIR__ . '/' . $testchecksum . '.dat';
        $a = new \AWonderPHP\FileResource\GenericFileResource($arr);
        $b = $a->validateFile();
        $this->assertTrue($b);
    }
    
    /**
     * Make sure validateFile() fails with an invalid base64 hash
     *
     * @return void
     */
    public function testBadBase64HashFails(): void
    {
        $testchecksum = '2328c729efad8ac17f711a4249d7deb909ecaa05ed116ee7a36e8b6ffee63dc0';
        $shuffled = str_shuffle($testchecksum);
        $raw = hex2bin($shuffled);
        $newtest = base64_encode($raw);
        $arr = array();
        $arr['checksum'] = 'sha256:' . $newtest;
        $arr['filepath'] = __DIR__ . '/' . $testchecksum . '.dat';
        $a = new \AWonderPHP\FileResource\GenericFileResource($arr);
        $b = $a->validateFile();
        $this->assertFalse($b);
    }
    
    /**
     * Make sure validateFile() return null if the file can not be found
     *
     * @return void
     */
    public function testValidateReturnsNullWhenFileNotePresent(): void
    {
        $testchecksum = '2328c729efad8ac17f711a4249d7deb909ecaa05ed116ee7a36e8b6ffee63dc0';
        $arr = array();
        $arr['checksum'] = 'sha256:' . $testchecksum;
        $arr['filepath'] = __DIR__ . '/' . str_shuffle($testchecksum) . '.dat';
        $a = new \AWonderPHP\FileResource\GenericFileResource($arr);
        $b = $a->validateFile();
        $this->assertNull($b);
    }
    
    /**
     * Make sure validateFile() return null if the specified algorithm is not valid
     *
     * @return void
     */
    public function testValidateReturnsNullWhenAlgoNotValid(): void
    {
        $testchecksum = '2328c729efad8ac17f711a4249d7deb909ecaa05ed116ee7a36e8b6ffee63dc0';
        $arr = array();
        $arr['checksum'] = 'fubar512:' . $testchecksum;
        $arr['filepath'] = __DIR__ . '/' . $testchecksum . '.dat';
        $a = new \AWonderPHP\FileResource\GenericFileResource($arr);
        $b = $a->validateFile();
        $this->assertNull($b);
    }
    
    // integrity attribute tests
    
    /**
     * Make sure validateFile() works with ripemd160 just so we know the PHP install supports it.
     *
     * @return void
     */
    public function testValidatesRipemd160FromHexHash(): void
    {
        $testchecksum = 'c9483f0d7a0f560d09de5663e14799804c6c2db0';
        $arr = array();
        $arr['checksum'] = 'ripemd160:' . $testchecksum;
        $arr['filepath'] = __DIR__ . '/' . $testchecksum . '.dat';
        $a = new \AWonderPHP\FileResource\GenericFileResource($arr);
        $b = $a->validateFile();
        $this->assertTrue($b);
    }
    
    /**
     * Make sure we can generate an integrity attribute from sha256
     *
     * @return null
     */
    public function testValidIntegrityFromSHA256(): void
    {
        $testchecksum = '2328c729efad8ac17f711a4249d7deb909ecaa05ed116ee7a36e8b6ffee63dc0';
        $arr = array();
        $arr['checksum'] = 'sha256:' . $testchecksum;
        $raw = hex2bin($testchecksum);
        $base64 = base64_encode($raw);
        $expected = 'sha256-' . $base64;
        $a = new \AWonderPHP\FileResource\GenericFileResource($arr);
        $actual = $a->getIntegrityAttribute();
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * Make sure we can generate an integrity attribute from sha356
     *
     * @return null
     */
    public function testValidIntegrityFromSHA384(): void
    {
        $testchecksum = '35cc0eed0355362c016dbf133c55441e9c8bd378b30e46f2d8da4084a7b82ec8add38790101764caf8862a2927cfb362';
        $arr = array();
        $arr['checksum'] = 'sha384:' . $testchecksum;
        $raw = hex2bin($testchecksum);
        $base64 = base64_encode($raw);
        $expected = 'sha384-' . $base64;
        $a = new \AWonderPHP\FileResource\GenericFileResource($arr);
        $actual = $a->getIntegrityAttribute();
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * Make sure we can generate an integrity attribute from sha512
     *
     * @return null
     */
    public function testValidIntegrityFromSHA512(): void
    {
        $testchecksum = '53b9baaefebcfa5f02b59aa0405df418565b1992860947e7e2f4df2eeb9de404122be374c2345d889992347cc241aa2b875a07d0367ffc0064d9116ca211868e';
        $arr = array();
        $arr['checksum'] = 'sha512:' . $testchecksum;
        $raw = hex2bin($testchecksum);
        $base64 = base64_encode($raw);
        $expected = 'sha512-' . $base64;
        $a = new \AWonderPHP\FileResource\GenericFileResource($arr);
        $actual = $a->getIntegrityAttribute();
        $this->assertEquals($expected, $actual);
    }
    
    /**
     * Make sure we do NOT generate an integrity attribute from ripemd160
     *
     * @return null
     */
    public function testIntegrityNullRipemd160(): void
    {
        $testchecksum = 'c9483f0d7a0f560d09de5663e14799804c6c2db0';
        $arr = array();
        $arr['checksum'] = 'ripemd160:' . $testchecksum;
        $a = new \AWonderPHP\FileResource\GenericFileResource($arr);
        $b = $a->getIntegrityAttribute();
        $this->assertNull($b);
    }
}

?>