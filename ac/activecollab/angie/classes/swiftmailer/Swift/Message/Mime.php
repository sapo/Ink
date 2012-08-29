<?php

/**
 * Swift Mailer MIME Library central component
 * Please read the LICENSE file
 * @copyright Chris Corbyn <chris@w3style.co.uk>
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Swift_Message
 * @license GNU Lesser General Public License
 */

require_once dirname(__FILE__) . "/../ClassLoader.php";
Swift_ClassLoader::load("Swift_Message_MimeException");
Swift_ClassLoader::load("Swift_File");

if (!defined("SWIFT_MIME_PLAIN")) define("SWIFT_MIME_PLAIN", "text/plain");
if (!defined("SWIFT_MIME_HTML")) define("SWIFT_MIME_HTML", "text/html");
if (!defined("SWIFT_MIME_MISC")) define("SWIFT_MIME_MISC", "application/octet-stream");
if (!defined("SWIFT_MIME_SAFELENGTH")) define("SWIFT_MIME_SAFELENGTH", 1000);
if (!defined("SWIFT_MIME_VERYSAFELENGTH")) define("SWIFT_MIME_VERYSAFELENGTH", 76);
if (!defined("SWIFT_MIME_LEVEL_ALTERNATIVE")) define("SWIFT_MIME_LEVEL_ALTERNATIVE", "alternative");
if (!defined("SWIFT_MIME_LEVEL_RELATED")) define("SWIFT_MIME_LEVEL_RELATED", "related");
if (!defined("SWIFT_MIME_LEVEL_MIXED")) define("SWIFT_MIME_LEVEL_MIXED", "mixed");
if (!defined("SWIFT_MIME_LEVEL_TOP")) define("SWIFT_MIME_LEVEL_TOP", "top");

/**
 * Mime is the underbelly for Messages, Attachments, Parts, Embedded Images, Forwarded Mail, etc
 * In fact, every single component of the composed email is simply a new Mime document nested inside another
 * When you piece an email together in this way you see just how straight-forward it really is
 * @package Swift_Message
 * @author Chris Corbyn <chris@w3style.co.uk>
 */
class Swift_Message_Mime
{
  /**
   * Constant for plain-text emails
   */
  var $PLAIN = SWIFT_MIME_PLAIN;
  /**
   * Constant for HTML emails
   */
  var $HTML = SWIFT_MIME_HTML;
  /**
   * Constant for miscellaneous mime type
   */
  var $MISC = SWIFT_MIME_MISC;
  /**
   * Constant for safe line length in almost all places
   */
  var $SAFE_LENGTH = SWIFT_MIME_SAFELENGTH; //RFC 2822
  /**
   * Constant for really safe line length
   */
  var $VERY_SAFE_LENGTH = SWIFT_MIME_VERYSAFELENGTH; //For command line mail clients such as pine
  /**
   * The header part of this MIME document
   * @var Swift_Message_Headers
   */
  var $headers = null;
  /**
   * The body of the documented (unencoded)
   * @var string data
   */
  var $data = "";
  /**
   * Maximum line length
   * @var int
   */
  var $wrap = SWIFT_MIME_SAFELENGTH; //RFC 2822
  /**
   * Nested mime parts
   * @var array
   */
  var $children = array();
  /**
   * The boundary used to separate mime parts
   * @var string
   */
  var $boundary = null;
  /**
   * The line ending characters needed
   * @var string
   */
  var $LE = "\r\n";
  /**
   * An instance of the encoder
   * @var Swift_Message_Encoder
   */
  var $_encoder;
  /**
   * An instance of Swift_Cache
   * @var Swift_Cache
   */
  var $cache;
  
