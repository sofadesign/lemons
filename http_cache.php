<?php
/**
 * Http Cache Functions
 * Uses built in HTTP 1.1 caching mechanism
 * (304 Not Modified)
 * 
 * A procedural implementation of HttpCacheComponent for CakePHP
 *
 * See {@link http://github.com/sofadesign/vincent-helye.com} for real case example.
 * 
 * @author RosSoft
 * @author Fabrice Luraine
 * @link http://rossoft.wordpress.com/2006/06/16/http-cache-component/
 * @link http://blog.codahale.com/2006/05/23/rails-plugin-http_caching/
 *
 */

/**
 * If the browser cache has a timestamp newer than the
 * modification date of the content, then a response of
 * not-modified is sent (and exits).
 *
 * @param integer $timestamp The modification date of the content (Unix timestamp)
 * @param boolean $no_cache If true, then it stores cache to user always
 *
 */   
function http_cache($timestamp,$no_cache=false)
{
  _http_cache_control();
  $modified_since = $_SERVER['HTTP_IF_MODIFIED_SINCE'];
  if ($_SERVER['HTTP_IF_MODIFIED_SINCE']
  && $timestamp <= strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'])
  && ! $no_cache)
  {
  	header('Last-Modified: '.timestamp_to_gmt($timestamp), true, 304);     
    header("HTTP/1.1 304 Not changed"); 
    exit();
  }
  else
  {
    header('Last-Modified: '.timestamp_to_gmt($timestamp), true, 200);
  }
}

/**
 * Sets an expiration time for the page.
 *
 * @param mixed $expires A strtotime string / timestamp integer: expiration date.
 * @param boolean $no_cache If true, then it stores cache to user always
 */     
function http_cache_expires($expires='+1 Day',$no_cache=false)
{
	_http_cache_control();
  if ($_SERVER['HTTP_IF_MODIFIED_SINCE']
  && time()<= strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'])
  && ! $no_cache)
  {
    header('Last-Modified: '.$_SERVER['HTTP_IF_MODIFIED_SINCE'], true, 304);
    header("HTTP/1.1 304 Not changed");
    exit();
  }   
  else
  {
    header('Last-Modified: '.timestamp_to_gmt($expires), true, 200);
  }
}

/**
 * Timestamp to GMT string   
 *
 * @param mixed $timestamp . A strtotime string / timestamp integer
 * @return string GMT time
 */
function timestamp_to_gmt($timestamp)
{
  if(!is_numeric($timestamp)) $timestamp=strtotime($timestamp);                          
  return gmdate('D, d M Y H:i:s', $timestamp).' GMT';
}   

/**
 * Sets header for cache control
 */   
function _http_cache_control()
{
  header("Cache-Control: Public",true);
  header("Pragma: Public",true);   
}
