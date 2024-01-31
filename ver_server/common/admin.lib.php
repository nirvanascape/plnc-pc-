<?
function OK() {
	echo "OK";

	exit;
}

function FAIL() {
	echo "FAIL";

	exit;
}

function fAlert($val, $exit=true) {
	global $objfunc;

	$objfunc->alert( addslashes( $val ), $exit );
}

function debug( $str ) {
	global $objfunc;

	echo "<script>parent.debugTbl.style.display = '';parent.debugArea.value=\"".$objfunc->htmlText( $str )."\";</script>";
	exit;
}

function adminMenu( $str, $url, $class='', $target='_self' ) {
	$rtn = "<a href='".$url."' target='".$target."'><font class='wine ".$class."'>".$str."</font></a>";

	return $rtn;
}

// config 업데이트
function updateConfig( $filename, $content ) {
	global $objdb;


	$sql = "SELECT COUNT( * ) FROM config_tbl WHERE filename = '".$filename."'";
	$cnt = $objdb->sqlRowOne( $sql );

	if( $cnt ) $sql = "UPDATE config_tbl SET content = '".$content."' WHERE filename = '".$filename."'";
	else $sql = "INSERT INTO config_tbl VALUES ( '".$filename."', '".$content."' )";
	$objdb->sqlExe( $sql );

}

############################################################################
# function                                                                 #
# phpcode_str(....)                                                        #
# args : 설정파일 관련 모든변수문자열                                      #
# coment : 모든 인수를 설정파일로 쓸수있게 변경한다.                       #
############################################################################

function phpcode_str() {
	global $objfile;

	$args_num = func_num_args();

	$rtnstr = PHP_OPEN."\n";

	for( $i = 0; $i < $args_num; $i++ ) {
		$thisarg = func_get_arg( $i );

		if( eregi( "^_", $thisarg ) ) {
			$thisstr = $thisarg;
			$thisval = $objfile->addSlash( $GLOBALS[$thisarg] );
		}
		else {
			$thisstr = "_".$thisarg;
			$thisval = $GLOBALS[$thisarg];
			$thisval = eregi_replace( "\\\'","'", $thisval );
			$thisval = eregi_replace( "\\\$", "\\$", $thisval );
			$thisval = eregi_replace( "<\?", "<@", $thisval );
			$thisval = eregi_replace( "\?>", "@>", $thisval );
		}

		$rtnstr .=   "\${$thisstr}=\"{$thisval}\";\n";
	}

	$rtnstr .= PHP_CLOSE;

	return $rtnstr;
}

function getconfstr(){
	global $objfile;
	$args_num = func_num_args();
	$args = func_get_args();
	if( $args_num > 0 )
	{
		include( $args[0] );
		$conf = get_defined_vars();
		for( $i = 1; $i<$args_num; $i++ )
		{
			$conf_args["_{$args[$i]}"] = $objfile->addSlash($GLOBALS["{$args[$i]}"]);
		}
		$rtnstr = PHP_OPEN."\n";
		foreach( $conf as $key=>$value )
		{
			if( $key != 'args_num' && $key != 'args' && $key != 'objfile' && $key != 'GLOBALS' )
			{
				if( isset( $conf_args[$key] ) )
				{
					$thisval = $conf_args[$key];
					unset($conf_args[$key]);
				}else{
					$thisval = $value;
					$thisval = eregi_replace( "\\\'","'", $thisval );
					$thisval = eregi_replace( "\\\$", "\\$", $thisval );
					$thisval = eregi_replace( "<\?", "<@", $thisval );
					$thisval = eregi_replace( "\?>", "@>", $thisval );
				}
				$rtnstr .=   "\${$key}=\"{$thisval}\";\n";
			}
		}
		foreach( $conf_args as $key=>$value )
		{
			$thisval = $conf_args[$key];
			unset($conf_args[$key]);
			$rtnstr .=   "\${$key}=\"{$thisval}\";\n";
		}
		$rtnstr .= PHP_CLOSE;
	}
	return $rtnstr;
}



