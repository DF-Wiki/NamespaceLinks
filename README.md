mw-namespace-links
==================

MediaWiki extension allowing default link namespaces to be changed

Essentially, links in a certain namespace without a namespace explicitly specified (e.g. `[[linkText]]` or `[[linkTitle|linkText]]`) can be changed to link to a different namespace ( __without__ using the prefix or pipe trick). 

Example:
```
User=User
*User talk=User
```
would cause all links in the `User` and `User talk` namespaces without a specified namespace to link to the `User` namespace.

* Note that the `*`'s are optional, but they are recommended to improve readability when rendered by MediaWiki (similar to other system messages, such as MediaWiki:Sidebar).
