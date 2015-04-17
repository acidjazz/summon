# Summon , Simple Secure Sessioning
[![Gitter](https://badges.gitter.im/Join Chat.svg)](https://gitter.im/acidjazz/summon?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

Secure php "remember me" sessioning methodology 

## what is this 
this is just a simple secure way to set cookies and revive expired sessions for as long as you want.  it also allows you to view and control all logged in sessions of a user and where they are coming from.

## how it works
* sets a cookie of an encoded string of some data when the user logs in.
* stores stuff in the user model for better verification
* upon our normal session expiring, allows you to re-login the user

## features
* multiple browser/client support
  * monitor and control mutiple sessions
* multiple level verification
  * verify cookie expiration
  * verify browser agent (optional)
  * store/verify our hash at the DB level
* non-expensive DB lookup
  * store an indexable identifier to avoid an expensive user lookup

## examples

Log a user in after, assuming $user is some sort of user model :

```php
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
```

Check if a user is logged in:

```php
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
```

Remove a session, logout a user

```php
<?php

$user->summon = Summon\Summon::remove($user->summon);
$user->save();
```


### installation
1. modify your user table/collection to allow a small object of hash=>string
2. store the results of summon::set() in your user model (check login.php)
3. add code to verify expired sessions w/ a potential re-login (check check.php)
4. add code at your logout area to remove expired hash=>strings from your user model ( check logout.php )
5. add a define "SUMMON_SECRET" with the value of a unique hash/string and keep it safe





### TODO
* remove expired/invalid summons upon check
* for dynamic timeouts based on agent/etc .. for reasons like tablets/phones to have a shorter expiration
* support for more parameters for hte payload for db/index purposes

### why?
I've spent hours googling this methodology enough to predict something like this needs to exist.  Please if you have any comments/ideas/features let me know or even better fork this and submit pull requests.
