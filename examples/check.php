<?

/* 
  sample code to: 
   - check for an expired session 
   - verify our cookies' validity
   - extract our cookies payload
   - verify once more in the DB 
   - log back in
*/

if (!isset($_SESSION['user']) && ($payload = Summon\Summon::check())) {

  $user = new user($payload['user_id']);
  if ($user->exists() && isset($user->summon[$payload['hash']])) {
    $user->login();
  }

}


