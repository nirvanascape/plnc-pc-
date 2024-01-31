<?php
session_start();
extract($_GET);
extract($_POST);
extract($_SERVER);
extract($_COOKIE);
extract($_SESSION);
include $_SERVER['DOCUMENT_ROOT'].'/common/dbinfo.php';
include $_SERVER['DOCUMENT_ROOT'].'/common/dbclass.lib.php';
include $_SERVER['DOCUMENT_ROOT'].'/common/func.lib.php';
include $_SERVER['DOCUMENT_ROOT'].'/common/fileclass.lib.php';
include $_SERVER['DOCUMENT_ROOT'].'/common/lib.php';

define( TODAY,		date( "Y-m-d" ) );
define( MYSQL_HOST, "$host");
define( MYSQL_USER, "$user");
define( MYSQL_PWD, "$pw");
define( MYSQL_DB, "$db_name");

define( PHP_OPEN,					"<"."?" );
define( PHP_CLOSE,					"?".">" );

$objdb = new DB_CLASS( MYSQL_HOST, MYSQL_USER, MYSQL_PWD, MYSQL_DB );
$objfunc = new FUNC_CLASS();
$objfile = new FILE_CLASS();

include $_SERVER["DOCUMENT_ROOT"].'/common/admin.lib.php';

////////////////Äõ¸®½ºÆ®¸µ

$bbsQrystr = "&mname=$_REQUEST[mname]".
						"&subnum=$_REQUEST[subnum]".
						"&m_num=$_REQUEST[m_num]".
						"&id=$_REQUEST[id]".
						"&idx=$_REQUEST[idx]".
						"&page=$_REQUEST[page]".
						"&search_word=$_REQUEST[search_word]".
						"&field=$_REQUEST[field]";
?>