###############################################################################
# 상품관련
###############################################################################

function chkGood( $tbl, $code ) {
	global $objdb;
	
	$sql = "SELECT count(*) FROM $tbl WHERE code = '".$code."'";
	$cnt = $objdb->sqlRowone($sql);

	if( $cnt )	return true;
	else		return false;
}





###############################################################################
# 디자인관련
###############################################################################

function skinBlurBottom() {
	global $_BOTTOMTYPE;

	if( $_BOTTOMTYPE == "my" )		$rtn = "";
	else									$rtn = "style='background:#EEEEEE;'";

	return $rtn;
}

function skinBlurLeft() {
	global $_LEFTTYPE;

	if( $_LEFTTYPE == "my" )		$rtn = "";
	else								$rtn = "style='background:#EEEEEE;'";

	return $rtn;
}

// 위치안내 설정부분
function make_navi_set( $bg, $font1, $font2, $navi_set, $layer_id ) {
	$naviArr = explode("|",$navi_set);
	$cur_bg = $naviArr[0];
	$cur_font1 = $naviArr[1];
	$cur_font2 = $naviArr[2];
	
	echo <<<NAVI
		<script>
		function changeColor_$layer_id( bg, font1, font2 ) {
			var layer = document.all["$layer_id"];

			layer.innerHTML = "<table cellpadding=5 bgcolor=" + bg + "><tr><td><font color='" + font1 + "'>Home &gt; 상위경로1 &gt; 상위경로2 &gt; </font><b><font color='" + font2 + "'>현재경로</font></b></td></tr></table>";
		}
		</script>
		배경색 <input type="text" name="$bg" size="10" class="box" value="$cur_bg" onkeyup="changeColor_$layer_id(this.form.$bg.value, this.form.$font1.value, this.form.$font2.value)">
		경로색 <input type="text" name="$font1" size="10" class="box" value="$cur_font1" onkeyup="changeColor_$layer_id(this.form.$bg.value, this.form.$font1.value, this.form.$font2.value)">
		현재색 <input type="text" name="$font2" size="10" class="box" value="$cur_font2" onkeyup="changeColor_$layer_id(this.form.$bg.value, this.form.$font1.value, this.form.$font2.value)"><br>
		<img height=5><br>
		<span id=$layer_id></span>
		<script>changeColor_$layer_id('$cur_bg', '$cur_font1', '$cur_font2')</script>
NAVI;
}




###############################################################
# 회원등급 관련
###############################################################

// 레벨표시
function levelSelect( $name='level', $curLevel='', $emptyStr='', $fullwidth=false ) {
	global $objdb;
	global $_NOLEVELNAME;

	echo "<select name=\"".$name."\" class='box ".( $fullwidth ? "fullwidth" : "" )."'>";
	if( $emptyStr ) echo "<option value=\"\">".$emptyStr."</option>";
	$sql = "SELECT level, levelname FROM memberlevel_tbl ORDER BY level";
	$rs = $objdb->sqlResult($sql);
	while( $row = $objdb->sqlFetch($rs) ) {
		if( $curLevel == "level".$row[level] ) $added = " selected";
		else $added = "";
		echo "<option value=\"level".$row[level]."\"".$added.">".$row[levelname]."</option>";
	}
	if( $curLevel == "n" ) $added = " selected";
	else $added = "";
	echo "<option value=\"n\"".$added.">".$_NOLEVELNAME."</option>";
	echo "</select>";
}

