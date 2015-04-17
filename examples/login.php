<?

/*
 * sample code to:
 *  - log the user in
 *  - set a session cookie
 *  - store that hash/encoded string in a Summon array in the user model

 * $results is an assoc array of 
 * - 'token' set as a cookie (default named token)
 * - 'expires' when this session expires
 * - 'sessions' an update list of all the users sessions to store in the DB
*/

$results = Summon\Summon::set($user->id(true), $user->sessions);
$user->sessions = $results['sessions'];
$user->save();

