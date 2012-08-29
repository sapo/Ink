<?php

  /**
   * Init feed library
   * 
   * @package angie.library.feed
   */
  
  define('FEED_LIB_PATH', ANGIE_PATH . '/classes/feed');
  
  require_once FEED_LIB_PATH . '/Feed.class.php';
  require_once FEED_LIB_PATH . '/FeedItem.class.php';
  require_once FEED_LIB_PATH . '/FeedAuthor.class.php';
  
  // ---------------------------------------------------
  //  Methods
  // ---------------------------------------------------
  
  /**
   * Render a sepecific feed instance
   *
   * @param Feed $feed
   * @param Smarty $smarty
   * @return null
   */
  function render_rss_feed($feed, $header = true) {
    if($header) {
      header('Content-Type: text/xml; charset=utf-8');
    } // if
    
    $result  = "<rss version=\"2.0\">\n<channel>\n";
    $result .= '<title>' . clean($feed->getTitle()) . "</title>\n";
    $result .= '<link>' . clean($feed->getLink()) . "</link>\n";
    if($description = trim($feed->getDescription())) {
      $result .= '<description><![CDATA[' . clean($description) . "]]></description>\n";
    } // if
    if($language = trim($feed->getLanguage())) {
      $result .= '<language>' . clean($language) . "</language>\n";
    } // if
    
    foreach($feed->getItems() as $item) {
      $result .= "<item>\n";
      $result .= '<title>' . clean($item->getTitle()) . "</title>\n";
      $result .= '<link>' . clean($item->getLink()) . "</link>\n";
      if($description = trim($item->getDescription())) {
        $result .= '<description><![CDATA[' . $description . "]]></description>\n";
      } // if
      
      $author = $item->getAuthor();
      if(instance_of($author, 'FeedAuthor')) {
        $result .= '<author>' . clean($author->getEmail()) . ' (' . clean($author->getName()) . ")</author>\n";
      } // if
      
      $pubdate = $item->getPublicationDate();
      if(instance_of($pubdate, 'DateValue')) {
        $result .= '<pubDate>' . $pubdate->toRSS() . "</pubDate>\n";
      } // if
      
      $id = $item->getId();
      if($id) {
        $result .= '<guid>' . clean($id) . "</guid>\n";
      } // if
      
      $result .= "</item>\n";
    } // foreach
    
    $result .= "</channel>\n</rss>";
    return $result;
  } // render_rss_feed

?>