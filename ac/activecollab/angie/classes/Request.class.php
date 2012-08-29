<?php

  /**
   * User request
   *
   * This class is used for request handling - extraction of request parameters, 
   * input filtering and cleaning, work with data from $_SERVER variable etc
   */
  class Request extends AngieObject {
    
    /**
     * Name of the route that produced this request object
     *
     * @var string
     */
    var $matched_route;
  
    /**
     * Array of parameters extracted from URL
     *
     * @var array
     */
    var $url_params;
    
    /**
     * Requested response format
     *
     * @var string
     */
    var $format = false;

    /**
     * Construct request object
     *
     * @param string $matched_route
     * @param array $url_params
     * @return Request
     */
    function __construct($matched_route, $url_params) {
      $this->matched_route = $matched_route;
      $this->url_params = $url_params;
      
      $_GET = array();
      if(is_foreachable($url_params)) {
        foreach($url_params as $k => $v) {
          if($k != 'controller' && $k != 'action') {
            $_GET[$k] = $v;
          } // if
        } // foreach
      } // if
    } // __construct
    
    /**
     * Return name of the module that needs to serve this request
     *
     * @param void
     * @return string
     */
    function getModule() {
      return array_var($this->url_params, 'module', DEFAULT_MODULE);
    } // getModule
    
    /**
     * Return requested controller
     *
     * @param void
     * @return string
     */
    function getController() {
      return array_var($this->url_params, 'controller', DEFAULT_CONTROLLER);
    } // getController
    
    /**
     * Return requested action
     *
     * @param void
     * @return string
     */
    function getAction() {
      return array_var($this->url_params, 'action', DEFAULT_ACTION);
    } // getAction
    
    /**
     * Return requested output format
     *
     * @param void
     * @return string
     */
    function getFormat() {
      if($this->format === false) {
        if(isset($this->url_params['format'])) {
          $this->format = $this->url_params['format'];
        } else {
          $accept = strtolower(array_var($_SERVER, 'HTTP_ACCEPT'));
          if($accept == 'application/json') {
            $this->format = FORMAT_JSON;
          } elseif($accept == 'application/xml') {
            $this->format = FORMAT_XML;
          } // if
        } // if
        
        if(empty($this->format)) {
          $this->format = defined('ANGIE_API_CALL') && ANGIE_API_CALL ? FORMAT_XML : DEFAULT_FORMAT; // Force API call?
        } // if
      } // if
      
      return $this->format;
    } // getFormat
    
    /**
     * Return variable from GET
     *
     * @param string $var
     * @param mixed $default
     * @return mixed
     */
    function get($var, $dafault = null) {
      if($var == 'module') {
        return $this->getModule();
      } // if
      if($var == 'controller') {
        return $this->getController();
      } // if
      if($var == 'action') {
        return $this->getAction();
      } // if
      return array_var($_GET, $var, $dafault);
    } // get
    
    /**
     * Return POST variable
     *
     * @param string $var
     * @param mixed $default
     * @return mixed
     */
    function post($var, $default = null) {
      return array_var($_POST, $var, $default);
    } // post
    
    /**
     * Returns true if this request is submitted through POST and submitted 
     * variable is set to submitted
     *
     * @param void
     * @return boolean
     */
    function isSubmitted() {
      return $this->post('submitted') == 'submitted';
    } // isSubmitted
    
    /**
     * Returns true if this request is API call
     *
     * @param void
     * @return boolean
     */
    function isApiCall() {
      return (defined('ANGIE_API_CALL') && ANGIE_API_CALL) || ($this->getFormat() != FORMAT_HTML);
    } // isApiCall
    
    /**
     * Returns true if this request is marked as async call
     *
     * @param void
     * @return boolean
     */
    function isAsyncCall() {
      return (boolean) $this->get('async');
    } // isAsyncCall
    
    /**
     * Return ID
     * 
     * This function will extract ID value from request. If $from is NULL get 
     * will be used, else it will be extracted from $from. Default value is 
     * returned if ID is missing
     *
     * @param string $name
     * @param array $from
     * @param mixed $default
     * @return integer
     */
    function getId($name = 'id', $from = null, $default = null) {
      if($from === null) {
        return (integer) $this->get($name, $default);
      } else {
        return (integer) array_var($from, $name, $default);
      } // if
    } // getId
    
    /**
     * Return page number
     *
     * @param string $variable_name
     * @return integer
     */
    function getPage($variable_name = 'page') {
      $page = (integer) $this->get($variable_name);
      return $page < 1 ? 1 : $page;
    } // getPage
    
    // ---------------------------------------------------
    //  Getters and setters
    // ---------------------------------------------------
    
    /**
     * Get url_params
     *
     * @param null
     * @return array
     */
    function getUrlParams() {
      return $this->url_params;
    } // getUrlParams
    
    /**
     * Set url_params value
     *
     * @param array $value
     * @return null
     */
    function setUrlParams($value) {
      $this->url_params = $value;
    } // setUrlParams
  
  } // Request

?>