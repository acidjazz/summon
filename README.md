## summon ##

secure php "remember me" methodology

### features
 
* multiple level verification
  * verify cookie expiration
  * verify browser agent (optional)
  * store/verify our hash at the DB level

* multiple browser/client/etc support
  * monitor and control mutiple sessions

* non-expensive DB lookup
  * store an indexable identifier to avoid an expensive user lookup


