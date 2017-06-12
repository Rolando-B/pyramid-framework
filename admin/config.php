<?php
define('SITEROOT', (substr($_SERVER['DOCUMENT_ROOT'], -1) != '/') ? $_SERVER['DOCUMENT_ROOT'] .'/' : $_SERVER['DOCUMENT_ROOT'] );
$root_uri = explode("/",$_SERVER['REQUEST_URI']);
define('ROOTURL', (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/');
define('ROOTDIR', $root_uri[1]."/");
define('ABSOLUTEROOT', SITEROOT.ROOTDIR);
define('ABSOLUTEURL', ROOTURL.ROOTDIR);
//DEBUG
/*echo $root_uri[1];
echo "</br>";
echo SITEROOT;
echo "</br>";
echo ROOTURL;
echo "</br>";
echo ROOTDIR;
echo "</br>";
echo ABSOLUTEROOT;
echo "</br>";
echo ABSOLUTEURL;
echo "</br>";*/