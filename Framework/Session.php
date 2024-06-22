<?php

namespace Framework;

class Session
{
  /**
   * Start a session
   * 
   * @return void 
   */
  public static function start()
  {
    // make sure there's no current session
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
  }

  /**
   * Set a session key/value pair
   * 
   * @param string $key
   * @param mixed $value
   * @return void 
   */
  public static function set($key, $value)
  {
    $_SESSION[$key] = $value;
  }

  /**
   * Get a session value from the key
   * 
   * @param string $key
   * @param mixed $default
   * @return mixed 
   */
  public static function get($key, $default = null)
  {
    return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
  }

  /**
   * Check if a session key exists
   * 
   * @param string $key
   * @return bool 
   */
  public static function has($key)
  {
    return isset($_SESSION[$key]);
  }

  /**
   * Clear a session by key
   * 
   * @param string $key
   * @return void 
   */
  public static function clear($key)
  {
    if (isset($_SESSION[$key])) {
      unset($_SESSION[$key]);
    }
  }

  /**
   * Clear all session data
   * 
   * @return void 
   */
  public static function clearAll()
  {
    session_unset();
    session_destroy();
  }

  /**
   * Set session flash message
   * 
   * @param string $key
   * @param string $message
   * @return void 
   */
  public static function setFlashMessage($key, $message)
  {
    self::set($key, $message);
  }

  /**
   * Get and unset flash message
   * 
   * @param string $key
   * @param mixed $default
   * @return string 
   */
  public static function getFlashMessage($key, $default = null)
  {
    $message = self::get($key, $default);
    self::clear($key);
    return $message;
  }
}
