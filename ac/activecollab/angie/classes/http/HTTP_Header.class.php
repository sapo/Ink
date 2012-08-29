<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * HTTP::Header
 * 
 * PHP versions 4 and 5
 *
 * @category    HTTP
 * @package     HTTP_Header
 * @author      Wolfram Kriesing <wk@visionp.de>
 * @author      Davey Shafik <davey@php.net>
 * @author      Michael Wallner <mike@php.net>
 * @copyright   2003-2005 The Authors
 * @license     BSD, revised
 * @version     CVS: $Id: Header.php,v 1.32 2005/11/08 19:06:10 mike Exp $
 * @link        http://pear.php.net/package/HTTP_Header
 */

/**#@+
 * Information Codes
 */
define('HTTP_HEADER_STATUS_100', '100 Continue');
define('HTTP_HEADER_STATUS_101', '101 Switching Protocols');
define('HTTP_HEADER_STATUS_102', '102 Processing');
define('HTTP_HEADER_STATUS_INFORMATIONAL',1);
/**#@-*/

/**#+
 * Success Codes
 */
define('HTTP_HEADER_STATUS_200', '200 OK');
define('HTTP_HEADER_STATUS_201', '201 Created');
define('HTTP_HEADER_STATUS_202', '202 Accepted');
define('HTTP_HEADER_STATUS_203', '203 Non-Authoritative Information');
define('HTTP_HEADER_STATUS_204', '204 No Content');
define('HTTP_HEADER_STATUS_205', '205 Reset Content');
define('HTTP_HEADER_STATUS_206', '206 Partial Content');
define('HTTP_HEADER_STATUS_207', '207 Multi-Status');
define('HTTP_HEADER_STATUS_SUCCESSFUL',2);
/**#@-*/

/**#@+
 * Redirection Codes
 */
define('HTTP_HEADER_STATUS_300', '300 Multiple Choices');
define('HTTP_HEADER_STATUS_301', '301 Moved Permanently');
define('HTTP_HEADER_STATUS_302', '302 Found');
define('HTTP_HEADER_STATUS_303', '303 See Other');
define('HTTP_HEADER_STATUS_304', '304 Not Modified');
define('HTTP_HEADER_STATUS_305', '305 Use Proxy');
define('HTTP_HEADER_STATUS_306', '306 (Unused)');
define('HTTP_HEADER_STATUS_307', '307 Temporary Redirect');
define('HTTP_HEADER_STATUS_REDIRECT',3);
/**#@-*/

/**#@+
 * Error Codes
 */
define('HTTP_HEADER_STATUS_400', '400 Bad Request');
define('HTTP_HEADER_STATUS_401', '401 Unauthorized');
define('HTTP_HEADER_STATUS_402', '402 Payment Granted');
define('HTTP_HEADER_STATUS_403', '403 Forbidden');
define('HTTP_HEADER_STATUS_404', '404 File Not Found');
define('HTTP_HEADER_STATUS_405', '405 Method Not Allowed');
define('HTTP_HEADER_STATUS_406', '406 Not Acceptable');
define('HTTP_HEADER_STATUS_407', '407 Proxy Authentication Required');
define('HTTP_HEADER_STATUS_408', '408 Request Time-out');
define('HTTP_HEADER_STATUS_409', '409 Conflict');
define('HTTP_HEADER_STATUS_410', '410 Gone');
define('HTTP_HEADER_STATUS_411', '411 Length Required');
define('HTTP_HEADER_STATUS_412', '412 Precondition Failed');
define('HTTP_HEADER_STATUS_413', '413 Request Entity Too Large');
define('HTTP_HEADER_STATUS_414', '414 Request-URI Too Large');
define('HTTP_HEADER_STATUS_415', '415 Unsupported Media Type');
define('HTTP_HEADER_STATUS_416', '416 Requested range not satisfiable');
define('HTTP_HEADER_STATUS_417', '417 Expectation Failed');
define('HTTP_HEADER_STATUS_422', '422 Unprocessable Entity');
define('HTTP_HEADER_STATUS_423', '423 Locked');
define('HTTP_HEADER_STATUS_424', '424 Failed Dependency');
define('HTTP_HEADER_STATUS_CLIENT_ERROR',4);
/**#@-*/