  /**
   * Constructor
   */
  function Swift_Message_Mime()
  {
    Swift_ClassLoader::load("Swift_Message_Encoder");
    $this->_encoder =& Swift_Message_Encoder::instance();
    Swift_ClassLoader::load("Swift_Message_Headers");
    $headers =& new Swift_Message_Headers();
    $this->setHeaders($headers);
    Swift_ClassLoader::load("Swift_CacheFactory");
    $this->cache =& Swift_CacheFactory::getCache();
  }
  /**
   * Compute a unique boundary
   * @return string
   */
  function generateBoundary()
  {
    static $used = null;
    if (!$used) $used = array();
    
    do
    {
      $boundary = uniqid(rand(), true);
    } while (in_array($boundary, $used));
    $used[] = $boundary;
    return "_=_swift-" . $boundary . "_=_";
  }
  /**
   * Replace the current headers with new ones
   * DO NOT DO THIS UNLESS YOU KNOW WHAT YOU'RE DOING!
   * @param Swift_Message_Headers The headers to use
   */
  function setHeaders(&$headers)
  {
    $this->headers =& $headers;
  }
  /**
   * Set the line ending character to use
   * @param string The line ending sequence
   * @return boolean
   */
  function setLE($le)
  {
    if (in_array($le, array("\r", "\n", "\r\n")))
    {
      $this->cache->clear("body");
      $this->LE = $le;
      //This change should be recursive
      $this->headers->setLE($le);
      foreach ($this->children as $id => $child)
      {
        $this->children[$id]->setLE($le);
      }
      
      return true;
    }
    else return false;
  }
  /**
   * Get the line ending sequence
   * @return string
   */
  function getLE()
  {
    return $this->LE;
  }
  /**
   * Reset the entire cache state from this branch of the tree and traversing down through the children
   */
  function uncacheAll()
  {
    $this->cache->clear("body");
    $this->cache->clear("append");
    $this->cache->clear("headers");
    $this->cache->clear("dbl_le");
    $this->headers->uncacheAll();
    foreach ($this->children as $id => $child)
    {
      $this->children[$id]->uncacheAll();
    }
  }
  /**
   * Set the content type of this MIME document
   * @param string The content type to use in the same format as MIME 1.0 expects
   */
  function setContentType($type)
  {
    $this->headers->set("Content-Type", $type);
  }
  /**
   * Get the content type which has been set
   * The MIME 1.0 Content-Type is provided as a string
   * @return string
   */
  function getContentType()
  {
    return $this->headers->get("Content-Type");
  }
  /**
   * Set the encoding format to be used on the body of the document
   * @param string The encoding type used
   * @param boolean If this encoding format should be used recursively. Note, this only takes effect if no encoding is set in the children.
   * @param boolean If the encoding should only be applied when the string is not ascii.
   */
  function setEncoding($encoding, $recursive=false, $non_ascii=false)
  {
    $this->cache->clear("body");
    switch (strtolower($encoding))
    {
      case "q": case "qp": case "quoted-printable":
        $encoding = "quoted-printable";
        break;
      case "b": case "base64":
        $encoding = "base64";
        break;
      case "7bit": case "8bit": case "binary":
        $encoding = strtolower($encoding);
        break;
    }
    
    $data =& $this->getData();
    if ($non_ascii && is_string($data) && !$this->_encoder->is7BitAscii($data))
    {
      $this->headers->set("Content-Transfer-Encoding", $encoding);
    }
    elseif (!$non_ascii || !is_string($data))
    {
      $this->headers->set("Content-Transfer-Encoding", $encoding);
    }
    
    if ($recursive)
    {
      foreach ($this->children as $id => $child)
      {
        if (!$child->getEncoding()) $this->children[$id]->setEncoding($encoding, $recursive, $non_ascii);
      }
    }
  }
  /**
   * Get the encoding format used in this document
   * @return string
   */
  function getEncoding()
  {
    return $this->headers->get("Content-Transfer-Encoding");
  }
  /**
   * Specify the string which makes up the body of this message
   * HINT: You can always nest another MIME document here if you call it's build() method.
   * $data can be an object of Swift_File or a string
   * @param mixed The body of the document
   */
  function setData($data)
  {
    $this->cache->clear("body");
    if (is_a($data, "Swift_File")) $this->data =& $data;
    else $this->data = (string) $data;
  }
  /**
   * Return the string which makes up the body of this MIME document
   * @return string,Swift_File
   */
  function &getData()
  {
    return $this->data;
  }
  /**
   * Get the data in the format suitable for sending
   * @return Swift_Cache_OutputStream
   * @throws Swift_FileException If the file stream given cannot be read
   * @throws Swift_Message_MimeException If some required headers have been forcefully removed
   */
  function &buildData()
  {
    Swift_ClassLoader::load("Swift_Cache_JointOutputStream");
    $encoder =& $this->_encoder;
    
    if (!empty($this->children)) //If we've got some mime parts we need to stick them onto the end of the message
    {
      if ($this->boundary === null) $this->boundary = Swift_Message_Mime::generateBoundary();
      $this->headers->setAttribute("Content-Type", "boundary", $this->boundary);
      
      $this->cache->clear("append");
      foreach ($this->children as $id => $part)
      {
        $this->cache->write("append", $this->LE . "--" . $this->boundary . $this->LE);
        $part_stream = $this->children[$id]->build();
        while (false !== $bytes = $part_stream->read()) $this->cache->write("append", $bytes);
      }
      $this->cache->write("append", $this->LE . "--" . $this->boundary . "--" . $this->LE);
    }
    
    $i = array();
    $joint_os = new Swift_Cache_JointOutputStream($i);
    
    //Try using a cached version to save some cycles (at the expense of memory)
    if ($this->cache->has("body"))
    {
      $body =& $this->cache->getOutputStream("body");
      $joint_os->addStream($body);
      $append =& $this->cache->getOutputStream("append");
      $joint_os->addStream($append);
      return $joint_os;
    }
    
    $data =& $this->getData();
    
    $is_file = (is_a($this->getData(), "Swift_File"));
    switch ($this->getEncoding())
    {
      case "quoted-printable":
        if ($is_file)
        {
          $qp_os = $encoder->QPEncodeFile($this->getData(), 76, $this->LE);
          while (false !== $bytes = $qp_os->read())
            $this->cache->write("body", $bytes);
        }
        else
        {
          $this->cache->write("body", $encoder->QPEncode($this->getData(), 76, 0, false, $this->LE));
        }
        break;
      case "base64":
        if ($is_file)
        {
          $b64_os = $encoder->base64EncodeFile($this->getData(), 76, $this->LE);
          while (false !== $bytes = $b64_os->read())
            $this->cache->write("body", $bytes);
        }
        else
        {
          $this->cache->write("body", $encoder->base64Encode($this->getData(), 76, 0, false, $this->LE));
        }
        break;
      case "binary":
        if ($is_file)
        {
          $data = $this->getData();
          while (false !== $bytes = $data->read(8192))
            $this->cache->write("body", $bytes);
        }
        else
        {
          $this->cache->write("body", $this->getData());
        }
        break;
      case "7bit":
        if ($is_file)
        {
          $os = $encoder->encode7BitFile($this->getData(), $this->wrap, $this->LE);
          while (false !== $bytes = $os->read())
            $this->cache->write("body", $bytes);
        }
        else
        {
          $this->cache->write("body", $encoder->encode7Bit($this->getData(), $this->wrap, $this->LE));
        }
        break;
      case "8bit": default:
        if ($is_file)
        {
          $os = $encoder->encode8BitFile($this->getData(), $this->wrap, $this->LE);
          while (false !== $bytes = $os->read())
            $this->cache->write("body", $bytes);
        }
        else
        {
          $this->cache->write("body", $encoder->encode8Bit($this->getData(), $this->wrap, $this->LE));
        }
        break;
    }
    $body =& $this->cache->getOutputStream("body");
    $joint_os->addStream($body);
    $append =& $this->cache->getOutputStream("append");
    $joint_os->addStream($append);
    return $joint_os;
  }
  /**
   * Set the size at which lines wrap around (includes the CRLF)
   * @param int The length of a line
   */
  function setLineWrap($len)
  {
    $this->cache->clear("body");
    $this->wrap = (int) $len;
  }
  /**
   * Nest a child mime part in this document
   * @param Swift_Message_Mime
   * @param string The identifier to use, optional
   * @param int Add the part before (-1) or after (+1) the other parts
   * @return string The identifier for this part
   */
  function addChild(&$mime, $id=null, $after=1)
  {
    if (!is_a($mime, "Swift_Message_Mime"))
    {
      trigger_error("Swift_Message_Mime can only add children of the same type [Swift_Message_Mime].");
      return;
    }
    if (empty($id))
    {
      do
      {
        $id = uniqid(microtime());
      } while (array_key_exists($id, $this->children));
    }
    $id = (string) $id;

    if ($after == -1)
    {
      $new = array();
      $new[$id] =& $mime;
      foreach ($this->listChildren() as $k)
      {
        $new[$k] =& $this->children[$k];
      }
      $this->children =& $new;
    }
    else $this->children[$id] =& $mime;
    
    return $id;
  }
  /**
   * Check if a child exists identified by $id
   * @param string Identifier to look for
   * @return boolean
   */
  function hasChild($id)
  {
    return array_key_exists($id, $this->children);
  }
  /**
   * Get a child document, identified by $id
   * @param string The identifier for this child
   * @return Swift_Message_Mime The child document
   * @throws Swift_Message_MimeException If no such child exists
   */
  function &getChild($id)
  {
    if ($this->hasChild($id))
    {
      return $this->children[$id];
    }
    else
    {
      Swift_Errors::trigger(new Swift_Message_MimeException(
      "Cannot retrieve child part identified by '" . $id . "' as it does not exist.  Consider using hasChild() to check."));
    }
  }
  /**
   * Remove a part from the document
   * @param string The identifier of the child
   */
  function removeChild($id)
  {
    $id = (string) $id;
    unset($this->children[$id]);
  }
  /**
   * List the IDs of all children in this document
   * @return array
   */
  function listChildren()
  {
    return array_keys($this->children);
  }
  /**
   * Get the total number of children present in this document
   * @return int
   */
  function numChildren()
  {
    return count($this->children);
  }
  /**
   * Get the level at which this mime part would appear in a document
   * One of "mixed", "alternative" or "related"
   * @return string
   */
  function getLevel() {}
  /**
   * Compile the entire MIME document into a string
   * The returned string may be used in other documents if needed.
   * @return Swift_Cache_OutputStream
   */
  function &build()
  {
    $this->preBuild();
    $data =& $this->buildData();
    $i = array();
    $joint_os =& new Swift_Cache_JointOutputStream($i);
    $this->cache->clear("headers");
    $this->cache->write("headers", $this->headers->build());
    $headers =& $this->cache->getOutputStream("headers");
    $joint_os->addStream($headers);
    $this->cache->clear("dbl_le");
    $this->cache->write("dbl_le", str_repeat($this->LE, 2));
    $dbl_le =& $this->cache->getOutputStream("dbl_le");
    $joint_os->addStream($dbl_le);
    $joint_os->addStream($data);
    return $joint_os;
  }
  /**
   * Execute any logic needed prior to building (abstract)
   */
  function preBuild() {}
}
