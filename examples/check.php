<?

/* 
  sample function to to: 
   - verify our cookies' validity
   - extract our cookies payload
   - verify once more in the DB 
*/

public static function loggedIn() {

  if ($data = Summon\Summon::check()) {

    $user = new DBModelOfSomeSort\user($data['user_id']);

    if ($user->exists() && isset($user->sessions[$data['hash']])) {
      return $user;
    }

  }

  return false;

}