/**#@+
 * Server Errors
 */
define('HTTP_HEADER_STATUS_500', '500 Internal Server Error');
define('HTTP_HEADER_STATUS_501', '501 Not Implemented');
define('HTTP_HEADER_STATUS_502', '502 Bad Gateway');
define('HTTP_HEADER_STATUS_503', '503 Service Unavailable');
define('HTTP_HEADER_STATUS_504', '504 Gateway Time-out');
define('HTTP_HEADER_STATUS_505', '505 HTTP Version not supported');
define('HTTP_HEADER_STATUS_507', '507 Insufficient Storage');
define('HTTP_HEADER_STATUS_SERVER_ERROR',5);
/**#@-*/

/**
 * HTTP_Header
 * 
 * @package     HTTP_Header
 * @category    HTTP
 * @access      public
 * @version     $Revision: 1.32 $
 */
class HTTP_Header extends HTTP
{
    /**
     * Default Headers
     * 
     * The values that are set as default, are the same as PHP sends by default.
     * 
     * @var     array
     * @access  private
     */
    var $_headers = array(
        'content-type'  =>  'text/html',
        'pragma'        =>  'no-cache',
        'cache-control' =>  'no-store, no-cache, must-revalidate, post-check=0, pre-check=0'
    );

    /**
     * HTTP version
     * 
     * @var     string
     * @access  private
     */
    var $_httpVersion = '1.0';

    /**
     * Constructor
     *
     * Sets HTTP version.
     * 
     * @access  public
     * @return  object  HTTP_Header
     */
    function HTTP_Header()
    {
        if (isset($_SERVER['SERVER_PROTOCOL'])) {
            $this->setHttpVersion(substr($_SERVER['SERVER_PROTOCOL'], -3));
        }
    }
    
    /**
     * Set HTTP version
     *
     * @access  public
     * @return  bool    Returns true on success or false if version doesn't 
     *                  match 1.0 or 1.1 (note: 1 will result in 1.0)
     * @param   mixed   $version HTTP version, either 1.0 or 1.1
     */
    function setHttpVersion($version)
    {
        $version = round((float) $version, 1);
        if ($version < 1.0 || $version > 1.1) {
            return false;
        }
        $this->_httpVersion = sprintf('%0.1f', $version);
        return true;
    }
    
    /**
     * Get HTTP version
     *
     * @access  public
     * @return  string
     */
    function getHttpVersion()
    {
        return $this->_httpVersion;
    }
    
    /**
     * Set Header
     * 
     * The default value for the Last-Modified header will be current
     * date and atime if $value is omitted.
     * 
     * @access  public
     * @return  bool    Returns true on success or false if $key was empty or
     *                  $value was not of an scalar type.
     * @param   string  $key The name of the header.
     * @param   string  $value The value of the header. (NULL to unset header)
     */
    function setHeader($key, $value = null)
    {
        if (empty($key) || (isset($value) && !is_scalar($value))) {
            return false;
        }
        
        $key = strToLower($key);
        if ($key == 'last-modified') {
            if (!isset($value)) {
                $value = HTTP::Date(time());
            } elseif (is_numeric($value)) {
                $value = HTTP::Date($value);
            }
        }
        
        if (isset($value)) {
            $this->_headers[$key] = $value;
        } else {
            unset($this->_headers[$key]);
        }
        
        return true;
    }

    /**
     * Get Header
     * 
     * If $key is omitted, all stored headers will be returned.
     * 
     * @access  public
     * @return  mixed   Returns string value of the requested header,
     *                  array values of all headers or false if header $key
     *                  is not set.
     * @param   string  $key    The name of the header to fetch.
     */
    function getHeader($key = null)
    {
        if (!isset($key)) {
            return $this->_headers;
        }
        
        $key = strToLower($key);
        
        if (!isset($this->_headers[$key])) {
            return false;
        }
        
        return $this->_headers[$key];
    }

