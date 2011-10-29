<?php
/*
 * config.inc.php
 * =-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
 * Configuration options for the hhncmanager
 
*/

// error_reporting( E_ALL );

/* Time Zone Setting */
date_default_timezone_set('America/New_York');

/* General Settings */

/* Version Numbers */
$hhnc_ver = array();
$hhnc_ver['name'] = 'HHManager'; 
$hhnc_ver['num'] = '0.5.2';
$hhnc_ver['date'] = '2011-08-23';
$hhnc_ver['developer'] = 'Desmond Smith, Web and Flow Design';
$hhnc_ver['dev_email'] = 'info@webandflowdesign.com';


/* Ordering Settings */
$orderingcloses = 3; // ordering closes on nth day of week



/* Software Name */
if(!defined('HHNC_VER_NAME')) {
    define('HHNC_VER_NAME',$hhnc_ver['name']);
}

/* Version Number */
if(!defined('HHNC_VER_NUM')) {
    define('HHNC_VER_NUM',$hhnc_ver['num']);
}

/* Version Date */
if(!defined('HHNC_VER_DATE')) {
    define('HHNC_VER_DATE',$hhnc_ver['date']);
}

/* Developer Information */
if(!defined('HHNC_VER_DEV')) {
    define('HHNC_VER_DEV',$hhnc_ver['developer']);
}

/* Developer Email */
if(!defined('HHNC_VER_DEV_EMAIL')) {
    define('HHNC_VER_DEV_EMAIL',$hhnc_ver['dev_email']);
}

/* Enrollment Closes X days before season begins */
if(!defined('HHNC_ENR_CLOSE_LIMIT_DAYS')) {
    define('HHNC_ENR_CLOSE_LIMIT_DAYS',2);
}

if(!defined('HHNC_ENR_CLOSE_LIMIT_SECONDS')) {
    $seconds = HHNC_ENR_CLOSE_LIMIT_DAYS*24*60*60;
    define('HHNC_ENR_CLOSE_LIMIT_SECONDS',$seconds);
}

/* Ordering Closes X days after week begins */
if(!defined('HHNC_ORDER_CLOSE')) {
    define('HHNC_ORDER_CLOSE',$orderingcloses);
}

/* ZipCode Filtering */
$zips   = array(
28032,28056,28016,28098,28012,28020,28034,28064,28054,28053,28055,28101,28102,28103,28104,28105,28106,28107,28108,28109,28110,28111,28112,28113,28114,28115,28116,28117,28118,28119,28120,28121,28122,28123,28124,28125,28126,28127,28128,28129,28130,28131,28132,28133,28134,28135,28136,28137,28138,28139,28140,28141,28142,28143,28144,28145,28146,28147,28148,28149,28150,28151,28152,28153,28154,28155,28156,28157,28158,28159,28160,28161,28162,28163,28164,28165,28166,28167,28168,28169,28170,28171,28172,28173,28174,28175,28176,28177,28178,28179,28180,28181,28182,28183,28184,28185,28186,28187,28188,28189,28190,28191,28192,28193,28194,28195,28196,28197,28198,28199,28200,28201,28202,28203,28204,28205,28206,28207,28208,28209,28210,28211,28212,28213,28214,28215,28216,28217,28218,28219,28220,28221,28222,28223,28224,28225,28226,28227,28228,28229,28230,28231,28232,28233,28234,28235,28236,28237,28238,28239,28240,28241,28242,28243,28244,28245,28246,28247,28248,28249,28250,28251,28252,28253,28254,28255,28256,28257,28258,28259,28260,28261,28262,28263,28264,28265,28266,28267,28268,28269,28270,28271,28272,28273,28274,28275,28276,28277,28278,28279,28280,28281,28282,28283,28284,28285,28286,28287,28288,28289,28290,28291,28292,28293,28294,28295,28296,28297,28298,28299

);


?>