// 회원등급 체크박스 표시 $curLevel=1|2|....|10
function levelCheck( $name="level[]", $curLevel='', $line='4', $checkableLevel='' ) {
	global $objdb;
	global $_NOLEVELNAME;

	if( !$curLevel ) $curLevel = "n|1|2|3|4|5|6|7|8|9|10";
	if( !$checkableLevel ) $checkableLevel = "n|1|2|3|4|5|6|7|8|9|10";
	
	$curLevelArr = explode( "|", $curLevel );
	$checkableLevelArr = explode( "|", $checkableLevel );
	$sql = "SELECT level, levelname FROM memberlevel_tbl ORDER BY level";
	$rs = $objdb->sqlResult($sql);
	$i = 1;
	while( $row = $objdb->sqlFetch($rs) ) {
		if( in_array( $row[level], $curLevelArr ) ) $added = " checked";
		else $added = "";
		if( in_array( $row[level], $checkableLevelArr ) ) {
			echo "<input type=checkbox name=".$name." value=".$row[level].$added.">".$row[level]."등급 - ".$row[levelname]."&nbsp;";
		}
		else {
			echo "<input type=checkbox disabled>".$row[level]."등급 - ".$row[levelname]."&nbsp;";
		}
		if( $i%$line == 0 ) echo "<br>";

		$i++;
	}
	if( in_array( "n", $curLevelArr ) ) $added = " checked";
	else $added = "";
	if( $_NOLEVELNAME ) $nameStr = " - ".$_NOLEVELNAME;
	if( in_array( "n", $checkableLevelArr ) ) {
		echo "<input type=checkbox name=".$name." value=\"n\"".$added.">미지정".$nameStr."&nbsp;";
	}
	else {
		echo "<input type=checkbox disabled>미지정".$nameStr."&nbsp;";
	}
}



###############################################################################
# 기타
###############################################################################

function menu_url( $chk, $url ) {
	if( $chk ) return $url;
	else return "javascript:alert( '접근권한이 없습니다.' );";
}

function get_service_type( $type='' ) {
	global $dear;

	if( !$type ) $type = $dear->get( 'service_type' );

	switch ( $type ) {
		case "SOHO" :
			$rtn = "소호형";
			break;
		case "BIZ" :
			$rtn = "비즈형";
			break;
		case "PRO" :
			$rtn = "프로형";
			break;
		case "INDI" :
			$rtn = "프로형[스페셜]";
			break;
		case "OUT" :
			$rtn = "프로형[독립]";
			break;
		case "PC" :
			$rtn = "PC전문형";
			break;
		case "OUTPC" :
			$rtn = "PC전문형[독립]";
			break;
		default :
			$rtn = strtoupper( $type )."형";
			break;
	}

	return $rtn;
}

// 부분적 기능제한 함수를 단순화 - 하위포함개념 by 정실장 in 2008-01-15
function chk_priv_grade( $grade ) {
	global $dear;

	switch( $dear->get( 'service_type' ) ) {
		case "SOHO" :
			$grade_my = 1;
			break;
		case "BIZ" :
			$grade_my = 2;
			break;
		default :
			$grade_my = 3;
			break;
	}

	if( $grade_my >= $grade ) return true;
	else return false;
}

function chk_priv_grade_url( $grade, $url ) {
	if( chk_priv_grade( $grade ) ) $rtn = $url;
	else $rtn = "javascript:alert( '[".get_service_type()."]에선 지원하지 않는 기능입니다.' );";

	return $rtn;
}

// 금월로부터의 개월차이 계산
function diff_month( $dateMonth ) {
	$i = 0;
	$thisMonth = date( "Y-m", mktime() );
	WHILE ( $thisMonth > $dateMonth ) {
		$dateArr = explode( "-", $dateMonth );
		$dateMonth = date( "Y-m", mktime( 0, 0, 0, $dateArr[1] + 1, 1, $dateArr[0] ) );
		$i++;
	}

	return $i;
}

function date_tool( $obj1, $obj2 ) {
	$str = "";
	$str .= "<a href=\"javascript:setValue( ".$obj1.", '".get_date( 0 )."' );setValue( ".$obj2.", '".get_date( 0 )."' );\"><font class=sky>[오늘]</font></a>";
	$str .= "<a href=\"javascript:setValue( ".$obj1.", '".get_date( 7 )."' );setValue( ".$obj2.", '".get_date( 0 )."' );\"><font class=sky>[일주일]</font></a>";
	$str .= "<a href=\"javascript:setValue( ".$obj1.", '".get_date( 1, 'm' )."' );setValue( ".$obj2.", '".get_date( 0 )."' );\"><font class=sky>[한달]</font></a>";

	return $str;
}

