<?

require_once 'lib/summon.class.php';

/* 
  sample code to: 
   - check for an expired session 
   - verify our cookies' validity
   - extract our cookies payload
   - verify once more in the DB 
   - log back in
*/

if (!isset($_SESSION['user']) && ($payload = summon::check())) {

  $user = new user($payload['user_id']);
  if ($user->exists() && isset($user->summon[$payload['hash']])) {
    $user->login();
  }

}


