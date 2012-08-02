<?

require_once 'lib/summon.class.php';

/*
  sample code to:
  - remove our hash/encoded from the summon array in the user model
  - remove our summon cookie
  - kill our session
*/

$user = new user($_SESSION['user']['id']);
$user->summon = summon::remove($user->summon);
$user->save();
unset($_SESSION['user']);

