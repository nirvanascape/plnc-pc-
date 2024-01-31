<!--
var _editor_url = "/common/editor/";
//var _upload_dir = "";

var win_ie_ver = parseFloat( navigator.appVersion.split( "MSIE" )[1] );
if( navigator.userAgent.indexOf( 'Mac' ) >= 0 ) { win_ie_ver = 0; }
if( navigator.userAgent.indexOf( 'Windows CE' ) >= 0 ) { win_ie_ver = 0; }
if( navigator.userAgent.indexOf( 'Opera' ) >= 0 ) { win_ie_ver = 0; }

if( win_ie_ver >= 5.5 ) {
	document.write( '<scr' + 'ipt src="' + _editor_url + 'editor.js"' );
	document.write( ' language="Javascript1.2"></scr' + 'ipt>' );
}
else {
	document.write( '<scr' + 'ipt>function editor_generate() { return false; }' );
	document.write( 'function getContentIs( val ) { return \'n\'; }</scr' + 'ipt>' );
}
// -->

function getEditor( width, height, editmode, editor_obj_name ) {
	var config = new Object();    // create new config object
	config.width = width;
	config.height = height;
	config.editmode = editmode;			// 0,1
	//config.layer_pos = layer_pos;
	//_upload_dir = upload_dir;
	editor_generate( editor_obj_name, config );
}