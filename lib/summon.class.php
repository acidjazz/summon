<?

/* 
 * minimal 'remember me' functionality
 *
 * @author kevin olson (acidjazz@gmail.com)
 *
*/

class summon {

  const method = 'AES-256-CBC'; // our cipher method
  const iv = '1234567812345678'; // initialization vector
  const secret = SUMMON_SECRET; // password (keep this protected)
  const expire = 60; // cookie expiration in days
  const cookie = 'summon'; // name of our cookie
  const VERIFY_AGENT = true; // verify our agent


  /* 
   * create our hash, set it as a cookie and return our hash and encoded string 
   *
   * @param string $user_id - a user id of some sort to store in our payload
   * @param array $summons - our array of hash=>strings
   * 
   */
  public static function set($user_id, $summons) {
    list($hash, $encoded) = self::encrypt($user_id);
    setcookie(self::cookie, $encoded, self::expire(), '/');
    $summons[$hash] = $encoded;
    return $summons;
  }

  /* check for an existing cookie and verify its validity
   *
   * - run this when your session has expired and you want to re-login
   */
  public static function check() {

    if (!isset($_COOKIE[self::cookie])) {
      return false;
    }

    if (!$json = self::decrypt($_COOKIE[self::cookie])) {
      return false;
    }

    $payload = json_decode($json, true);

    // verify expiration 
    if (!isset($payload['expire']) || time() > $payload['expire']) {
      return false;
    }

    // verify our agent 
    if (self::VERIFY_AGENT) {
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
   * @param array $summons - our array of hash=>strings
   *
   */
  public static function remove($summons) {

    if (is_array($summons) && in_array($_COOKIE[self::cookie], $summons)) {
      $summons = array_diff($summons, array($_COOKIE[self::cookie]));
    }

    unset($_COOKIE[self::cookie]);
    return $summons;

  }

  /* encrypts and returns our encoded string and hash */
  private static function encrypt($user_id) {

    $hash = md5(self::seed());

    $payload = json_encode(array(
      'expire' => self::expire(),
      'agent' => $_SERVER['HTTP_USER_AGENT'],
      'user_id' => $user_id,
      'hash' => $hash
    ));
    
    $encoded = openssl_encrypt($payload, self::method, self::secret, false, self::iv);

    return array($hash, $encoded);

  }

  /* decrypts our encoded string */
  private static function decrypt($hash) {
    return openssl_decrypt($hash, self::method, self::secret, false, self::iv);
  }


  // expiration time in seconds 
  private static function expire() {
    return time()+60*60*24*self::expire;
  }

  // i konw /dev/urandom is sweeter but whatever. chill out aspies.
  private function seed() {
   list($usec, $sec) = explode(' ', microtime());
   mt_srand((float) $sec + ((float) $usec * 100000));
   return mt_rand();
  }

}
