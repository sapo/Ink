<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * HTTP
 * 
 * PHP versions 4 and 5
 *
 * @category    HTTP
 * @package     HTTP
 * @author      Stig Bakken <ssb@fast.no>
 * @author      Sterling Hughes <sterling@php.net>
 * @author      Tomas V.V.Cox <cox@idecnet.com>
 * @author      Richard Heyes <richard@php.net>
 * @author      Philippe Jausions <Philippe.Jausions@11abacus.com>
 * @author      Michael Wallner <mike@php.net>
 * @copyright   2002-2005 The Authors
 * @license     BSD, revised
 * @version     CVS: $Id: HTTP.php,v 1.48 2005/11/08 20:11:54 mike Exp $
 * @link        http://pear.php.net/package/HTTP
 */

/**
 * Miscellaneous HTTP Utilities
 *
 * PEAR::HTTP provides static shorthand methods for generating HTTP dates,
 * issueing HTTP HEAD requests, building absolute URIs, firing redirects and
 * negotiating user preferred language.
 *
 * @package     HTTP
 * @category    HTTP
 * @access      public
 * @static
 * @version     $Revision: 1.48 $
 */
class HTTP
{
    /**
     * Date
     * 
     * Format a RFC compliant GMT date HTTP header.  This function honors the 
     * "y2k_compliance" php.ini directive and formats the GMT date corresponding
     * to either RFC850 or RFC822.
     * 
     * @static 
     * @access  public 
     * @return  mixed   GMT date string, or false for an invalid $time parameter
     * @param   mixed   $time unix timestamp or date (default = current time)
     */
    function Date($time = null)
    {
        if (!isset($time)) {
            $time = time();
        } elseif (!is_numeric($time) && (-1 === $time = strtotime($time))) {
            return false;
        }
        
        // RFC822 or RFC850
        $format = ini_get('y2k_compliance') ? 'D, d M Y' : 'l, d-M-y';
        
        return gmdate($format .' H:i:s \G\M\T', $time);
    }

    /**
     * Negotiate Language
     * 
     * Negotiate language with the user's browser through the Accept-Language 
     * HTTP header or the user's host address.  Language codes are generally in 
     * the form "ll" for a language spoken in only one country, or "ll-CC" for a 
     * language spoken in a particular country.  For example, U.S. English is 
     * "en-US", while British English is "en-UK".  Portugese as spoken in
     * Portugal is "pt-PT", while Brazilian Portugese is "pt-BR".
     * 
     * Quality factors in the Accept-Language: header are supported, e.g.:
     *      Accept-Language: en-UK;q=0.7, en-US;q=0.6, no, dk;q=0.8
     * 
     * <code>
     *  require_once 'HTTP.php';
     *  $langs = array(
     *      'en'   => 'locales/en',
     *      'en-US'=> 'locales/en',
     *      'en-UK'=> 'locales/en',
     *      'de'   => 'locales/de',
     *      'de-DE'=> 'locales/de',
     *      'de-AT'=> 'locales/de',
     *  );
     *  $neg = HTTP::negotiateLanguage($langs);
     *  $dir = $langs[$neg];
     * </code>
     * 
     * @static 
     * @access  public 
     * @return  string  The negotiated language result or the supplied default.
     * @param   array   $supported An associative array of supported languages,
     *                  whose values must evaluate to true.
     * @param   string  $default The default language to use if none is found.
     */
    function negotiateLanguage($supported, $default = 'en-US')
    {
        $supp = array();
        foreach ($supported as $lang => $isSupported) {
            if ($isSupported) {
                $supp[strToLower($lang)] = $lang;
            }
        }
        
        if (!count($supp)) {
            return $default;
        }

        $matches = array();
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            foreach (explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']) as $lang) {
                $lang = array_map('trim', explode(';', $lang));
                if (isset($lang[1])) {
                    $l = strtolower($lang[0]);
                    $q = (float) str_replace('q=', '', $lang[1]);
                } else {
                    $l = strtolower($lang[0]);
                    $q = null;
                }
                if (isset($supp[$l])) {
                    $matches[$l] = isset($q) ? $q : 1000 - count($matches);
                }
            }
        }

