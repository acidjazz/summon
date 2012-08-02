## summon ##

secure php "remember me" methodology

## what is this 
this is just a simple secure way to add "remember me" functionality to your site.  

## how it works
* sets a cookie of an encoded string of your user data when the user logs in.
* stores that data in the user model (security)
* upon our normal session expiring, allows you to re-login the user

### features
 
* multiple level verification
  * verify cookie expiration
  * verify browser agent (optional)
  * store/verify our hash at the DB level

* multiple browser/client/etc support
  * monitor and control mutiple sessions

* non-expensive DB lookup
  * store an indexable identifier to avoid an expensive user lookup

### installation

1. modify your user table/collection/etc to allow a small object of hash=>string
2. store the results of summon::set() in your user model (check login.php)
3. add code to verify expired sessions w/ a potential re-login (check check.php)
4. add code at your logout area to remove expired hash=>strings from your user model ( check logout.php )