function date_input( $form, $obj1, $obj2, $value1='', $value2='' ) {
	$rtn = <<<STR
	<input type="text" name="$obj1" size="10" class=box onfocus="popUpCalendar( this, this, 'yyyy-mm-dd' )" value="$value1" readonly> 부터 <input type="text" name="$obj2" size="10" class=box onfocus="popUpCalendar( this, this, 'yyyy-mm-dd' )" value="$value2" readonly> 까지
STR;
	$rtn .= " ".date_tool( $form.".".$obj1, $form.".".$obj2 );

	return $rtn;
}

function date_input_single( $obj, $value='' ) {
	$rtn = <<<STR
	<input type="text" name="$obj" size="10" class=box onfocus="popUpCalendar( this, this, 'yyyy-mm-dd' )" value="$value" readonly>
STR;

	return $rtn;
}

function getColorPicker( $obj1, $obj2, $obj3 ) {
	//$rtn = "<img src='/shopadmin/img2/ico_cp.gif' align=absmiddle class=hand onclick=\"getColorPicker( 'opener.".$obj1."', 'opener.".$obj2."', 'opener.".$obj3."' );\">";
	$rtn = "<a href=\"javascript:getColorPicker( 'opener.".$obj1."', 'opener.".$obj2."', 'opener.".$obj3."' );\"><img src='/shopadmin/img2/ico_cp.gif' align=absmiddle border=0></a>";

	return $rtn;
}

function getColorPickerSet( $form, $objname, $val, $addedStyle='', $sepa='' ) {
	$new_objname = $form.".elements[\\'".$objname."\\']";
	
	$rtn = "<input type=text name=".$objname." size=9 class='box imedisabled' onFocus='this.select();' value=\"".htmlText( $val )."\" style='color:".htmlText( $val )."' ".$addedStyle."> ";
	$rtn .= $sepa;
	$rtn .= getColorPicker( $new_objname, $new_objname, $new_objname.".style.color" );

	return $rtn;
}

function makeForm( $name, $mode, $forms='', $action='', $target='submitFrame' ) {
	$rtn = "";

	$rtn .= "<FORM name=\"".$name."\" method=post action=\"".$action."\" target=\"".$target."\">\n";

	if( is_array( $forms ) ) {
		foreach( $forms as $k => $form ) {
			$rtn .= "<input type=hidden name=\"".$form."\">\n";
		}
	}
	else {
		$rtn .= "<input type=hidden name=\"".$forms."\">\n";
	}

	$rtn .= "<input type=hidden name=\"mode\" value=\"".$mode."\">";

	$rtn .= "</FORM>\n";

	return $rtn;
}

function moveEmptyPage( $PageNo, $qryString ) {
	global $PHP_SELF;
	global $objfunc;

	if( $PageNo > 1 ) {
		$objfunc->goUrl( $PHP_SELF."?PageNo=".( $PageNo - 1 ).$qryString );
	}

	return;
}

function getClassTR( $i, $type='' ) {
	switch( $type ) {
		case "red" :
			if( $i % 2 ) {
				$rtn	= "admintd11";
			}
			else {
				$rtn	= "admintd1";
			}
			break;
		case "gray" :
			if( $i % 2 ) {
				$rtn	= "admintd22";
			}
			else {
				$rtn	= "admintd2";
			}
			break;
		case "pink" :
			if( $i % 2 ) {
				$rtn	= "admintd33";
			}
			else {
				$rtn	= "admintd3";
			}
			break;
		default :
			if( $i % 2 ) {
				$rtn	= "adminsky";
			}
			else {
				$rtn	= "adminwhite";
			}
			break;
	}

	return $rtn;
}
?>