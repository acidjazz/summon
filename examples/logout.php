<?

/*
  sample code to:
  - remove our hash/encoded from the summon array in the user model
  - remove our summon cookie
*/

$user->summon = Summon\Summon::remove($user->summon);
$user->save();

