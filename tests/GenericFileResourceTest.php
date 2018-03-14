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

    // crossorigin validation tests

    /**
     * Make sure that getCrossOrigin returns null when property is set to null
     *
     * @return void
     */
    public function testNullCrossOriginReturnsNull(): void
    {
        $arr = array();
        $a = new \AWonderPHP\FileResource\GenericFileResource($arr);
        $b = $a->getCrossOrigin();
        $this->assertNull($b);
    }

    /**
     * Make sure that getCrossOrigin returns anonymous when property is set to anonymous
     *
     * @return void
     */
    public function testCrossOriginReturnsAnonymous(): void
    {
        $arr = array();
        $arr['crossorigin'] = 'anonymous';
        $a = new \AWonderPHP\FileResource\GenericFileResource($arr);
        $expected = 'anonymous';
        $actual = $a->getCrossOrigin();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Make sure that getCrossOrigin returns use-credentials when property is set to use-credentials
     *
     * @return void
     */
    public function testCrossOriginReturnsUseCredentials(): void
    {
        $arr = array();
        $arr['crossorigin'] = 'use-credentials';
        $a = new \AWonderPHP\FileResource\GenericFileResource($arr);
        $expected = 'use-credentials';
        $actual = $a->getCrossOrigin();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Make sure that getCrossOrigin is not case sensitive
     *
     * @return void
     */
    public function testCrossOriginCaseSensitive(): void
    {
        $arr = array();
        $arr['crossorigin'] = 'Use-Credentials';
        $a = new \AWonderPHP\FileResource\GenericFileResource($arr);
        $expected = 'use-credentials';
        $actual = $a->getCrossOrigin();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Make sure that getCrossOrigin trims white-space
     *
     * @return void
     */
    public function testCrossOriginTrimsWhitespace(): void
    {
        $arr = array();
        $arr['crossorigin'] = '    Use-Credentials  ';
        $a = new \AWonderPHP\FileResource\GenericFileResource($arr);
        $expected = 'use-credentials';
        $actual = $a->getCrossOrigin();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Make sure that getCrossOrigin throws invalid argument when argument not valid
     *
     * @return void
     */
    public function testCrossInvalid(): void
    {
        $arr = array();
        $arr['crossorigin'] = 'invalid';
        $a = new \AWonderPHP\FileResource\GenericFileResource($arr);
        $expected = 'use-credentials';
        $this->expectException(\InvalidArgumentException::class);
        $actual = $a->getCrossOrigin();
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

    // src attribute tests

    /**
     * Make sure we can build src attribute from all components
     *
     * @return void
     */
    public function testBuilsSrcFromAllComponents(): void
    {
        $expected = 'https://www.example.org/path/to/file.php?foo=bar&bar=foo';
        $parsed = parse_url($expected);
        $arr = array();
        $arr['urlscheme'] = $parsed['scheme'];
        $arr['urlhost']   = $parsed['host'];
        $arr['urlpath']   = $parsed['path'];
        $arr['urlquery']  = $parsed['query'];
        $a = new \AWonderPHP\FileResource\GenericFileResource($arr);
        $actual = $a->getSrcAttribute();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Make sure we only build local url when scheme missing
     *
     * @return void
     */
    public function testBuilsSrcNoScheme(): void
    {
        $expected = 'https://www.example.org/path/to/file.php?foo=bar&bar=foo';
        $parsed = parse_url($expected);
        $arr = array();
        //$arr['urlscheme'] = $parsed['scheme'];
        $arr['urlhost']   = $parsed['host'];
        $arr['urlpath']   = $parsed['path'];
        $arr['urlquery']  = $parsed['query'];
        $expected = '/path/to/file.php?foo=bar&bar=foo';
        $a = new \AWonderPHP\FileResource\GenericFileResource($arr);
        $actual = $a->getSrcAttribute();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Make sure we only build local url when host missing
     *
     * @return void
     */
    public function testBuilsSrcNoHost(): void
    {
        $expected = 'https://www.example.org/path/to/file.php?foo=bar&bar=foo';
        $parsed = parse_url($expected);
        $arr = array();
        $arr['urlscheme'] = $parsed['scheme'];
        //$arr['urlhost']   = $parsed['host'];
        $arr['urlpath']   = $parsed['path'];
        $arr['urlquery']  = $parsed['query'];
        $expected = '/path/to/file.php?foo=bar&bar=foo';
        $a = new \AWonderPHP\FileResource\GenericFileResource($arr);
        $actual = $a->getSrcAttribute();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Make sure we refuse to build src if scheme is http w/o checksum
     *
     * @return void
     */
    public function testNullSrcWithHttpNoChecksum(): void
    {
        $expected = 'http://www.example.org/path/to/file.php?foo=bar&bar=foo';
        $parsed = parse_url($expected);
        $arr = array();
        $arr['urlscheme'] = $parsed['scheme'];
        $arr['urlhost']   = $parsed['host'];
        $arr['urlpath']   = $parsed['path'];
        $arr['urlquery']  = $parsed['query'];
        $a = new \AWonderPHP\FileResource\GenericFileResource($arr);
        $actual = @$a->getSrcAttribute();
        //$this->assertEquals($expected, $actual);
        $this->assertNull($actual);
    }

    /**
     * Make sure we do build src if scheme is http w/ sha256
     *
     * @return void
     */
    public function testNullSrcWithHttpValidAlgoChecksum(): void
    {
        $expected = 'http://www.example.org/path/to/file.php?foo=bar&bar=foo';
        $parsed = parse_url($expected);
        $arr = array();
        $arr['urlscheme'] = $parsed['scheme'];
        $arr['urlhost']   = $parsed['host'];
        $arr['urlpath']   = $parsed['path'];
        $arr['urlquery']  = $parsed['query'];
        $arr['checksum']  = 'sha256:2328c729efad8ac17f711a4249d7deb909ecaa05ed116ee7a36e8b6ffee63dc0';
        $a = new \AWonderPHP\FileResource\GenericFileResource($arr);
        $actual = @$a->getSrcAttribute();
        $this->assertEquals($expected, $actual);
        //$this->assertNull($actual);
    }

    /**
     * Make sure we do not build src if scheme is http w/ ripemd160
     *
     * @return void
     */
    public function testNullSrcWithHttpInvalidAlgoChecksum(): void
    {
        $expected = 'http://www.example.org/path/to/file.php?foo=bar&bar=foo';
        $parsed = parse_url($expected);
        $arr = array();
        $arr['urlscheme'] = $parsed['scheme'];
        $arr['urlhost']   = $parsed['host'];
        $arr['urlpath']   = $parsed['path'];
        $arr['urlquery']  = $parsed['query'];
        $arr['checksum']  = 'ripemd160:c9483f0d7a0f560d09de5663e14799804c6c2db0';
        $a = new \AWonderPHP\FileResource\GenericFileResource($arr);
        $actual = @$a->getSrcAttribute();
        //$this->assertEquals($expected, $actual);
        $this->assertNull($actual);
    }

    /**
     * Make sure we do not build src if scheme is not null,http,https
     *
     * @return void
     */
    public function testNullSrcWithSftp(): void
    {
        $expected = 'sftp://www.example.org/path/to/archive.tar.bz2';
        $parsed = parse_url($expected);
        $arr = array();
        $arr['urlscheme'] = $parsed['scheme'];
        $arr['urlhost']   = $parsed['host'];
        $arr['urlpath']   = $parsed['path'];
        $a = new \AWonderPHP\FileResource\GenericFileResource($arr);
        $actual = @$a->getSrcAttribute();
        $this->assertNull($actual);
    }

    /**
     * Make sure we can build a local path
     *
     * @return void
     */
    public function testSrcWithLocalPath(): void
    {
        $arr = array();
        $arr['urlpath']   = '/path/to/image.jpg';
        $expected = $arr['urlpath'];
        $a = new \AWonderPHP\FileResource\GenericFileResource($arr);
        $actual = $a->getSrcAttribute();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Make sure we can build a local path and a valid prefix
     *
     * @return void
     */
    public function testSrcWithLocalPathAndValidPrefix(): void
    {
        $prefix = '/some/prefix';
        $arr = array();
        $arr['urlpath']   = '/path/to/image.jpg';
        $expected = $prefix . $arr['urlpath'];
        $a = new \AWonderPHP\FileResource\GenericFileResource($arr);
        $actual = $a->getSrcAttribute($prefix);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Make sure prefix without leading slash invalidates
     *
     * @return void
     */
    public function testSrcWithLocalPathBadPrefixNoLeadingForwardSlash(): void
    {
        $prefix = 'some/prefix';
        $arr = array();
        $arr['urlpath']   = '/path/to/image.jpg';
        $expected = $prefix . $arr['urlpath'];
        $a = new \AWonderPHP\FileResource\GenericFileResource($arr);
        $actual = $a->getSrcAttribute($prefix);
        //$this->assertEquals($expected, $actual);
        $this->assertNull($actual);
    }

    /**
     * Make sure prefix without illegal characters invalidates
     *
     * @return void
     */
    public function testSrcWithLocalPathBadPrefixContainsSpace(): void
    {
        $prefix = '/some prefix';
        $arr = array();
        $arr['urlpath']   = '/path/to/image.jpg';
        $expected = $prefix . $arr['urlpath'];
        $a = new \AWonderPHP\FileResource\GenericFileResource($arr);
        $actual = $a->getSrcAttribute($prefix);
        //$this->assertEquals($expected, $actual);
        $this->assertNull($actual);
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

    //timestamp tests

    /**
     * Make sure we produce a valid UNIX timestamp from ISO 8601 string
     *
     * @return void
     */
    public function testUnixTimestampFromISO8601(): void
    {
        $arr = array();
        $arr['lastmod'] = '2018-03-14T11:51:00Z';
        $a = new \AWonderPHP\FileResource\GenericFileResource($arr);
        $expected = 1521028260;
        $actual = $a->getTimestamp();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Make sure we produce a valid UNIX timestamp from non ISO 8601 string that strtotime() can parse
     *
     * @return void
     */
    public function testUnixTimestampFromAbsoluteDateString(): void
    {
        $arr = array();
        $arr['lastmod'] = 'Wed Mar 14 05:08:33 PDT 2018';
        $a = new \AWonderPHP\FileResource\GenericFileResource($arr);
        $expected = 1521029313;
        $actual = $a->getTimestamp();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Make sure a relative date string produces null
     *
     * @return void
     */
    public function testUnixTimestampFromRelativeDateString(): void
    {
        $arr = array();
        $arr['lastmod'] = '+1 week';
        $a = new \AWonderPHP\FileResource\GenericFileResource($arr);
        $b = $a->getTimestamp();
        $this->assertNull($b);
    }
}

?>