    /**
     * Send Headers
     * 
     * Send out the header that you set via setHeader().
     * 
     * @access  public
     * @return  bool    Returns true on success or false if headers are already
     *                  sent.
     * @param   array   $keys Headers to (not) send, see $include.
     * @param   array   $include If true only $keys matching headers will be
     *                  sent, if false only header not matching $keys will be
     *                  sent.
     */
    function sendHeaders($keys = array(), $include = true)
    {
        if (headers_sent()) {
            return false;
        }
        
        if (count($keys)) {
            array_change_key_case($keys, CASE_LOWER);
            foreach ($this->_headers as $key => $value) {
                if ($include ? in_array($key, $keys) : !in_array($key, $keys)) {
                    header($key .': '. $value);
                }
            }
        } else {
            foreach ($this->_headers as $header => $value) {
                header($header .': '. $value);
            }
        }
        return true;
    }

    /**
     * Send Satus Code
     * 
     * Send out the given HTTP-Status code. Use this for example when you 
     * want to tell the client this page is cached, then you would call 
     * sendStatusCode(304).
     *
     * @see HTTP_Header_Cache::exitIfCached()
     * 
     * @access  public
     * @return  bool    Returns true on success or false if headers are already
     *                  sent.
     * @param   int     $code The status code to send, i.e. 404, 304, 200, etc.
     */
    function sendStatusCode($code)
    {
        if (headers_sent()) {
            return false;
        }
        
        if ($code == (int) $code && defined('HTTP_HEADER_STATUS_'. $code)) {
            $code = constant('HTTP_HEADER_STATUS_'. $code);
        }
        
        if (strncasecmp(PHP_SAPI, 'cgi', 3)) {
            header('HTTP/'. $this->_httpVersion .' '. $code);
        } else {
            header('Status: '. $code);
        }
        return true;
    }

    /**
     * Date to Timestamp
     * 
     * Converts dates like
     *      Mon, 31 Mar 2003 15:26:34 GMT
     *      Tue, 15 Nov 1994 12:45:26 GMT
     * into a timestamp, strtotime() didn't do it in older versions.
     *
     * @deprecated      Use PHPs strtotime() instead.
     * @access  public
     * @return  mixed   Returns int unix timestamp or false if the date doesn't
     *                  seem to be a valid GMT date.
     * @param   string  $date The GMT date.
     */
    function dateToTimestamp($date)
    {
        static $months = array(
            null => 0, 'Jan' => 1, 'Feb' => 2, 'Mar' => 3, 'Apr' => 4,
            'May' => 5, 'Jun' => 6, 'Jul' => 7, 'Aug' => 8, 'Sep' => 9,
            'Oct' => 10, 'Nov' => 11, 'Dec' => 12
        );
        
        if (-1 < $timestamp = strToTime($date)) {
            return $timestamp;
        }
        
        if (!preg_match('~[^,]*,\s(\d+)\s(\w+)\s(\d+)\s(\d+):(\d+):(\d+).*~',
            $date, $m)) {
            return false;
        }
        
        // [0] => Mon, 31 Mar 2003 15:42:55 GMT
        // [1] => 31 [2] => Mar [3] => 2003 [4] => 15 [5] => 42 [6] => 55
        return mktime($m[4], $m[5], $m[6], $months[$m[2]], $m[1], $m[3]);
    }

    /**
     * Redirect
     * 
     * This function redirects the client. This is done by issuing a Location 
     * header and exiting.  Additionally to HTTP::redirect() you can also add 
     * parameters to the url.
     * 
     * If you dont need parameters to be added, simply use HTTP::redirect()
     * otherwise use HTTP_Header::redirect().
     *
     * @see     HTTP::redirect()
     * @author  Wolfram Kriesing <wk@visionp.de>
     * @access  public
     * @return  void
     * @param   string  $url The URL to redirect to, if none is given it 
     *                  redirects to the current page.
     * @param   array   $param Array of query string parameters to add; usually
     *                  a set of key => value pairs; if an array entry consists
     *                  only of an value it is used as key and the respective
     *                  value is fetched from $GLOBALS[$value]
     * @param   bool    $session Whether the session name/id should be added
     */
    function redirect($url = null, $param = array(), $session = false)
    {
        if (!isset($url)) {
            $url = $_SERVER['PHP_SELF'];
        }
        
        $qs = array();

        if ($session) {
            $qs[] = session_name() .'='. session_id();
        }

        if (is_array($param) && count($param)) {
            if (count($param)) {
                foreach ($param as $key => $val) {
                    if (is_string($key)) {
                        $qs[] = urlencode($key) .'='. urlencode($val);
                    } else {
                        $qs[] = urlencode($val) .'='. urlencode(@$GLOBALS[$val]);
                    }
                }
            }
        }
        
        if ($qstr = implode('&', $qs)) {
            $purl = parse_url($url);
            $url .= (isset($purl['query']) ? '&' : '?') . $qstr;
        }

        parent::redirect($url);
    }

