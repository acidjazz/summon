<?php

/* 
 * minimal 'remember me' functionality
 *
 * @author kevin olson (acidjazz@gmail.com)
 *
*/

namespace Summon;

class Summon {

  const method = 'AES-256-CBC'; // our cipher method
  const iv = '1234567812345678'; // initialization vector
  const expire = 60; // cookie/token expiration in days
  const cookie = 'token'; // name of our cookie

  public static $verifyAgent = true; // verify our agent

  // change this to something pulled in via local configuration
  public static $secret = '31337';


  /* 
   * create our hash, set it as a cookie and return our hash and encoded string 
   *
   * @param string $user_id - a user id of some sort to store in our payload
   * @param array $summons - our array of hash=>strings
   * @param stdClass $browser - a laravel dusk browser instance to support unit testing
   * 
   */
  public static function set($user_id, $summons, $browser=false) {

    list($hash, $token, $payload) = self::encrypt($user_id);
    $expires = self::expire();

    if ($browser !== false) {
      $browser->plainCookie(self::cookie, $token, $expires);
    } else {
      setcookie(self::cookie, $token, $expires, '/');
    }

    $summons[$hash] = $payload;

    return [
      'token' => $token,
      'expires' =>  $expires,
      'sessions' => $summons
    ];

  }

  /* 
   * check for an existing cookie and verify its validity
   * @param $token - to allow manual token checking
   * run this when your session has expired and you want to re-login
   */
  public static function check($token=false) {

    if (!isset($_COOKIE[self::cookie]) && !isset($_REQUEST[self::cookie])) {
      return false;
    }

    if ($token === false) {
      $token = isset($_COOKIE[self::cookie]) ? $_COOKIE[self::cookie] : $_REQUEST[self::cookie];
    }

    $token = str_replace(' ', '+', $token);

    if (!$payload = self::decrypt($token)) {
      return false;
    }

    // verify expiration 
    if (!isset($payload['expires']) || time() > $payload['expires']) {
      return false;
    }

    // verify our agent 
    if (self::$verifyAgent) {
      if (!isset($payload['agent']) || $payload['agent'] != $_SERVER['HTTP_USER_AGENT']) {
        return false;
      }
    }

    return $payload;

  }

  /*
   * remove a hash/string pair from our array and remove the corresponding cookie 
   * returns the passed array of summons w/ that one removed
   * 
   * - run this when logging out
   *
   * @param array $sessions - our array of hash=>strings
   * @param stdClass $browser - a browser instance to support unit testing
   *
   */

  public static function remove($sessions,$browser=false) {

    if (is_array($sessions)) {
      foreach ($sessions as $key=>$session) {
        list($hash, $token, $payload) = self::encrypt($session['user_id'], $session);

        if ($browser !== false) {

          if ($token === $browser->plainCookie(self::cookie)) {
            unset($sessions[$key]);
          }

        } else {

          if ($token === $_COOKIE[self::cookie]) {
            unset($sessions[$key]);
          }

        }

      }
    }

    if ($browser !== false) {
      $browser->deleteCookie(self::cookie);
    } else {
      setcookie(self::cookie, false, time()-3600, '/');
    }

    return $sessions;

  }

  /*
   * clean up a list of expired payloads, this helps clean the paylaod object stored with your user document/table
   *
   * @param array $sessions - our array of hash=>strings
   *
   */

  public static function clean($sessions) {

    if (is_array($sessions)) {
      foreach ($sessions as $hash=>$payload) {
        if (!is_array($payload) && is_string($payload)) {
          $payload = self::decrypt($payload);
        }
        $days = ($payload['expires'] -  time())/60/60/24;
        if ($days > $payload['expires']) {
          unset($sessions[$hash]);
        }
      }
    }

    return $sessions;

  }

  /* encrypts and returns our encoded string and hash */
  public static function encrypt($user_id, $payload=false) {

    $hash = md5(self::seed());

    if ($payload === false) {
      $payload = [
        'expires' => self::expire(),
        'agent' => (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : false),
        'ip_address' => (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false),
        'user_id' => $user_id,
        'hash' => $hash
      ];
    }
    
    $token = openssl_encrypt(json_encode($payload), self::method, self::$secret, false, self::iv);

    return [$hash, $token, $payload];

  }

  /* decrypts our encoded string */
  public static function decrypt($hash) {
    if (!$json = openssl_decrypt($hash, self::method, self::$secret, false, self::iv)) {
      return false;
    }


    return json_decode($json, true);

  }


  // expiration time in seconds 
  private static function expire() {
    return time()+60*60*24*self::expire;
  }

  // i konw /dev/urandom is sweeter but whatever. chill out aspies.
  public static function seed() {
   list($usec, $sec) = explode(' ', microtime());
   mt_srand((float) $sec + ((float) $usec * 100000));
   return mt_rand();
  }

}
