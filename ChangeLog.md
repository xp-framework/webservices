Webservices for XP Framework ChangeLog
========================================================================

## ?.?.? / ????-??-??

## 6.2.0 / 2014-12-09

* Rewrote code to ue `literal()` instead of `xp::reflect()`. See
  xp-framework/rfc#298
  (@thekid)
* Fixed JsonClient and JsonRpcHttpTransport which were broken on
  namespace conversion
  (@kiesel)

## 6.1.1 / 2015-07-12

* Added forward compatibility with XP 6.4.0 - @thekid

## 6.1.0 / 2015-06-13

* Added forward compatibility with PHP7 - @thekid
* Made tests pass on HHVM 3.5 - @thekid
* Adjusted to changes in scriptlet library - @thekid

## 6.0.1 / 2015-02-12

* Changed dependency to use XP ~6.0 (instead of dev-master) - @thekid

## 6.0.0 / 2015-01-10

* Rewrite `XmlRpcDecoder` and `XmlRpcEncoder` to use reflection and
  instead of using array-cast trick and then accessing mangled names.
  (@thekid)
* Heads up: Converted classes to PHP 5.3 namespaces - (@thekid)
