<?

require_once 'lib/summon.class.php';

/*
 sample code to:
  - log the user in
  - set a summon cookie
  - store that hash/encoded string in a summon array in the user model
*/

// refresh our user session data
$_SESSION['user'] = $user->data();

// set our new hash and store it
$user->summon = summon::set($user->id, $this->summon);
$user->save();

