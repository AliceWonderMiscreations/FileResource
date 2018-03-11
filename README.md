FileResource Abstract Class
===========================
An abstract class for resources embedded in a web page (images, scripts,
whatever)

__WARNING__: Developer Release, may have serious flaws.

This package is intended to provide a standard means by which web applications
can treat resources embedded in the web page as objects.

The advantage of treating resources as objects is it allows them to have both
properties and methods that can do stuff with those properties and/or
parameters fed to those methods.

This package also includes an abstract class for using PHP to serve the
resource described by a FileResource object.


Class Properties
----------------

The following properties are defined by the abstract class:

* `protected $mime = null;`  
  The MIME type the file should be served with when a client requests it.

* `protected $checksum = null;`  
  The algorithm used to calculate a checksum digest, followed by a colon,
  followed by either a hex or base64 representation of the checksum digest. It
  is recommended to use one of the SHA-2 algorithms that browsers are required
  to support for use with the `integrity` attribute.

* `protected $validIntegrityAlgo = array('sha256', 'sha384', 'sha512');`  
  Algorithms that are supported by all web browsers that support the
  `integrity` attribute.

* `protected $filepath = null;`  
  The full path on the local server to the file. Please note this property
  should remain `null` when the file described by the object is not present
  on the local filesystem.

* `protected $crossorigin = null;`  
  The contents of the `crossorigin` attribute that should be used when creating
  an (X)HTML node that references the resource.

* `protected $lastmod = null;`  
  The last time the file was modified, using ISO 8601 in `Y-m-d\TH:i:sO`, which
  can be achieve in PHP as `date('c')`. This does not need to match the file
  timestamp on the local filesystem, which often is not accurate.

* `protected $urlscheme = null;`  
  Should be one of `null`, `http`, or `https`. The scheme to use when creating
  a `src` or `href` attribute to embed the resource in a web page. This should
  be `null` when the resource will be served from the same server that is
  serving the web page.

* `protected $urlhost = null;`  
  Should be `null` or the hostname to use when creating a `src` or `href`
  attribute to embed the resource in a web page. This should be `null` when the
  resource will be served from the same server as that is serving the web page.

* `protected $urlpath = null;`  
  The path to the resource on the server that is serving the resource when
  creating a `src` or `href` attribute to embed the resource in a web page.

* `protected $urlquery = null;`  
  In the event a URL query is needed (the part after the `?` in a URL), the
  query that should be appended at the end of the URL when construction a `src`
  or `href` attribute to embed the resource in a web page.

### Notes About the Class Properties

For many files, additional properties are needed to adequately embed the
resource within a web page (such as `alt` tag and possibly `caption` for an
image)

This abstract class is intended to define *most* properties commonly needed for
embedding a resource in a web page and *everything* that is needed for a PHP
wrapper to correctly serve the file.

The four `url*` properties correspond with the the PHP function
[`parse_url`](http://php.net/manual/en/function.parse-url.php) with the
following ommissions:

* `port`  
  Only applicable if the resource is remote, in which case HTTPS should be
  preferred and only the default port 443 should be used for HTTPS.

* `user`  
  Not an appropriate component of a URL for embedding resources in a web page.

* `pass`  
  Not an appropriate component of a URL for embedding resources in a web page.

* `fragment`  
  Not an appropriate component of a URL for embedding resources in a web page.


Class Methods
-------------

The following methods are defined by the abstract class:

* `public function getMimeType()`  
  Returns the `$mime` property.

* `public function getChecksum()`  
  Returns the `$checksum` property.

* `public function getCrossOrigin()`  
  Returns the `$crossorigin` property.

* `public function getFilePath()`  
  Returns the `$filepath` property.

* `public function validateFile()`  
  If the `$checksum` property is set *and* the `$filepath` property is set
  *and* the file exists, returns `true` if the file matches the checksum and
  `false` if it does not.

* `public function getSrcAttribute($prefix = null)`  
  Builds the contents of the `src` or `href` attribute needed to embed the
  resource in a web page. Note that this will return `null` if the `$urlscheme`
  property is `http` and the file does not have a `$checksum` property that
  uses an algorithm in the `$validIntegrityAlgo` property. The optional
  parameter `$prefix` is a filesystem path to put in front of the `$urlpath`
  property, useful for web applications using a wrapper to serve the file.

* `public function getIntegrityAttribute()`  
  Builds the contents of an `integrity` attribute, if the `$checksum` property
  uses a suitable algorithm.

* `public function getTimestamp()`  
  If the `$lastmode` property is not null, returns a UNIX timestamp (seconds
  from UNIX epoch).



AWonderPHP\FileResource\FileWrapper
================================

This is a class that extends my `\AWonderPHP\FileWrapper\FileWrapper` class.
The class it extends was written to be a download wrapper including support
for client cache validation and partial content requests.

However that class uses the timestamp of the file on the filesystem for the
`Last-Modified` header and uses the inode of the file on the filesystem in
its generation of the `ETag` header.

The extended class allows the modification timestamp and the ETag to be set
independent of the file on the filesystem.



AWonderPHP\FileResource\ResourceServer
===================================

This is an abstract class that provides a public method for serving a file
based from a `FileResource` object.

The idea is to extend the class adding a method construct the necessary
`FileResource` object and then serve it.

-----------------------------------
__EOF__