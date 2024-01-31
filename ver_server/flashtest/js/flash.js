// flash(파일주소, 가로, 세로, 배경색, 윈도우모드, 변수, 경로)
function flash(url,w,h,bg,win,vars,base){
	var s=
	"<object classid='clsid:d27cdb6e-ae6d-11cf-96b8-444553540000' codebase='http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0' width='"+w+"' height='"+h+"' align='middle'>"+
	"<param name='allowScriptAccess' value='always' />"+
	"<param name='movie' value='"+url+"' />"+
	"<param name='wmode' value='"+win+"' />"+
	"<param name='menu' value='false' />"+
	"<param name='quality' value='high' />"+
	"<param name='FlashVars' value='"+vars+"' />"+
	"<param name='bgcolor' value='"+bg+"' />"+
	"<param name='base' value='"+base+"' />"+
	"<embed src='"+url+"' base='"+base+"' wmode='"+win+"' menu='false' quality='high' bgcolor='"+bg+"' width='"+w+"' height='"+h+"' align='middle' type='application/x-shockwave-flash' pluginspage='http://www.macromedia.com/go/getflashplayer' />"+
	"</object>";
	document.write(s);
}

var app;

switch( navigator.appName ) {
	case "Microsoft Internet Explorer" :
		app = "ie";
		break;
	case "Netscape" :
		app = "ns";
		break;
}
// explore 변경 관련 - flash
function load_flash( src, w, h, id ) {
	if( typeof( id ) == "undefined" ) id = "";

	html = '';
	html += '<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="' + w + '" height="' + h + '" id="' + id + '">';
	html += '<param name="movie" value="' + src + '">';
    html += '<param name="quality" value="high">';
	html += '<param name="wmode" value="transparent">';
	html += '<embed src="' + src + '" quality="high" pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="' + w + '" height="' + h + '"></embed>';
	html += '</OBJECT>';
	
	document.writeln( html );
}

// explore 변경 관련 - object
function load_object( src, w, h, id ) {
	if( typeof( id ) == "undefined" ) id = "";

	html = '';
	html += '<OBJECT width="' + w + '" height="' + h + '" id="' + id + '">';
	html += '<param name="movie" value="' + src + '">';
    html += '<param name="quality" value="high">';
	html += '<param name="wmode" value="transparent">';
	html += '<embed src="' + src + '" quality="high" width="' + w + '" height="' + h + '"></embed>';
	html += '</OBJECT>';
	
	document.writeln( html );
}


/*
IE Flash ActiveContent Activation Script
Author: Faisal Iqbal (chall3ng3r)
Blog: http://www.orison.biz/blog/chall3ng3r/

Feel free to modify or distribute.
*/
 
/*
Method: FlashObject
 Param1: SWF path
 Param2: Movie width
 Param3: Movie height
 Param4: BGColor
 Param5: Flashvars (Optional)
*/
function FlashObject(swf, width, height, bgcolor, id, flashvars)
{
    var strFlashTag = new String();
    
    if (navigator.appName.indexOf("Microsoft") != -1)
    {
        strFlashTag += '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" ';
        strFlashTag += 'codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=version=8,0,0,0" ';
        strFlashTag += 'id="' + id + '" width="' + width + '" height="' + height + '">';
        strFlashTag += '<param name="movie" value="' + swf + '"/>';
        
        if(flashvars != null) {strFlashTag += '<param name="flashvars" value="' + flashvars + '"/>'};
        strFlashTag += '<param name="quality" value="best"/>';
        strFlashTag += '<param name="bgcolor" value="' + bgcolor + '"/>';
        strFlashTag += '<param name="menu" value="false"/>';
        strFlashTag += '<param name="salign" value="LT"/>';
        strFlashTag += '<param name="scale" value="noscale"/>';
        strFlashTag += '<param name="wmode" value="transparent"/>';
        strFlashTag += '<param name="allowScriptAccess" value="sameDomain"/>';
        strFlashTag += '</object>';
    }
    else
    {
        strFlashTag += '<embed src="' + swf + '" ';
        strFlashTag += 'quality="best" ';
        strFlashTag += 'bgcolor="' + bgcolor + '" ';
        strFlashTag += 'width="' + width + '" ';
        strFlashTag += 'height="' + height + '" ';
        strFlashTag += 'menu="false" ';
        strFlashTag += 'scale="noscale" ';
         strFlashTag += 'id="' + id + '" ';
        strFlashTag += 'salign="LT" ';
       //  strFlashTag += 'wmode="transparent" ';
        strFlashTag += 'allowScriptAccess="sameDomain" ';
        if(flashvars != null) {strFlashTag += 'flashvars="' + flashvars + '" '};
        strFlashTag += 'type="application/x-shockwave-flash" ';
        strFlashTag += 'pluginspage="http://www.macromedia.com/go/getflashplayer">';
        strFlashTag += '</embed>';
    }

 document.write(strFlashTag);
}

function trim( str ) {
      var count = str.length;
      var len = count;                
      var st = 0;

      while( st < len && str.charAt( st ) <= ' ' ) {
         st++;
      }

      while ( st < len && str.charAt( len - 1 ) <= ' ' ) {
         len--;
      }

      return ( st > 0 || len < count ) ? str.substring( st, len ) : str;   
}

function form_length_chk( form, form_name, msg, min, max, func ) {
	if( msg == null ) msg = "";
	if( min == null ) min = 0;
	if( max == null ) max = 0;
	if( func == null ) func = "focus";

	var obj = form.elements[form_name];
	var chk = true;
	var fs_chk = false;

	if( typeof( obj ) == "object" ) {
		switch( obj.type ) {
			case "text" :
			case "textarea" :
			obj.value = trim( obj.value );
				if( min && obj.value.length < min ) chk = false;
				if( max && obj.value.length > max ) chk = false;
				fs_chk = true;
				break;
			case "password" :
				obj.value = trim( obj.value );
				
				if( min && obj.value.length < min ) chk = false;
				if( max && obj.value.length > max ) chk = false;
				fs_chk = true;
				break;
		}
	}

	if( chk == true ) {
		return true;
	}
	else {
		if( msg ) alert( msg );

		if( fs_chk ) {
			eval( "obj." + func + "();" );
		}

		return false;
	}
}

function form_regexp_chk( form, form_name, msg, regexp, func ) {
	if( msg == null ) msg = "";
	if( regexp == null ) regexp = "";
	if( func == null ) func = "focus";

	var obj = form.elements[form_name];

	var chk = false;
	var fs_chk = false;

	if( typeof( obj ) == "object" && regexp ) {
		var re = new RegExp( regexp, "g" );

		switch( obj.type ) {
			case "text" :
			case "textarea" :
				obj.value = trim( obj.value );
				if( obj.value.match( re ) ) chk = true;
				fs_chk = true;
				break;
			case "password" :
				obj.value = trim( obj.value );
				if( obj.value.match( re ) ) chk = true;
				fs_chk = true;
				break;
		}
	}

	if( chk == true ) {
		return true;
	}
	else {
		if( msg ) alert( msg );

		if( fs_chk ) {
			eval( "obj." + func + "();" );
		}

		return false;
	}
}


function moveNext( obj, num ) {
	val = event.srcElement.value;
	if( val.length == num ) obj.focus();
}
