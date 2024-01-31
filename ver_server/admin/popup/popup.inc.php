<?
###########################################################################
# 2008-05-02 소스 재구축 / 레이어 기능 추가 by 정실장
###########################################################################
?>

<SCRIPT>
function show_popup( id, width, height, top, left ) {
	var height = parseInt( height ) + 30;

	var url = "/admin/popup/popupView.html?id=" + id;
	var name = "w_show_popup_" + id;

	var property = new Array();
//	alert(height);
	property[0] = 'width=' + width;
	property[1] = 'height=' + height;
	property[2] = 'top=' + top;
	property[3] = 'left=' + left;

	var w_show_popup = window.open( url, name, property.join() );
	if( w_show_popup != null ) w_show_popup.focus();
}
</SCRIPT>

<?
$TBLPU	=	"popup";

$sql = "UPDATE $TBLPU SET ".
		"chkuse = 'n' ".
		"WHERE endday < '".TODAY."'";
$objdb->sqlExe( $sql );

$sql = "SELECT * FROM $TBLPU ".
		"WHERE chkuse = 'y' ".
		"ORDER BY id";
$rs = $objdb->sqlResult( $sql );
while( $pop = $objdb->sqlFetch( $rs ) ) {
	if( $_COOKIE["cookie_popup_".$pop[id]] ) continue;

	if( $pop[type] == 'l' ) {
		$_REQUEST[id] = $pop[id];
		include $_SERVER["DOCUMENT_ROOT"]."/admin/popup/popupView.html";
	}
	else {
?>
	<SCRIPT>
	show_popup( '<?= $pop[id] ?>', '<?= $pop[width] ?>', '<?= $pop[height] ?>', '<?= $pop[toploc] ?>', '<?= $pop[leftloc] ?>' );
	</SCRIPT>
<?
	}
}
?>