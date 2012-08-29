<?php

  /**
   * Page controller class
   * 
   * Page controller is special controller that is able to map controller name 
   * and actions name with layout and template and automaticly display them. 
   * This behaviour is present only when action has not provided any exit by 
   * itself
   * 
   * @package angie.library.controller
   */
  class PageController extends Controller {
    
    /**
     * Request object
     *
     * @var Request
     */
    var $request;
  
    /**
     * Template name. If it is empty this controller will use action name.php
     *
     * @var string
     */
    var $template;
    
    /**
     * Layout name. If it is empty this controller will use its name.php
     *
     * @var string
     */
    var $layout;
    
    /**
     * Do not render the layout, render only content
     *
     * @var boolean
     */
    var $skip_layout = false;
    
    /**
     * Automaticly render template / layout if action ends without exit
     *
     * @var boolean
     */
    var $auto_render = true;
    
    /**
     * Array of method names that are available through API
     *
     * @var array
     */
    var $api_actions = array();
    
    /**
     * Construct controller
     *
     * @param Request $request
     * @return null
     */
    function __construct($request) {
      parent::__construct();
      
      $this->skip_layout = (boolean) $request->get('skip_layout');
      
      require_once ANGIE_PATH . '/classes/PageConstruction.class.php';
      
      $this->setRequest($request);
      $this->setSystemControllerClass('PageController');
      tpl_assign('request', $request);
      
      // Make sure we output Unicode, important for AJAX requests
      header('Content-Type: text/html; charset=utf-8');
    } // __construct
    
    /**
     * Execute action
     *
     * @param string $action
     * @return null
     */
    function execute($action) {
      if($this->request->isApiCall() && !in_array($this->request->getAction(), $this->api_actions)) {
        $this->httpError(HTTP_ERR_FORBIDDEN, null, true, true);
      } // if
      
      $execute = parent::execute($action);
      if(is_error($execute)) {
        return $execute;
      } // if
      
      if($this->getAutoRender()) {
        $render = $this->render();
        if(is_error($render)) {
          return $render;
        } // if
      } // if
      
      return true;
    } // execute
    
    /**
     * Render content... If template and/layout are NULL script will resolve 
     * their names based on controller name and action. 
     * 
     * PageController::index will map with:
     *  - template => views/page/index.php
     *  - layout => layouts/page.php
     *
     * @param string $template
     * @param string $layout
     * @param boolean $die
     * @return boolean
     * @throws FileDnxError
     */
    function render($template = null, $layout = null, $die = true) {
      if(!is_null($template)) {
        $this->setTemplate($template);
      } // if
      
      $template_path = $this->getTemplatePath();
      if(is_error($template_path)) {
        return $template_path;
      } // if
      
      if(!$this->skip_layout) {
        if(!is_null($layout)) {
          $this->setLayout($layout);
        } // if
        
        $layout_path = $this->getLayoutPath();
        if(is_error($layout_path)) {
          return $layout_path;
        } // if
      } // if
      
      $content = tpl_fetch($template_path);
      
      if($this->skip_layout) {
        print $content;
        if($die) {
          die();
        } // if
      } else {
        $this->renderLayout($layout_path, $content, $die);
      } // if
      
      return true;
    } // render
    
    /**
     * Assign content and render layout
     *
     * @param string $layout_path Path to the layout file
     * @param string $content Value that will be assigned to the $content_for_layout
     *   variable
     * @return boolean
     * @throws FileDnxError
     */
    function renderLayout($layout_path, $content = null, $die = false) {
      tpl_assign('content_for_layout', $content);
      $display = tpl_display($layout_path);
      
      if($die) {
        die();
      } // if
      
      return $display;
    } // renderLayout
    
    /**
     * Shortcut method for printing text and setting auto_render option
     *
     * @param string $text Text that need to be rendered
     * @param boolean $render_layout Render controller layout. Default is false for
     *   simple and fast text rendering
     * @param boolean $die
     * @return null
     */
    function renderText($text, $render_layout = false, $die = true) {
      if($render_layout) {
        $this->setAutoRender(false);
        
        $layout_path = $this->getLayoutPath();
        if(is_error($layout_path)) {
          return $layout_path;
        } // if
        
        $this->renderLayout($layout_path, $text, $die);
      } else {
        print $text;
      } // if
      
      if($die) {
        die();
      } // if
    } // renderText
    
    // ---------------------------------------------------
    //  Redirects
    // ---------------------------------------------------
    
    /**
     * Redirect to specific route
     * 
     * Params of this function will be used to assemble URL (using assemble_url 
     * function)
     *
     * @param string $route_name
     * @param array $params
     * @param array $options
     * @return null
     */
    function redirectTo($route_name, $params = null, $options = null) {
      redirect_to(assemble_url($route_name, $params, $options));
    } // redirectTo
    
    /**
     * Redirect to URL
     *
     * @param string $url
     * @return null
     */
    function redirectToUrl($url) {
      redirect_to($url);
    } // redirectToUrl
    
    /**
     * Redirect to referer
     *
     * @param string $alternative
     * @return null
     */
    function redirectToReferer($alternative) {
      redirect_to_referer($alternative);
    } // redirectToReferer
    
    // ---------------------------------------------------
    //  Server request
    // ---------------------------------------------------
    
    /**
     * Serve data to the client
     *
     * @param mixed $data
     * @param string $as
     * @param string $format
     * @param boolean $die
     * @return null
     */
    function serveData($data, $as = null, $format = null, $die = true) {
      if($format === null) {
        $format = $this->request->getFormat();
      } // if
      
      switch($format) {
        case FORMAT_JSON:
          header('Content-Type: application/json; charset=utf-8');
          print do_json_encode($data, $as);
          break;
        case FORMAT_XML:
          header('Content-Type: application/xml; charset=utf-8');
          print do_xml_encode($data, $as);
          break;
        default:
          print $data;
      } // switch
      
      if($die) {
        die();
      } // if
    } // serveData
    
    // ---------------------------------------------------
    //  HTTP errors
    // ---------------------------------------------------
    
    /**
     * Send OK to the browser
     * 
     * If $message is NULL, only headers will be sent without any output being 
     * rendered
     *
     * @param boolean $message
     * @param boolean $die
     * @return null
     */
    function httpOk($message = null, $die = true) {
      header("HTTP/1.1 200 OK");
      if($message) {
        print '<h1>' . clean($message) . '</h1>';
      } // if
      
      if($die) {
        die();
      } // if
    } // httpOk
    
    /**
     * Serve HTTP error
     *
     * @param integer $code
     * @param string $message
     * @param boolean die
     * @param boolean $only_headers
     * @return null
     */
    function httpError($code, $message = null, $die = true, $only_headers = false) {
      if($message === null) {
        $errors = array(
          100 => "HTTP/1.1 100 Continue",
          101 => "HTTP/1.1 101 Switching Protocols",
          200 => "HTTP/1.1 200 OK",
          201 => "HTTP/1.1 201 Created",
          202 => "HTTP/1.1 202 Accepted",
          203 => "HTTP/1.1 203 Non-Authoritative Information",
          204 => "HTTP/1.1 204 No Content",
          205 => "HTTP/1.1 205 Reset Content",
          206 => "HTTP/1.1 206 Partial Content",
          300 => "HTTP/1.1 300 Multiple Choices",
          301 => "HTTP/1.1 301 Moved Permanently",
          302 => "HTTP/1.1 302 Found",
          303 => "HTTP/1.1 303 See Other",
          304 => "HTTP/1.1 304 Not Modified",
          305 => "HTTP/1.1 305 Use Proxy",
          307 => "HTTP/1.1 307 Temporary Redirect",
          400 => "HTTP/1.1 400 Bad Request",
          401 => "HTTP/1.1 401 Unauthorized",
          402 => "HTTP/1.1 402 Payment Required",
          403 => "HTTP/1.1 403 Forbidden",
          404 => "HTTP/1.1 404 Not Found",
          405 => "HTTP/1.1 405 Method Not Allowed",
          406 => "HTTP/1.1 406 Not Acceptable",
          407 => "HTTP/1.1 407 Proxy Authentication Required",
          408 => "HTTP/1.1 408 Request Time-out",
          409 => "HTTP/1.1 409 Conflict",
          410 => "HTTP/1.1 410 Gone",
          411 => "HTTP/1.1 411 Length Required",
          412 => "HTTP/1.1 412 Precondition Failed",
          413 => "HTTP/1.1 413 Request Entity Too Large",
          414 => "HTTP/1.1 414 Request-URI Too Large",
          415 => "HTTP/1.1 415 Unsupported Media Type",
          416 => "HTTP/1.1 416 Requested range not satisfiable",
          417 => "HTTP/1.1 417 Expectation Failed",
          500 => "HTTP/1.1 500 Internal Server Error",
          501 => "HTTP/1.1 501 Not Implemented",
          502 => "HTTP/1.1 502 Bad Gateway",
          503 => "HTTP/1.1 503 Service Unavailable",
          504 => "HTTP/1.1 504 Gateway Time-out" 
        );
        
        $message = array_var($errors, $code);
        if(trim($message) == '') {
          $message = 'Unknown';
        } // if
      } // if
      
      header("HTTP/1.1 $code $message");
      print '<h1>' . clean($message) . '</h1>';
      
      if($die) {
        die();
      } // if
    } // httpError
    
    // -------------------------------------------------------
    // Getters and setters
    // -------------------------------------------------------
    
    /**
     * Get request
     *
     * @param null
     * @return Request
     */
    function getRequest() {
      return $this->request;
    } // getRequest
    
    /**
     * Set request value
     *
     * @param Request $value
     * @return null
     */
    function setRequest($value) {
      $this->request = $value;
    } // setRequest
    
    /**
     * Get template
     *
     * @param null
     * @return string
     */
    function getTemplate() {
      return $this->template;
    } // getTemplate
    
    /**
     * Set template value
     * 
     * $value can be string or associative array with following fields:
     * 
     * - template - template name, without extension
     * - controller - controller name
     * - module - module name
     *
     * @param string $value
     * @return null
     */
    function setTemplate($value) {
      $this->template = $value;
    } // setTemplate
    
    /**
     * Get layout
     *
     * @param null
     * @return string
     */
    function getLayout() {
      return $this->layout;
    } // getLayout
    
    /**
     * Set layout value
     *
     * @param string $value
     * @return null
     */
    function setLayout($value) {
      $this->layout = $value;
    } // setLayout
    
    /**
     * Get auto_render
     *
     * @param null
     * @return boolean
     */
    function getAutoRender() {
      return $this->auto_render;
    } // getAutoRender
    
    /**
     * Set auto_render value
     *
     * @param boolean $value
     * @return null
     */
    function setAutoRender($value) {
      $this->auto_render = (boolean) $value;
    } // setAutoRender
    
    /**
     * Return path of the template. If template dnx throw exception
     *
     * @param void
     * @return string
     * @throws FileDnxError
     */
    function getTemplatePath() {
      $module = $this->request->getModule();
      
      if(is_array($this->getTemplate())) {
        $template   = array_var($this->getTemplate(), 'template');
        $controller = array_var($this->getTemplate(), 'controller');
        $module     = array_var($this->getTemplate(), 'module', $this->request->getModule());
      } elseif(is_string($this->getTemplate())) {
        $template   = $this->getTemplate();
        $controller = $this->getControllerName();
      } else {
        $template   = $this->request->getAction();
        $controller = $this->getControllerName();
      } // if
      
      $path = get_template_path($template, $controller, $module);
      return is_file($path) ? $path : new FileDnxError($path);
    } // getTemplatePath
    
    /**
     * Return path of the layout file. File dnx throw exception
     *
     * @param void
     * @return string
     * @throws FileDnxError
     */
    function getLayoutPath() {
      $layout = $this->getLayout();
      if(is_array($layout)) {
        $path = get_layout_path($layout['layout'], $layout['module']);
      } elseif($layout) {
        $path = get_layout_path($layout, $this->request->getModule());
      } else {
        $path = get_layout_path($this->getControllerName(), $this->request->getModule());
      } // if
      
      return is_file($path) ? $path : new FileDnxError($path);
    } // getLayoutPath
  
  } // PageController

?>