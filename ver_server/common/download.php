<?
include $_SERVER['DOCUMENT_ROOT']."/common/config.inc.php";

$dir = str_replace( "../", "", $dir );
$file = str_replace( "../", "", $file );

if( substr( $dir, -1 ) != "/" ) $dir .= "/";

if( eregi( ".(ph|inc|php[0-9a-z]*|phtml|inc|cgi|asp|html|htm|bak|sql|old)$", $filename ) ) exit;

if( !($file && $dir && is_file( $_SERVER["DOCUMENT_ROOT"].$dir.$file ) ) ) $objfunc->alert( "존재하지 않는 파일입니다." );

$objfile->downloadFile( $file, $_SERVER["DOCUMENT_ROOT"].$dir );

exit;
?>