        if (count($matches)) {
            asort($matches, SORT_NUMERIC);
            return $supp[end($l = array_keys($matches))];
        }
        
        if (isset($_SERVER['REMOTE_HOST'])) {
            $lang = strtolower(end($h = explode('.', $_SERVER['REMOTE_HOST'])));
            if (isset($supp[$lang])) {
                return $supp[$lang];
            }
        }

        return $default;
    }

    /**
     * Head
     * 
     * Sends a "HEAD" HTTP command to a server and returns the headers
     * as an associative array. Example output could be:
     * <code>
     *     Array
     *     (
     *         [response_code] => 200          // The HTTP response code
     *         [response] => HTTP/1.1 200 OK   // The full HTTP response string
     *         [Date] => Fri, 11 Jan 2002 01:41:44 GMT
     *         [Server] => Apache/1.3.20 (Unix) PHP/4.1.1
     *         [X-Powered-By] => PHP/4.1.1
     *         [Connection] => close
     *         [Content-Type] => text/html
     *     )
     * </code>
     * 
     * @see HTTP_Client::head()
     * @see HTTP_Request
     * 
     * @static 
     * @access  public 
     * @return  mixed   Returns associative array of response headers on success
     *                  or PEAR error on failure.
     * @param   string  $url A valid URL, e.g.: http://pear.php.net/credits.php
     * @param   integer $timeout Timeout in seconds (default = 10)
     */
    function head($url, $timeout = 10)
    {
        $p = parse_url($url);
        if (!isset($p['scheme'])) {
            $p = parse_url(HTTP::absoluteURI($url));
        } elseif ($p['scheme'] != 'http') {
            return HTTP::raiseError('Unsupported protocol: '. $p['scheme']);
        }

        $port = isset($p['port']) ? $p['port'] : 80;

        if (!$fp = @fsockopen($p['host'], $port, $eno, $estr, $timeout)) {
            return HTTP::raiseError("Connection error: $estr ($eno)");
        }

        $path  = !empty($p['path']) ? $p['path'] : '/';
        $path .= !empty($p['query']) ? '?' . $p['query'] : '';

        fputs($fp, "HEAD $path HTTP/1.0\r\n");
        fputs($fp, 'Host: ' . $p['host'] . ':' . $port . "\r\n");
        fputs($fp, "Connection: close\r\n\r\n");

        $response = rtrim(fgets($fp, 4096));
        if (preg_match("|^HTTP/[^\s]*\s(.*?)\s|", $response, $status)) {
            $headers['response_code'] = $status[1];
        }
        $headers['response'] = $response;

        while ($line = fgets($fp, 4096)) {
            if (!trim($line)) {
                break;
            }
            if (($pos = strpos($line, ':')) !== false) {
                $header = substr($line, 0, $pos);
                $value  = trim(substr($line, $pos + 1));
                $headers[$header] = $value;
            }
        }
        fclose($fp);
        return $headers;
    }

    /**
     * Redirect
     * 
     * This function redirects the client. This is done by issuing
     * a "Location" header and exiting if wanted.  If you set $rfc2616 to true
     * HTTP will output a hypertext note with the location of the redirect.
     * 
     * @static 
     * @access  public 
     * @return  mixed   Returns true on succes (or exits) or false if headers
     *                  have already been sent.
     * @param   string  $url URL where the redirect should go to.
     * @param   bool    $exit Whether to exit immediately after redirection.
     * @param   bool    $rfc2616 Wheter to output a hypertext note where we're
     *                  redirecting to (Redirecting to <a href="...">...</a>.)
     */
    function redirect($url, $exit = true, $rfc2616 = false)
    {
        if (headers_sent()) {
            return false;
        }
        
        $url = HTTP::absoluteURI($url);
        header('Location: '. $url);
        
        if (    $rfc2616 && isset($_SERVER['REQUEST_METHOD']) &&
                $_SERVER['REQUEST_METHOD'] != 'HEAD') {
            printf('Redirecting to: <a href="%s">%s</a>.', $url, $url);
        }
        if ($exit) {
            exit;
        }
        return true;
    }

    /**
     * Absolute URI
     * 
     * This function returns the absolute URI for the partial URL passed.
     * The current scheme (HTTP/HTTPS), host server, port, current script
     * location are used if necessary to resolve any relative URLs.
     * 
     * Offsets potentially created by PATH_INFO are taken care of to resolve
     * relative URLs to the current script.
     * 
     * You can choose a new protocol while resolving the URI.  This is 
     * particularly useful when redirecting a web browser using relative URIs 
     * and to switch from HTTP to HTTPS, or vice-versa, at the same time.
     * 
     * @author  Philippe Jausions <Philippe.Jausions@11abacus.com> 
     * @static 
     * @access  public 
     * @return  string  The absolute URI.
     * @param   string  $url Absolute or relative URI the redirect should go to.
     * @param   string  $protocol Protocol to use when redirecting URIs.
     * @param   integer $port A new port number.
     */
    function absoluteURI($url = null, $protocol = null, $port = null)
    {
        // filter CR/LF
        $url = str_replace(array("\r", "\n"), ' ', $url);
        
        // Mess around with already absolute URIs
        if (preg_match('!^([a-z0-9]+)://!i', $url)) {
            if (empty($protocol) && empty($port)) {
                return $url;
            }
            if (!empty($protocol)) {
                $url = $protocol .':'. end($array = explode(':', $url, 2));
            }
            if (!empty($port)) {
                $url = preg_replace('!^(([a-z0-9]+)://[^/:]+)(:[\d]+)?!i', 
                    '\1:'. $port, $url);
            }
            return $url;
        }
            
        $host = 'localhost';
        if (!empty($_SERVER['HTTP_HOST'])) {
            list($host) = explode(':', $_SERVER['HTTP_HOST']);
        } elseif (!empty($_SERVER['SERVER_NAME'])) {
            list($host) = explode(':', $_SERVER['SERVER_NAME']);
        }

        if (empty($protocol)) {
            if (isset($_SERVER['HTTPS']) && !strcasecmp($_SERVER['HTTPS'], 'on')) {
                $protocol = 'https';
            } else {
                $protocol = 'http';
            }
            if (!isset($port) || $port != intval($port)) {
                $port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : 80;
            }
        }
        
        if ($protocol == 'http' && $port == 80) {
            unset($port);
        }
        if ($protocol == 'https' && $port == 443) {
            unset($port);
        }

        $server = $protocol .'://'. $host . (isset($port) ? ':'. $port : '');
        
        if (!strlen($url)) {
            $url = isset($_SERVER['REQUEST_URI']) ? 
                $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF'];
        }
        
        if ($url{0} == '/') {
            return $server . $url;
        }
        
        // Check for PATH_INFO
        if (isset($_SERVER['PATH_INFO']) && strlen($_SERVER['PATH_INFO']) && 
                $_SERVER['PHP_SELF'] != $_SERVER['PATH_INFO']) {
            $path = dirname(substr($_SERVER['PHP_SELF'], 0, -strlen($_SERVER['PATH_INFO'])));
        } else {
            $path = dirname($_SERVER['PHP_SELF']);
        }
        
        if (substr($path = strtr($path, '\\', '/'), -1) != '/') {
            $path .= '/';
        }
        
        return $server . $path . $url;
    }

    /**
     * Raise Error
     * 
     * @static 
     * @access  protected 
     * @return  Error
     * @param   mixed   $error 
     * @param   int     $code 
     */
    function raiseError($error = null, $code = null) {
      return new Error($error);
    } // raiseError
}

?>