    /**#@+
     * @author  Davey Shafik <davey@php.net>
     * @param   int $http_code HTTP Code to check
     * @access  public
     */

    /**
     * Return HTTP Status Code Type
     *
     * @return int|false
     */
    function getStatusType($http_code) 
    {
        if(is_int($http_code) && defined('HTTP_HEADER_STATUS_' .$http_code) || defined($http_code)) {
            $type = substr($http_code,0,1);
            switch ($type) {
                case HTTP_HEADER_STATUS_INFORMATIONAL:
                case HTTP_HEADER_STATUS_SUCCESSFUL:
                case HTTP_HEADER_STATUS_REDIRECT:
                case HTTP_HEADER_STATUS_CLIENT_ERROR:
                case HTTP_HEADER_STATUS_SERVER_ERROR:
                    return $type;
                    break;
                default:
                    return false;
                    break;
            }
        } else {
            return false;
        }
    }

    /**
     * Return Status Code Message
     *
     * @return string|false
     */
    function getStatusText($http_code) 
    {
        if ($this->getStatusType($http_code)) {
            if (is_int($http_code) && defined('HTTP_HEADER_STATUS_' .$http_code)) {
                return substr(constant('HTTP_HEADER_STATUS_' .$http_code),4);
            } else {
                return substr($http_code,4);
            }
        } else {
            return false;
        }
    }

    /**
     * Checks if HTTP Status code is Information (1xx)
     *
     * @return boolean
     */
    function isInformational($http_code) 
    {
        if ($status_type = $this->getStatusType($http_code)) {
            return $status_type{0} == HTTP_HEADER_STATUS_INFORMATIONAL;
        } else {
            return false;
        }
    }

    /**
     * Checks if HTTP Status code is Successful (2xx)
     *
     * @return boolean
     */
    function isSuccessful($http_code) 
    {
        if ($status_type = $this->getStatusType($http_code)) {
            return $status_type{0} == HTTP_HEADER_STATUS_SUCCESSFUL;
        } else {
            return false;
        }
    }

    /**
     * Checks if HTTP Status code is a Redirect (3xx)
     *
     * @return boolean
     */
    function isRedirect($http_code) 
    {
        if ($status_type = $this->getStatusType($http_code)) {
            return $status_type{0} == HTTP_HEADER_STATUS_REDIRECT;
        } else {
            return false;
        }
    }

    /**
     * Checks if HTTP Status code is a Client Error (4xx)
     *
     * @return boolean
     */
    function isClientError($http_code) 
    {
        if ($status_type = $this->getStatusType($http_code)) {
            return $status_type{0} == HTTP_HEADER_STATUS_CLIENT_ERROR;
        } else {
            return false;
        }
    }

    /**
     * Checks if HTTP Status code is Server Error (5xx)
     *
     * @return boolean
     */
    function isServerError($http_code) 
    {
        if ($status_type = $this->getStatusType($http_code)) {
            return $status_type{0} == HTTP_HEADER_STATUS_SERVER_ERROR;
        } else {
            return false;
        }
    }

    /**
     * Checks if HTTP Status code is Server OR Client Error (4xx or 5xx)
     *
     * @return boolean
     */
    function isError($http_code) 
    {
        if ($status_type = $this->getStatusType($http_code)) {
            return (($status_type == HTTP_HEADER_STATUS_CLIENT_ERROR) || ($status_type == HTTP_HEADER_STATUS_SERVER_ERROR)) ? true : false;
        } else {
            return false;
        }
    }
    /**#@-*/
}
?>
