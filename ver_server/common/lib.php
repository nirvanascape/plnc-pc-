<?
#####################################################
# javascript write로 html을 출력하는 클래스
# by jgkim
# 2006-12-01 TEST
#####################################################

class ob_jsoutput {
	var $js_start_tag = "\n<SCRIPT LANGUAGE=\"JavaScript\">\n";
	var $js_end_tag = "\n</SCRIPT>\n";
	var $js_varname = "html_str";
	var $js_write = "document.writeln";
	var $nl_write = true;
	function ob_jsoutput(){
		$tmp = @ob_list_handlers();
		if( count($tmp) > 0 )
			while( @ob_end_flush() );
		else
			flush();
		if( function_exists('ob_start') )
			ob_start( array(&$this,'jsoutput') );
	}
	function jsoutput( $buf, $status ){
		preg_match_all("/<script[^>]*>\s*<\/script>/i",$buf,$lscript,PREG_PATTERN_ORDER);
		$htmls = spliti( "<script[^>]*>", $buf );
		unset($buf);
		foreach( $htmls as $k=>$html ){
			$val = spliti( "</script[^>]*>", $html );
			if( count($val)>1 ){
				$val[0] = "\n".$this->js_write."(".$this->js_varname.");\n".$val[0] . "\n".$this->js_varname." = '';\n";
				$this->replace($val[1]);
			}else{
				$this->replace($val[0]);
			}
			$htmls[$k] = implode('',$val);
		}
		$nbuf  = implode('',$lscript[0]);
		$nbuf .= $this->js_start_tag."\n";
		$nbuf .= "document.close();\n";
		$nbuf .= "var ".$this->js_varname." = '';\n";
		$nbuf .= implode('',$htmls)."\n";
		$nbuf .= $this->js_write."(".$this->js_varname.");\n";
		$nbuf .= $this->js_end_tag;

		$nbuf .= "\n<!-- ".memory_get_usage()." -->";
		//$data = getrusage();
		//$nbuf .= "\n<!-- ".$data['ru_nswap']." -->";
		//$nbuf .= "\n<!-- ".$data['ru_majflt']." -->";
		//$nbuf .= "\n<!-- ".$data['ru_ixrss']." -->";
		//$nbuf .= "\n<!-- ".$data['ru_utime.tv_sec']." -->";
		//$nbuf .= "\n<!-- ".$data['ru_stime.tv_sec']." -->";
		unset($htmls);
		return $nbuf;
	}
	function replace(&$val){
		$val = str_replace("\r","",$val);
		$val = str_replace("\\","\\\\",$val);
		$val = str_replace("'","\\'",$val);
		if( $this->nl_write ){
			$val = str_replace("\n","';\n".$this->js_varname." += '",$val);
		}else{
			$val = str_replace("\n","",$val);
		}
		$val = $this->js_varname." += '" . $val . "';";
		//$val = eregi_replace("^","html_str += '",$val);
		//$val = eregi_replace("$","';",$val);
	}
}
function &ob_class(){
	$ob = new ob_jsoutput;
	return $ob;
}


#####################################################
# 보안관련
#####################################################

function get_parse_url( $url, $part ) {
	$rtn = "";

	$arr = parse_url( $url );
	
	switch( $part ) {
		case "dirname" :
			$rtn = dirname( $arr[path] );
			break;
		case "basename" :
			$rtn = basename( $arr[path] );
			break;
		default :
			$rtn = $arr[$part];
			break;
	}

	return $rtn;
}

function chk_from_our_domain() {
	$rtn = false;

	if( get_parse_url( _HTTP_REFERER_, "host" ) == _HTTP_HOST_ ) $rtn = true;

	return $rtn;
}

#####################################################








#####################################################
# 가격관련
#####################################################

// 실수정수구분 넘버포맷 반환
function nf( $price ) {
	//$price = floatval( $price );
	$price = eregi_replace( "\.[0*]$", "", $price );
	if( eregi( "\.", $price ) ) $rtnstr = number_format( $price, 2 );
	else $rtnstr = number_format( $price );

	return $rtnstr;
}

function pm( $num ) {
	$rtn = $num * -1;

	return $rtn;
}

// 판매가,세일가 반환
function getPrice( $price, $saleprice=0 ) {
	if( $saleprice )
		return $saleprice;
	else
		return $price;
}

// 가격 표시
function goodPrice( $price, $pricedeco1='', $pricedeco2='', $zerotext=true ) {
	global $_ZEROTEXT, $_UNIT1, $_UNIT2;
	global $PHP_SELF;

	if( eregi( "/shopadmin/|/my_admin/", $PHP_SELF ) ) $zerotext = false;

	if( !$zerotext ) $_ZEROTEXT = "";
	if( empty($_UNIT1) && empty($_UNIT2) ) $_UNIT2 = "원";

	if( $price ) $rtn_price = $_UNIT1.$pricedeco1.nf($price).$pricedeco2.$_UNIT2;
	else if( $_ZEROTEXT ) $rtn_price = $pricedeco1.$_ZEROTEXT.$pricedeco2;
	else $rtn_price = $_UNIT1.$pricedeco1."0".$pricedeco2.$_UNIT2;

	return $rtn_price;
}

function getPoint( $price, $point ) {
	global $_AUTOPOINT;

	if( $_AUTOPOINT ) $point = round( $price * $_AUTOPOINT / 100 );
	else $point = $point;

	return $point;
}

function goodSoldOut( $stocknum, $soldoutchk, $order_min, $soldoutmsg='' ) {
	global $_GOODSTOCK;

	$chk = false;
	$rtnstr = false;

	if( $_GOODSTOCK == "y" && $stocknum < $order_min ) {
		$chk = true;
	}

	if( $soldoutchk == "y" ) {
		$chk = true;
	}

	if( $chk ) {
		$rtnstr = ( $soldoutmsg != '' ) ? $soldoutmsg : true;
	}

	return $rtnstr;
}

function getGoodStockWithOptStock( $code, $stocknum, $mainpropertyString ) {
	global $objdb;

	$stockNum = 0;

	if( $mainpropertyString ) {
		$mainproperty = explode( "<:@:>", $mainpropertyString );

		foreach( $mainproperty as $k=>$v ) {
			$mainpropertyArr = explode( "@", $v );
			$chkArr[] = $mainpropertyArr[1];
		}

		$sql = "SELECT * FROM good_option_stock WHERE code = '".$code."'";
		$rs = $objdb->sqlResult($sql);
		while( $stock = $objdb->sqlFetch($rs) ) {
			$stockArr = explode( "*", $stock[optionkey] );	//	070820	addslashes 제거
			$diff = array_diff( $chkArr, $stockArr );
			if( count( array_diff( $stockArr, $chkArr ) ) == 0 ) {
				$stockNum = $stock[optionnum];
				if( $stockNum < 0 ) $stockNum = 0;
			}
		}

		if( $stockNum > $stocknum ) $stockNum = $stocknum;
	}
	else {
		$stockNum = $stocknum;
	}

	return $stockNum;
}

function getTotalPrice_good( $dbopt, $dbmainproperty, $dbetcgood, $dbprice, $dbqty ) {
	$totalPrice = $dbprice * $dbqty;

	$opt = get_selected_opt( $dbopt, $dbmainproperty );
	$etc = get_selected_etc( $dbetcgood );

	$opt_price = 0;
	foreach( $opt as $k=>$v ) {
		if( $v[ng] != "y" ) $opt_price += $v[price];
	}
	$totalPrice += $opt_price * $dbqty;

	$etc_price = 0;
	foreach( $etc as $k=>$v ) {
		$etc_price += $v[price];
	}
	$totalPrice += $etc_price * $dbqty;

	return $totalPrice;
}

function getNgPrice_good( $dbopt, $dbmainproperty ) {
	$opt = get_selected_opt( $dbopt, $dbmainproperty );

	$ng_price = 0;
	foreach( $opt as $k=>$v ) {
		if( $v[ng] == "y" ) $ng_price += $v[price];
	}

	return $ng_price;
}

function mainGoodPrice( $price ) {
	global $_ZEROTEXT;

	if( $price ) $rtn_price = nf($price);
	else if( $_ZEROTEXT ) $rtn_price = $_ZEROTEXT;
	else $rtn_price = "0";

	return $rtn_price;
}

function getMainPrice( $code )
{
	global $objdb;

	$sql = "SELECT chkcobuy, price, saleprice, chklevelprice, levelprice, nolevelmargin FROM good WHERE chkuse='y' AND chkmain='1' AND chkcate='y' AND code='".$code."'";
	$row = $objdb->sqlRow($sql);

	if( $row[chkcobuy] == "y" ) {
		$sql = "SELECT * FROM cobuy WHERE code = '".$row[code]."'";
		$cb = $objdb->sqlRow($sql);
		$row = array_merge( $row, $cb );

		if( $row[cbprice] ) $price = $row[cbprice];
		else $price = getprice( $row[price], $row[saleprice] );
	}else
	{
		$price = levelprice( $row[price], $row[saleprice], $row[chklevelprice], $row[levelprice], $row[nolevelmargin] );
	}

	return mainGoodPrice( $price );
}

####################################################################
# 배송료 관련
####################################################################

// 배송료 계산
function carryPrice( $totalPrice ) {
	global $_CHKCARRYUSE, $_CARRYCOST, $_CARRYPRICE, $_CARRYRANGESTART, $_CARRYRANGEPRICE;

	if( $_CHKCARRYUSE == "y" ) {
		// 무료배송료가 있을 경우 - 할인정책 및 무료정책 적용
		if( $_CARRYCOST > 0 ) {
			if( $totalPrice >= $_CARRYCOST ) {
				$carryPrice = 0;
			}
			else if( $_CARRYRANGESTART && $_CARRYRANGEPRICE && $totalPrice >= $_CARRYRANGESTART ) {
				$carryPrice = $_CARRYRANGEPRICE;
			}
			else {
				$carryPrice = $_CARRYPRICE;
			}
		}
		// 무료배송료가 없을 경우 - 할인정책만 적용 후 무조건 청구
		else {
			if( $_CARRYRANGESTART && $_CARRYRANGEPRICE && $totalPrice >= $_CARRYRANGESTART ) {
				$carryPrice = $_CARRYRANGEPRICE;
			}
			else {
				$carryPrice = $_CARRYPRICE;
			}
		}
	}
	else {
		$carryPrice = 0;
	}

	return $carryPrice;
}

// 추가배송방법
function carryPriceEtc( $dbcarrytype ) {
	$carrytypeArr = explode( "@", $dbcarrytype );

	return $carrytypeArr[1];
}

function carryPrintEtc( $dbcarrytype, $added='' ) {
	$carrytypeArr = explode( "@", $dbcarrytype );
	echo $added."<b>[배송방법]</b> ".$carrytypeArr[0];
	if( $carrytypeArr[1] > 0 ) echo " &nbsp;<b>[배송료]</b> ".goodprice( $carrytypeArr[1] )." ";
	if( $carrytypeArr[2] ) echo "&nbsp;- ".$carrytypeArr[2];
	echo "<br>";
}


####################################################################
# 결제 관련
####################################################################

function getPaymethodInfo( $paymethod ) {
	switch( $paymethod ) {
		case "bank"  :
			$bgcolor	=	"#FFFFFF";
			$icon		=	"ico_cash.gif";
			break;
		case "card"  :
			$bgcolor	=	"#F3F8F9";
			$icon		=	"ico_card.gif";
			break;
		case "real"  :
			$bgcolor	=	"#F7F9F3";
			$icon		=	"ico_banking.gif";
			break;
		case "mobi"  :
			$bgcolor	=	"#F2F7EA";
			$icon		=	"ico_mobile.gif";
			break;
		case "virt"  :
			$bgcolor	=	"#FFF8F3";
			$icon		=	"ico_virtual.gif";
			break;
		case "escr"  :
			$bgcolor	=	"#EFF5F7";
			$icon		=	"ico_escr.gif";
			break;
		default :
			$bgcolor	=	"#FFFFFF";
			$icon		=	"";
			break;
	}

	if( $icon ) $icon = "<img border=0 src='/shopadmin/img2/".$icon."'>";

	$rtn = array(
		"bgcolor"	=>	 $bgcolor,
		"icon"		=>	 $icon
	);

	return $rtn;
}

function getPaymethodName( $paymethod ) {
	switch( $paymethod ) {
		case "bank"  :
			$rtn = "무통장입금";
			break;
		case "card"  :
			$rtn = "신용카드";
			break;
		case "real"  :
			$rtn = "실시간계좌이체";
			break;
		case "mobi"  :
			$rtn = "핸드폰결제";
			break;
		case "virt"  :
			$rtn = "가상계좌";
			break;
		case "escr"  :
			$rtn = "에스크로";
			break;
		default :
			$rtn = "";
			break;
	}

	return $rtn;
}


/////////////////////////////////////////////////////////
// 상품 image 출력 관련 function by Bread powered by jgkim
/////////////////////////////////////////////////////////
function img_url( $image='' ) {
	$_DEFAULT_GOOD_IMAGE_URL = "/good_image/";

	$image_path = httpChk( $image ) ? $image : $_DEFAULT_GOOD_IMAGE_URL.$image;

	return $image_path;
}

function get_ImageSize( $imgurl='', $fix_width=115, $fix_height=115, $kind='s_1', $img_size='' ) {
	$httpchk = httpChk( $imgurl );

	$imgurl = $httpchk ? $imgurl : $_SERVER["DOCUMENT_ROOT"].$imgurl;

	if( $imgurl ) {
		if( $httpchk ) {
			$imgSize = parseImgSize( $img_size );
			if( $kind == "s" )	$kind	=	"s_1";
			if( $kind == "m" )	$kind	=	"m_1";
			if( $kind == "b" )	$kind	=	"b_1";

			if( $imgSize["width_".$kind] && $imgSize["height_".$kind] ) {
				$size = array( $imgSize["width_".$kind], $imgSize["height_".$kind] );
			}
			else {
				$size = array( $fix_width, $fix_height );
			}
		}
		else {
			$size = @getImageSize( $imgurl );
		}

		if( $fix_width && $fix_height ) $imageSize = img_size( $size, $fix_width, $fix_height );
		else $imageSize = $size;
	}
	else {
		if( $fix_width && $fix_height ) $imageSize = array( $fix_width, $fix_height );
		else $imageSize = array( 0, 0 );
	}

	return $imageSize;
}

function GetNoImageFile ($w)
{
	if ($w <= 40)
		$filename = "/img/goods/blankimg40.gif";
	else if ($w <= 60)
		$filename = "/img/goods/blankimg60.gif";
	else if ($w <= 115)
		$filename = "/img/goods/blankimg115.gif";
	else if ($w <= 145)
		$filename = "/img/goods/blankimg145.gif";
	else if ($w <= 240)
		$filename = "/img/goods/blankimg240.gif";
	else if ($w <= 250)
		$filename = "/img/goods/blankimg250.gif";
	else
		$filename = "/img/goods/blankimg.gif";

	return $filename;
}

function canMakeWatermark( $filesrc, $kind ) {
	global $_WATERMARK_USE, $_WATERMARK_IMG, $_WATERMARK_EXT, $_WATERMARK_NOSIZE;

	if( _SHOPADMIN === true ) return false;

	// 사용여부
	if( $_WATERMARK_USE != "y" ) return false;
	if( !$_WATERMARK_IMG ) return false;

	// 이미지 체크
	if( substr( $kind, 0, 2 ) == "s_" ) return false;

	// 파일존재유무 체크
	if( !is_file($filesrc) ) return false;
	if( !is_file($_SERVER["DOCUMENT_ROOT"]."/good_image/".$_WATERMARK_IMG) ) return false;

	$arr = @getimagesize($filesrc);

	// 이미지타입 체크
	switch( $arr[mime] ) {
		case 'image/jpeg' :
			if( !$_WATERMARK_EXT[0] ) return false;
			break;
		case 'image/gif' :
			if( !$_WATERMARK_EXT[1] ) return false;
			break;
		case 'image/png' :
			if( !$_WATERMARK_EXT[2] ) return false;
			break;
	}

	// 사이즈 체크
	if( $arr[0] < $_WATERMARK_NOSIZE || $arr[1] < $_WATERMARK_NOSIZE ) return false;

	return true;
}

function img_path( $image='', $fix_width=115, $fix_height=115, $kind='s_1', $etcstr='', $img_size='' ) {
	if( !$etcstr ) {
		$etcstr = "border=0";
	}

	if( empty($image) ) 	{
		$filename = GetNoImageFile($fix_width);
		$rtnval = "<img src=\"".$filename."\" ".$etcstr." width=\"".$fix_width."\" height=\"".$fix_height."\">";
		return $rtnval;
	}

	$image_path = img_url($image);

	$imageSize = get_ImageSize( $image_path, $fix_width, $fix_height , $kind, $img_size );

	$swidth	= $imageSize[0];
	$sheight	= $imageSize[1];

	if( $image_path != "" ) {
		$httpchk = httpChk( $image_path );

		if( $httpchk == false ) {
			if( is_file( $_SERVER["DOCUMENT_ROOT"].$image_path ) ) {
				if( canMakeWatermark( $_SERVER["DOCUMENT_ROOT"].$image_path, $kind ) ) $image_path = "/lib/imgHView.html?img=".$image;
			}
			else {
				$image_path	=	GetNoImageFile($fix_width);
				$swidth		=	$fix_width;
				$sheight		=	$fix_height;
			}
		}

		if( $swidth && $sheight ) {
			$sizestr = "WIDTH=\"".$swidth."\" HEIGHT=\"".$sheight."\"";
		}
		else {
			$sizestr = "";
		}
	}
	else {
		$image_path	= GetNoImageFile($fix_width);
		$sizestr		= "WIDTH=\"".$fix_width."\" HEIGHT=\"".$fix_height."\"";
	}

	$rtnval = "<IMG SRC=\"".$image_path."\" ".$sizestr." ".$etcstr.">";

	return $rtnval;
}

function img_size( $image_size_arr, $fix_width=115, $fix_height=115 ) {
	$width_diff = (float)( ( $image_size_arr[0] - $fix_width ) / $image_size_arr[0] );
	$height_diff = (float)( ( $image_size_arr[1] - $fix_height ) / $image_size_arr[1] );

	if( $width_diff >= $height_diff ) {
		if( $image_size_arr[0] > $fix_width ) {
			$WIDTH = $fix_width;
			$HEIGHT = $image_size_arr[1] * ( $WIDTH / $image_size_arr[0] );
		}
		else {
			$WIDTH = $image_size_arr[0];
			$HEIGHT = $image_size_arr[1];
		}
	}
	else {
		if( $image_size_arr[1] > $fix_height ) {
			$HEIGHT = $fix_height;
			$WIDTH = $image_size_arr[0] * ( $HEIGHT / $image_size_arr[1] );
		}
		else {
			$HEIGHT = $image_size_arr[1];
			$WIDTH = $image_size_arr[0];
		}
	}

	return array( ROUND($WIDTH), ROUND($HEIGHT) );
}

function getThImg( $path, $img, $width=0, $height=0, $style='border=0', $th='th_' ) {
	global $DOCUMENT_ROOT;

	if( is_file( $DOCUMENT_ROOT.$path."/".$th.$img ) ) $img = $th.$img;

	if( $img ) 	$rtn = "<img src='".$path."/".rawurlencode($img)."' ".imgSize( $DOCUMENT_ROOT.$path."/".$img, $width, $height )." ".$style.">";
	else $rtn = "";

	return $rtn;
}

function httpChk( $val ) {
	if( eregi( "^http://", $val ) ) return true;
	else return false;
}

function parseImgSize( $size_str ) {
	$size_arr = explode( "|", $size_str );

	$rtn[width_s_1]		=	$size_arr[0];
	$rtn[height_s_1]		=	$size_arr[1];
	$rtn[width_m_1]		=	$size_arr[2];
	$rtn[height_m_1]	=	$size_arr[3];
	$rtn[width_b_1]		=	$size_arr[4];
	$rtn[height_b_1]	=	$size_arr[5];
	$rtn[width_m_2]		=	$size_arr[6];
	$rtn[height_m_2]	=	$size_arr[7];
	$rtn[width_b_2]		=	$size_arr[8];
	$rtn[height_b_2]	=	$size_arr[9];
	$rtn[width_m_3]		=	$size_arr[10];
	$rtn[height_m_3]	=	$size_arr[11];
	$rtn[width_b_3]		=	$size_arr[12];
	$rtn[height_b_3]	=	$size_arr[13];
	$rtn[width_m_4]		=	$size_arr[14];
	$rtn[height_m_4]	=	$size_arr[15];
	$rtn[width_b_4]		=	$size_arr[16];
	$rtn[height_b_4]	=	$size_arr[17];

	return $rtn;
}

function imgSize( $img, $max_width=0, $max_height=0 ) {
	$httpchk = eregi( "^http://", $img ) ? true : false;

	if( $httpchk || !$max_width ) return "";
	else {
		$size = @getImageSize( $img );

		if( !$size ) return "";

		$width_diff = (float)( ( $size[0] - $max_width ) / $size[0] );
		$height_diff = (float)( ( $size[1] - $max_height ) / $size[1] );

		if( $width_diff >= $height_diff || $max_height == 0 ) {
			if( $size[0] > $max_width ) {
				$width = $max_width;
			}
			else {
				$width = $size[0];
			}

			if( $width ) $rtn = "width=".$width;
		}
		else {
			if( $size[1] > $max_height ) {
				$height = $max_height;
			}
			else {
				$height = $size[1];
			}

			if( $height ) $rtn = "height=".$height;
		}
	}

	return $rtn;
}

###############################################################
# 카테고리 관련 펑션
###############################################################
// largeno만 받아서 메뉴이미지 등의 모든 정보를 리턴
function getCateInfo( $largeno, $middleno='0', $smallno='0' ) {
	global $objdb;

	$sql = "SELECT * FROM category WHERE largeno = '".$largeno."' and middleno = '".$middleno."' and smallno = '".$smallno."'";
	$row = $objdb->sqlRow($sql);

	return $row;
}

// 각각의 타이틀만 가져오기
function getCateTitle($largeno, $middleno='0', $smallno='0', $fix_title='', $empty_str='') {
	global $objdb;
	if($fix_title) $title=$fix_title;
	else
	{
		$sql="select title from category where largeno='$largeno' and middleno='$middleno' and smallno='$smallno'";
		$title=$objdb->sqlRowone($sql);
	}

	if(!$title) $title=$empty_str;

	return $title;
}

// 카테고리 셀렉트 뿌리기
function getCateSelect( $name, $part, $onchange='', $style='', $selected_largeno=0, $selected_middleno=0, $selected_smallno=0 ) {
	global $objdb;

	switch( $part ) {
		case "largeno" :
			$qry	=	"WHERE largeno > 0 and middleno = 0 and smallno = 0";
			$str	=	"대분류";
			break;
		case "middleno" :
			$qry	=	"WHERE largeno = ".$selected_largeno." and middleno > 0 and smallno = 0";
			$str	=	"중분류";
			break;
		case "smallno" :
			$qry	=	"WHERE largeno = ".$selected_largeno." and middleno = ".$selected_middleno." and smallno > 0";
			$str	=	"소분류";
			break;
	}

	$ret = "<select name=\"".$name."\" class=box onchange=\"".$onchange."\" ".$style.">\n";
	$ret .= "<option value=\"0\">".$str." 선택</option>\n";
	$sql = "SELECT * FROM category ".$qry." ORDER BY sortno";
	$result = $objdb->sqlResult($sql);
	while( $row = $objdb->sqlFetch($result) ) {
		if( $row[$part] == ${"selected_".$part} ) $added = "selected";
		else $added = "";
		if( $row[chkmember] != "n" ) $row[title] .= " ⓜ";
		if( $row[chkuse] == "n" ) $optStr = "style='color:dd0000'";
		else $optStr = "";
		$ret .= "<option value=\"".$row[$part]."\" ".$added." ".$optStr.">".strip_tags($row[title])."</option>\n";
	}
	$ret .= "</select>\n";

	return $ret;
}

function get_cate_str( $largeno, $middleno, $smallno, $sepa=' ', $class='small' ) {
	if( !is_numeric( $largeno ) )	$largeno_title	=	$largeno;
	if( !is_numeric( $middleno ) )	$middleno_title	=	$middleno;
	if( !is_numeric( $smallno ) )	$smallno_title	=	$smallno;
	$catestr = "<img src=\"/shopadmin/img2/ico_bca.gif\" align=absmiddle border=0> ".getCateTitle( $largeno, 0, 0, $largeno_title, "<font color='gray'>존재하지않는분류</font>" );
	if( $middleno ) {
		$catestr .= $sepa."<img src=\"/shopadmin/img2/ico_mca.gif\" align=absmiddle border=0> ".getCateTitle( $largeno, $middleno, 0, $middleno_title, "<font color='gray'>존재하지않는분류</font>" );
	}
	if( $smallno ) {
		$catestr .= $sepa."<img src=\"/shopadmin/img2/ico_sca.gif\" align=absmiddle> ".getCateTitle( $largeno, $middleno, $smallno, $smallno_title, "<font color='gray'>존재하지않는분류</font>" );
	}
	$catestr = "<font class='".$class."'>".$catestr."</font>";

	return $catestr;
}

###############################################################
# 회원등급 관련
###############################################################

// 회원등급 정보 반환
function levelInfo( $level, $part='' ) {
	global $objdb;
	global $_NOLEVELNAME;

	$levelnum = str_replace( "level", "", $level );

	$sql = "SELECT * FROM memberlevel_tbl ".
			"WHERE level = '".$levelnum."'";
	$lev = $objdb->sqlRow( $sql );

	if( !$lev ) {
		$lev[level]			=	0;
		$lev[levelname]		=	$_NOLEVELNAME;
		$lev[levelmargin]		=	0;
	}

	if( $part ) return $lev[$part];
	else return $lev;
}

// 회원등급 체크
function getLevelPriv( $userLevel, $level ) {
	$levelnum = str_replace( "level", "", $userLevel );
	if( !$level ) $level = "n|1|2|3|4|5|6|7|8|9|10";
	$arr = explode( "|", $level );
	if( in_array( $levelnum, $arr ) ) return true;
	else return false;
}




###############################################################################
# 상품관련
###############################################################################
// 정렬
function getOrderBy( $kind ) {
	global $_SORT_SOLDOUT;
	global $_SORT, $_SORT_AUTO, $_SORT_AUTO_KIND, $_GOODSTOCK;

	$orderby = "ORDER BY ";

	if( $_SORT_SOLDOUT ) {
		$orderby .= "IF( ".( $_GOODSTOCK == 'y' ? 'stocknum < order_min OR ' : '' )."chksoldout = 'y', 1, 0 ) ASC, ";
	}

	if( $_SORT[$kind] == "a" ) $orderby .= $_SORT_AUTO[$kind]." ".$_SORT_AUTO_KIND[$kind];
	else {
		if( is_numeric( $kind ) ) $orderby .= "g.sortno ASC, g.wdate DESC";
		else if( $kind == "search" || $kind == "soldout" ) $orderby .= "g.wdate DESC";
		else $orderby .= "g.".$kind."sortno ASC, g.wdate DESC";
	}

	return $orderby;
}

// 상품공급업체 정보
function getSubAdminInfo( $id, $field='', $empty='' ) {
	global $objdb;

	$TBLSAT	=	"sub_admin_tbl";

	if( $field ) {
		$sql = "SELECT $field FROM $TBLSAT WHERE admin_id = '".$id."'";
		$rtn = $objdb->sqlRowOne( $sql );

		if( $rtn == "" ) $rtn = $empty;
	}
	else {
		$sql = "SELECT * FROM $TBLSAT WHERE admin_id = '".$id."'";
		$rtn = $objdb->sqlRow($sql);
	}

	return $rtn;
}

// 공동구매 만료 - 수량/기간
function cobuyEnd() {
	global $objdb;

	$TBLCO	=	"cobuy";
	$TBLG		=	"good";

	$chkChange = false;

	// 공동구매 시작
	$sql = "UPDATE $TBLCO SET ".
			"status = 'y' ".
			"WHERE status = 'n' ".
			"AND chkstartdate = 'y' ".
			"AND CONCAT( cbstartdate, ' ', cbstarttime ) <= SYSDATE()";
	$objdb->sqlExe( $sql );

	if( $objdb->sqlAffRows() > 0 ) $chkChange = true;

	// 공동구매 만료
	$sql = "UPDATE $TBLCO SET ".
			"status = 'e' ".
			"WHERE status = 'y' ".
			"AND ( ( chkenddate = 'y' AND CONCAT( cbenddate, ' ', cbendtime ) < SYSDATE() ) ".
			"OR ( chkstock = 'y' AND cbstock <= 0 ) )";
	$objdb->sqlExe( $sql );

	if( $objdb->sqlAffRows() > 0 ) $chkChange = true;

	if( $chkChange ) {
		$sql = "SELECT code FROM $TBLCO ".
				"WHERE status IN ( 'y', 'e' )";
		$codes = $objdb->sqlArrayOne( $sql );

		$sql = "UPDATE $TBLG SET ".
				"chkcobuy = IF( code IN ( ".arr2in( $codes )." ), 'y', 'n' )";
		$objdb->sqlExe( $sql );
	}
}

###############################################################################
# 디자인관련
###############################################################################
function get_str ( $str1, $str2 ) {
	if( $str1 ) return $str1;
	else return $str2;
}

function get_str_chk ( $chk, $str1, $str2 ) {
	if( $chk ) return $str1;
	else return $str2;
}

function make_img( $img, $width, $height=0, $style='border=0', $noneprint=false, $imgheight=false ) {
	global $DOCUMENT_ROOT, $objfunc;

	if( is_file( $DOCUMENT_ROOT.$img ) ) {
		$enc_img = eregi_replace( "%2F", "/", urlencode($img) );
		$enc_img = eregi_replace( "\+", "%20", $enc_img );
		if( $objfunc->chkFileExt($img) == "image" ) {
			$rtn = "<img src=\"".$enc_img."\" ".imgSize( $DOCUMENT_ROOT.$img, $width, ( $imgheight ? $height : 0 ) )." ".$style.">";
		}
		else if( $objfunc->chkFileExt($img) == "flash" ) {
			$rtn = $objfunc->flashLoadRtn( $enc_img, $width, $height );
		}
	}
	else if( $noneprint ) {
		$rtn = "<img width=\"".$width."\" ".$style." style='display:none'>";
	}

	return $rtn;
}

function get_design( $div ) {
	global $_ETCTOPTYPE, $_ETCTOP, $_ETCTOPHEIGHT, $_NAVI_SET, $_SUBLAYOUT;
	global $objdb;

	if( $_ETCTOPTYPE == "my" ) {
		if( is_file( $_SERVER["DOCUMENT_ROOT"]."/config/topimg.conf.php" ) ) {
			include $_SERVER["DOCUMENT_ROOT"]."/config/topimg.conf.php";

			$_SKIN_TOP = unserialize( stripslashes( $_MY_TOP ) );
			$_SKIN_TOP_HTML = unserialize( $_MY_TOP_HTML );
		}
	}
	else {
		include $_SERVER["DOCUMENT_ROOT"]."/skintype/skinconfig/topimg.conf.common.php";
	}

	// 상단이미지
	$design[top_img]		=	str_replace( "..", "", $_SKIN_TOP[$div] );
	$design[top_height]		=	$_ETCTOPHEIGHT;

	// 상단HTML
	$design[top_html]		=	stripslashes( $_SKIN_TOP_HTML[$div] );

	// 네비게이션
	$design[navi_set]		=	$_NAVI_SET;

	// 타이틀
	$title_img = str_replace( "/shopuser/", "", $_SERVER[PHP_SELF] );
	$title_img = str_replace( ".html", "", $title_img );
	$title_img = str_replace( "/", "_", $title_img );
	$design[title_img]		=	"/skintype/titleimage/type25/".$title_img.".gif";

	return $design;
}

function MakeCompanySearchPage() {
	global $objdb, $objfile;

	$sql = "SELECT DISTINCT company FROM good ".
			"WHERE chkuse = 'y' AND chkcate = 'y' AND company IS NOT NULL AND company != '' ".
			"ORDER BY BINARY( company ) ASC";
	$rs = $objdb->sqlResult( $sql );

	$buf = "";
	while( $row = $objdb->sqlFetch( $rs ) ) {
		if( $row[company] ) {
			$buf .= "<option value=\"".htmlText( $row[company] )."\">".$row[company]."</option>\n";
		}
	}

	$file_name = _DOCUMENT_ROOT_."/config/myCompanySearchPage.html";
	$objfile->write_file( $file_name, $buf );
}



###############################################
# 옵션관련
###############################################
function make_option( $option_set, $addedStyle='', $addedClass='' ) {
	$val = explode( "<:@:>", $option_set );

	if( $option_set || $addedStyle ) $type = "select";
	else $type = "text";

	if( $type == "select" ) {
		$str = "<select class='box $addedClass' $addedStyle>";
		foreach( $val as $k=>$v ) {
			$optArr = explode( "@", $v );
			if( $optArr[1] ) {
				if( $optArr[1] > 0 ) $char = "+";
				else $char = "";
				$added = " (".$char.number_format($optArr[1]).")";
			}
			else $added = "";

			if( $optArr[0] ) $str .= "<option value=\"".$v."\">".$optArr[0].$added."</option>";
		}
		$str .= "</select>";
	}
	else
		$str = "<input type=text class='box $addedClass'>";

	return $str;
}

function make_etcgood( $etcgood_set, $addedStyle='', $addedClass='' ) {
	$val = explode( "<:@:>", $etcgood_set );

	$str = "<select class='box $addedClass' $addedStyle>";
	foreach( $val as $k=>$v ) {
		$optArr = explode( "@", $v );
		if( $optArr[1] ) {
			if( $optArr[1] > 0 ) $char = "+";
			else $char = "";
			$added = " (".$char.number_format($optArr[1]).")";
		}
		else $added = "";
		$added .= " - 재고 ".number_format($optArr[2])."개";
		if( $optArr[3] ) $added .= " : SET ".$optArr[3];

		if( $optArr[0] ) $str .= "<option value=\"".$v."\">".$optArr[0].$added."</option>";
	}
	$str .= "</select>";

	return $str;
}

function get_selected_opt( $opt, $mainproperty ) {
	unset( $arr );
	if( $mainproperty ) {
		$recMainproperty = explode( "<:@:>", $mainproperty );
		for( $i = 0; $i < count($recMainproperty); $i++ ) {
			$mpArr = explode( "@", $recMainproperty[$i] );
			$arr[$i][name] = $mpArr[0];
			$arr[$i][value] = $mpArr[1];
			if( $mpArr[2] == "" ) $mpArr[2] = 0;
			$arr[$i][price] = $mpArr[2];
			$arr[$i][ng] = $mpArr[3];
		}
	}
	if( $opt ) {
		$recOpt = explode( "<:@:>", $opt );
		for( $j = 0; $j < count($recOpt); $j++ ) {
			$optArr = explode( "@", $recOpt[$j] );
			$arr[$i + $j][name] = $optArr[0];
			$arr[$i + $j][value] = $optArr[1];
			if( $optArr[2] == "" ) $optArr[2] = 0;
			$arr[$i + $j][price] = $optArr[2];
			$arr[$i + $j][ng] = $optArr[3];
		}
	}

	//$fullArr = array_merge( $recMainproperty, $recOpt );

	return $arr;
}

function get_selected_etc( $etcgood ) {
	unset( $arr );
	if( $etcgood ) {
		$recEtc = explode( "<:@:>", $etcgood );
		for( $i = 0; $i < count($recEtc); $i++ ) {
			$etcArr = explode( "@", $recEtc[$i] );
			$arr[$i][name] = $etcArr[0];
			$arr[$i][value] = $etcArr[1];
			if( $etcArr[2] == "" ) $etcArr[2] = 0;
			$arr[$i][price] = $etcArr[2];
		}
	}

	return $arr;
}

function print_buy_opt( $opt, $mp ) {
	$opt = get_selected_opt( $opt, $mp );
	$str = "";
	foreach( $opt as $k=>$v ) {
		if( $v[ng] == "y" ) $str .= "① ";
		$str .= $v[name]." : ".$v[value];
		if( $v[price] ) $str .= " - ".goodprice($v[price]);
		$str .= "<br>";
	}

	return $str;
}

function print_buy_etc( $etc ) {
	$opt = get_selected_etc( $etc );
	$str = "";
	foreach( $opt as $k=>$v ) {
		$str .= $v[name]." : ".$v[value];
		if( $v[price] ) $str .= " - ".goodprice($v[price]);
		$str .= "<br>";
	}

	return $str;
}

###############################################
# 공동구매 관련
###############################################
function get_cobuy_set( $idx=1 ) {
	global $objdb;

	$sql = "select * from cobuy_set where idx=".$idx;
	$co = $objdb->sqlRow($sql);

	return $co;
}

###############################################
# 어레이 관련
###############################################
function merge_array( $arr1, $arr2, $sepa='' ) {
	foreach( $arr1 as $k1=>$v1 ) {
		foreach( $arr2 as $k2=>$v2 ) {
			$tmparr[] = $v1.$sepa.$v2;
		}
	}

	return $tmparr;
}

function join_array( $arr, $sepa ) {
	$final = count( $arr ) - 1;
	for( $i = 0; $i < $final; $i++ ) {
		$arr[ $i + 1 ] = merge_array( $arr[$i], $arr[ $i + 1 ], $sepa );
	}

	return $arr[$final];
}

###############################################
# 사이트맵 관련
###############################################
function chkSiteMap( $menu_id ) {
	global $_SITEMAP;

	$arr = explode( "@", $_SITEMAP );
	if( in_array( $menu_id, $arr ) ) return true;
	else return false;
}

###############################################
# 디자인 관련
###############################################
function sup( $str, $class="" ) {
	if( $class ) $str = "<font class=".$class.">".$str."</font>";
	$str = "<sup>".$str."</sup>";

	return $str;
}

// 클래스를 적용시켜 출력
function classPrint($str, $class) {
	echo "<font class=".$class.">".$str."</font>";
}

// 리스트에서 각종 아이콘 표시할때 쓴다.
function getIconImg($icon, $basicimg) {
	$chk = $GLOBALS[$icon."_CHK"];
	$img = $GLOBALS[$icon."_IMG"];

	if( $chk == "y" ) return "/main_image/".$img;
	else return $basicimg;
}

###############################################
# 포인트 관련
###############################################

// 포인트 관리
function pointLog( $id, $part, $comment='', $point=0, $updown='y', $ordno='', $ref_idx=0, $from_id='' ) {
	global $objdb;
	global $_CHKPOINTUSE, $_BASEPOINT, $_POINTACT;

	if( !$id ) return;
	if( $_CHKPOINTUSE != "y" ) return;

	$TBLPL	=	"pointlog";
	$TBLM		=	"member";
	$TBLG		=	"good";

	$POINT = unserialize( stripslashes( $_POINTACT ) );

	switch( $part ) {
		case "join" :
			$realComment	=	"회원가입 : 회원 가입 감사 적립금";
			$realPoint			=	$_BASEPOINT;
			$realOrdno		=	"";
			$chkCnt			=	true;
			$chkOrdering		=	false;
			$chkOCode		=	"";
			$addedQry		=	"";
			$updateType		=	"";
			break;
		case "cc" :
			$realComment	=	"회원추천 : 회원 추천 감사 적립금 - ".$comment;
			$realPoint			=	$POINT[cc][point];
			$realOrdno		=	$comment;
			$chkCnt			=	true;
			$chkOrdering		=	false;
			$chkOCode		=	"";
			$addedQry		=	"";
			if( $POINT[cc][once] )		$chkCnt			=	true;
			else							$chkCnt			=	false;
			$updateType		=	"";
			break;
		case "order_cncl_used" :
			$realComment	=	"주문취소 : 사용한 적립금 환불 - ".$comment;
			$realPoint			=	$point;
			$realOrdno		=	$comment;
			$chkCnt			=	true;
			$chkOrdering		=	false;
			$chkOCode		=	"";
			$addedQry		=	"AND ordno = '".$realOrdno."'";
			$updateType		=	"";
			break;
		case "order_cncl_get" :
			$realComment	=	"주문취소 : 적립된 적립금 차감 - ".$comment;
			$realPoint			=	$point;
			$realOrdno		=	$comment;
			$chkCnt			=	true;
			$chkOrdering		=	false;
			$chkOCode		=	"";
			$addedQry		=	"AND ordno = '".$realOrdno."'";
			$updateType		=	"";
			break;
		case "order_used" :
			$realComment	=	"주문완료 : 사용한 적립금 차감 - ".$comment;
			$realPoint			=	$point;
			$realOrdno		=	$comment;
			$chkCnt			=	true;
			$chkOrdering		=	false;
			$chkOCode		=	"";
			$addedQry		=	"AND ordno = '".$realOrdno."'";
			$updateType		=	"";
			break;
		case "order_get" :
			$realComment	=	"주문완료 : 습득한 적립금 적립 - ".$comment;
			$realPoint			=	$point;
			$realOrdno		=	$comment;
			$chkCnt			=	true;
			$chkOrdering		=	false;
			$chkOCode		=	"";
			$addedQry		=	"AND ordno = '".$realOrdno."'";
			$updateType		=	"";
			break;
		case "rate" :
			$comArr			=	explode( "|", $comment );
			$sql = "SELECT name FROM good ".
					"WHERE code = '".$comArr[0]."' AND chkmain = 1";
			$gname = $objdb->sqlRowOne( $sql );
			$realComment	=	"상품평등록 : 상품평 적립금 - ".addslashes( strip_tags( $gname ) );
			$realPoint			=	$POINT[rate][point];
			$realOrdno		=	$comArr[1];
			$chkCnt			=	true;
			if( $POINT[rate][ordering] ) {
				$chkOrdering	=	true;
				$chkOCode	=	$comArr[0];
			}
			else {
				$chkOrdering	=	false;
				$chkOCode	=	"";
			}
			if( $POINT[rate][once] ) {
				$addedQry	=	"AND DATE_FORMAT( wdate, '%Y-%m-%d' ) = DATE_FORMAT( SYSDATE(), '%Y-%m-%d' )";
			}
			else {
				$addedQry	=	"AND ordno = '".$realOrdno."'";
			}
			$updateType		=	"goodrate";
			break;
		case "login" :
			$realComment	=	"회원로그인 : 로그인 감사 적립금";
			$realPoint			=	$POINT[login][point];
			$realOrdno		=	$GLOBALS[SESSION];
			if( $POINT[login][once] ) {
				$chkCnt		=	true;
				$chkOrdering	=	false;
				$chkOCode	=	"";
				$addedQry	=	"AND DATE_FORMAT( wdate, '%Y-%m-%d' ) = DATE_FORMAT( SYSDATE(), '%Y-%m-%d' )";
			}
			else {
				$chkCnt		=	false;
				$chkOrdering	=	false;
				$chkOCode	=	"";
				$addedQry	=	"";
			}
			$updateType		=	"";
			break;
		case "free" :
			$realComment	=	"게시판글등록 : 자유게시판 - ".$comment;
			$realPoint			=	$POINT[free][point];
			$realOrdno		=	$comment;
			$chkCnt			=	true;
			$chkOrdering		=	false;
			$chkOCode		=	"";
			if( $POINT[free][once] ) {
				$addedQry	=	"AND DATE_FORMAT( wdate, '%Y-%m-%d' ) = DATE_FORMAT( SYSDATE(), '%Y-%m-%d' )";
			}
			else {
				$addedQry	=	"AND ordno = '".$realOrdno."'";
			}
			$updateType		=	"free";
			break;
		case "qna" :
			$realComment	=	"게시판글등록 : Q&A게시판 - ".$comment;
			$realPoint			=	$POINT[qna][point];
			$realOrdno		=	$comment;
			$chkCnt			=	true;
			$chkOrdering		=	false;
			$chkOCode		=	"";
			if( $POINT[qna][once] ) {
				$addedQry	=	"AND DATE_FORMAT( wdate, '%Y-%m-%d' ) = DATE_FORMAT( SYSDATE(), '%Y-%m-%d' )";
			}
			else {
				$addedQry	=	"AND ordno = '".$realOrdno."'";
			}
			$updateType		=	"qna";
			break;
		case "admin" :
			$realComment	=	$comment;
			$realPoint			=	$point;
			$realOrdno		=	$ordno;
			$chkCnt			=	false;
			$chkOrdering		=	false;
			$chkOCode		=	"";
			$addedQry		=	"";
			$updateType		=	"";
			break;
		default :
			$sql = "SELECT title FROM bbs_manager WHERE id = '".$part."'";
			$bbsname = $objdb->sqlRowone( $sql );

			if( $bbsname ) {
				$realComment	=	"게시판글등록 : ".$bbsname." - ".$comment;
				$realPoint			=	$POINT[$part][point];
				$realOrdno		=	$comment;
				$chkCnt			=	true;
				$chkOrdering		=	false;
				$chkOCode		=	"";
				if( $POINT[$part][once] ) {
					$addedQry	=	"AND DATE_FORMAT( wdate, '%Y-%m-%d' ) = DATE_FORMAT( SYSDATE(), '%Y-%m-%d' )";
				}
				else {
					$addedQry	=	"AND ordno = '".$realOrdno."'";
				}
				$updateType		=	$part;
			}
			else {
				$realComment	=	$comment;
				$realPoint			=	$point;
				$realOrdno		=	$ordno;
				$chkCnt			=	false;
				$chkOrdering		=	false;
				$chkOCode		=	"";
				$addedQry		=	"";
				$updateType		=	"";
			}
	}

	if( !$realPoint ) return;

	if( _SHOPADMIN !== true ) {
		if( $updateType && $POINT[$part][type] ) {
			switch( $updateType ) {
				case "qna" :
				case "free" :
				case "goodrate" :
					$sql = "UPDATE ".$updateType." SET ".
							"point_ok = 0 ".
							"WHERE no = '".$realOrdno."'";
					$objdb->sqlExe( $sql );
					break;
				default :
					$sql = "UPDATE bbs SET ".
							"point_ok = 0 ".
							"WHERE bbsid = '".$updateType."' AND no = '".$realOrdno."'";
					$objdb->sqlExe( $sql );
					break;
			}

			return;
		}

		if( $chkCnt ) {
			$sql = "SELECT COUNT( * ) FROM pointlog ".
					"WHERE id = '".$id."' AND part = '".$part."' AND chkupdown = '".$updown."' ".$addedQry;
			$cnt = $objdb->sqlRowOne( $sql );

			if( $cnt ) return;
		}

		if( $chkOrdering ) {
			$sql = "SELECT COUNT( * ) FROM ordering o, ordered_good og ".
					"WHERE o.ordno = og.ordno AND og.code = '".$chkOCode."' AND o.orderid = '".$id."' AND o.state != '취소완료'";
			$cnt = $objdb->sqlRowOne( $sql );

			if( !$cnt ) return;
		}
	}

	$sql = "SELECT MAX( no ) FROM pointlog";
	$maxno = $objdb->sqlMaxNo( $sql );

	$sql = "INSERT INTO pointlog ( ".
			"no, id, part, comment, point, chkupdown, wdate, ordno, ref_idx, from_id ".
			") VALUES ( ".
			"'".$maxno."', '".$id."', '".$part."', '".$realComment."', '".$realPoint."', '".$updown."', SYSDATE(), '".$realOrdno."', '".$ref_idx."', '".$from_id."' ".
			")";
	$objdb->sqlExe( $sql );

	$pm = $updown == "y" ? "+" : "-";

	$sql = "UPDATE member SET point = point $pm $realPoint WHERE id = '".$id."'";
	$objdb->sqlExe( $sql );

	if( $updateType ) {
		switch( $updateType ) {
			case "qna" :
			case "free" :
			case "goodrate" :
				$sql = "UPDATE ".$updateType." SET ".
						"point_ok = 1 ".
						"WHERE no = '".$realOrdno."'";
				$objdb->sqlExe( $sql );
				break;
			default :
				$sql = "UPDATE bbs SET ".
						"point_ok = 1 ".
						"WHERE bbsid = '".$updateType."' AND no = '".$realOrdno."'";
				$objdb->sqlExe( $sql );
				break;
		}
	}

	return $maxno;
}

// 게시판 관련 지급포인트 차감
function pointLogCancel( $id, $part, $ordno ) {
	global $objdb;

	if( !$id )			return;
	if( !$part )		return;
	if( !$ordno )		return;

	switch( $part ) {
		case "rate" :
			$comment = "상품평삭제 : 상품평 적립금 - ".$ordno;
			break;
		case "qna" :
			$comment = "게시판글삭제 : Q&A게시판 - ".$ordno;
			break;
		case "free" :
			$comment = "게시판글삭제 : 자유게시판 - ".$ordno;
			break;
		default :
			$sql = "SELECT title FROM bbs_manager WHERE id = '".$part."'";
			$bbsname = $objdb->sqlRowOne( $sql );

			if( $bbsname ) {
				$comment = "게시판글삭제 : ".$bbsname." - ".$ordno;
			}
			else {
				return;
			}
			break;
	}

	$sql = "SELECT point FROM pointlog ".
			"WHERE id = '".$id."' AND part = '".$part."' AND ordno = '".$ordno."' AND chkupdown = 'y'";
	$point = $objdb->sqlRowOne( $sql );

	if( !$point ) return;

	$sql = "SELECT MAX( no ) FROM pointlog";
	$maxno = $objdb->sqlMaxNo( $sql );

	$sql = "INSERT INTO pointlog ( ".
			"no, id, part, comment, point, chkupdown, wdate, ordno ".
			") VALUES ( ".
			"'".$maxno."', '".$id."', '".$part."', '".$comment."', '".$point."', 'n', SYSDATE(), '".$ordno."' ".
			")";
	$objdb->sqlExe( $sql );

	$sql = "UPDATE member SET ".
			"point = point - $point ".
			"WHERE id = '".$id."'";
	$objdb->sqlExe( $sql );
}



###############################################
# 재고 관련
###############################################

function updateGoodStock( $code, $qty=1, $pm='+', $mp='', $chkcobuy='n' ) {
	global $_GOODSTOCK;
	global $objdb;

	// 재고
	if( $_GOODSTOCK == "y" ) {
		if( !empty( $mp ) ) {
			$mpARR = explode( "<:@:>", $mp );

			foreach( $mpARR as $k => $v ) {
				$mainpropertyARR = explode( "@", $v );
				$chkARR[] = $mainpropertyARR[1];
			}

			$sql = "SELECT * FROM good_option_stock WHERE code = '".$code."'";
			$rs = $objdb->sqlResult( $sql );
			while( $stock = $objdb->sqlFetch( $rs ) ) {
				$stockARR = explode( "*", $stock[optionkey] );

				$diff = array_diff( $stockARR, $chkARR );
				if( count( $diff ) == 0 ) {
					$optionKey	=	$stock[optionkey];
				}
			}

			if( $optionKey ) {
				$sql = "UPDATE good_option_stock SET optionnum = optionnum $pm $qty WHERE code = '".$code."' and optionkey = '".$optionKey."'";
				$objdb->sqlExe( $sql );
			}
		}

		$sql = "UPDATE good SET stocknum = stocknum $pm $qty WHERE code = '".$code."'";
		$objdb->sqlexe( $sql );
	}

	// 공동구매
	if( $chkcobuy == "y" ) {
		$sql = "UPDATE cobuy SET cbstock = cbstock $pm $qty WHERE code = '".$code."'";
		$objdb->sqlexe( $sql );
	}

	$pm_sale = $pm == "+" ? "-" : "+";
	$sql = "UPDATE good SET salenum = salenum $pm_sale $qty WHERE code = '".$code."'";
	$objdb->sqlExe($sql);

	if( defined( '__PCMALL__' ) )
		spec_soldout_update( $code );
}

function insertGoodStockLog( $code, $qty=1, $pm='+', $memo='', $ordno='' ) {
	global $_GOODSTOCK;
	global $objdb;

	if( $code == "added" ) return;

	if( $_GOODSTOCK == "y" ) {
		// 3개월 이내의 로그 삭제
		$sql = "DELETE FROM good_stock_log WHERE wdate <= DATE_SUB( sysdate(), interval 3 month )";
		$objdb->sqlExe($sql);

		// 현재 상품의 재고를 가져옴
		$sql = "SELECT stocknum FROM good WHERE code = '".$code."'";
		$stocknum = $objdb->sqlRowOne($sql);

		$sql = "INSERT INTO good_stock_log ( code, pm, qty, stocknum, memo, ordno, wdate ) VALUES ( '".$code."', '".$pm."', '".$qty."', '".$stocknum."', '".$memo."', '".$ordno."', sysdate() )";
		$objdb->sqlExe($sql);
	}
}



###############################################
# 게시판 관련
###############################################

// 금지단어
function cautionWords( $parseStr, $cautionWords ) {
	$rtn = "";

	if( $cautionWords ) {
		$cautionWordsARR = explode( ",", $cautionWords );

		foreach( $cautionWordsARR as $k => $word ) {
			if( @eregi( $word, $parseStr ) ) {
				$rtn = $word;
				break;
			}
		}
	}

	return $rtn;
}



###############################################
# 주문 관련
###############################################

function insertOrderingLog( $ordno, $type, $content ) {
	global $_SESSION_USERID;
	global $objdb;

	if( _SHOPADMIN ) $log_adminid = $_SESSION_USERID;
	else $log_adminid = "";

	$TBLOL	=	"ordering_log";

	$sql = "INSERT INTO $TBLOL ( ".
			"ordno, log_type, log_content, log_date, log_adminid, log_ip ".
			") VALUES ( ".
			"'".$ordno."', '".$type."', '".$content."', SYSDATE(), '".$log_adminid."', '"._REMOTE_ADDR_."' ".
			")";
	$objdb->sqlExe( $sql );
}




###############################################
# 관리자 관련
###############################################

// 최근 몇시간내인가?
function printNew( $datetime, $rtn_img, $new_period='24' ) {
	$period = $new_period * 60 * 60;

	if( ( mktime() - strtotime( $datetime ) ) <= $period ) {
		$rtn = "<img src=\"".$rtn_img."\" align=absmiddle>";
	}
	else {
		$rtn = "";
	}

	return $rtn;
}

// 날짜 셀렉트 표시
function printYear( $name, $cur='', $novalue='' ) {
	$str = "";
	$curYear = date( "Y" );
	$str .= "<select name='".$name."' class=box>";
	if( $novalue ) $str .= "<option value=''>".$novalue."</option>";
	for( $i = $curYear; $i <= $curYear + 3; $i++ ) {
		if( $cur == $i ) $added = " selected";
		else $added = "";
		$str .= "<option value='".$i."' ".$added.">".$i."</option>";
	}
	$str .= "</select>";

	return $str;
}


function printMonth( $name, $cur='', $novalue='' ) {
	$str = "";
	$str .= "<select name='".$name."' class=box>";
	if( $novalue ) $str .= "<option value=''>".$novalue."</option>";
	for( $i = 1; $i <= 12; $i++ ) {
		if( strlen( $i ) == 1 ) $val = "0".$i;
		else $val = $i;
		if( $cur == $val ) $added = " selected";
		else $added = "";
		$str .= "<option value='".$val."' ".$added.">".$val."</option>";
	}
	$str .= "</select>";

	return $str;
}

function printDay( $name, $cur='', $novalue='' ) {
	$str = "";
	$str .= "<select name='".$name."' class=box>";
	if( $novalue ) $str .= "<option value=''>".$novalue."</option>";
	for( $i = 1; $i <= 31; $i++ ) {
		if( strlen( $i ) == 1 ) $val = "0".$i;
		else $val = $i;
		if( $cur == $val ) $added = " selected";
		else $added = "";
		$str .= "<option value='".$val."' ".$added.">".$val."</option>";
	}
	$str .= "</select>";

	return $str;
}

function printJob( $name, $cur='', $novalue='' ) {
	global $_JOB;

	$str = "";
	$str .= "<select name='".$name."' class=box>";
	if( $novalue ) $str .= "<option value=''>".$novalue."</option>";
	foreach( $_JOB as $k=>$val ) {
		if( $cur == $val ) $added = " selected";
		else $added = "";
		$str .= "<option value='".$val."' ".$added.">".$val."</option>";
	}
	$str .= "</select>";

	return $str;
}

// 금일로부터 며칠전?
function get_date( $period, $type='d' ) {
	switch( $type ) {
		case "d" :
			$newdate = date( "Y-m-d", mktime( 0, 0, 0, date( "m" ), date( "d" ) - $period, date( "Y" ) ) );
			break;
		case "m" :
			$newdate = date( "Y-m-d", mktime( 0, 0, 0, date( "m" ) - $period, date( "d" ), date( "Y" ) ) );
			break;
		case "Y" :
			$newdate = date( "Y-m-d", mktime( 0, 0, 0, date( "m" ), date( "d" ), date( "Y" ) - $period ) );
			break;
	}

	return $newdate;
}

function get_date_pm( $date, $pm, $pm_type='d' ) {
	global $objfunc;

	$rtn = $objfunc->getDatePM( $date, $pm, $pm_type );

	return $rtn;
}

function date_diff( $start, $end, $split='-' ) {
	$start_arr = explode( $split, $start );
	$startTime = mktime( 0, 0, 0, $start_arr[1], $start_arr[2], $start_arr[0] );

	if( is_numeric( $end ) ) $end = date( "Y".$split."m".$split."d", mktime( 0, 0, 0, $start_arr[1], $start_arr[2] + $end, $start_arr[0] ) );

	$end_arr = explode( $split, $end );
	$endTime = mktime( 0, 0, 0, $end_arr[1], $end_arr[2], $end_arr[0] );

	$rtn = ( $endTime - $startTime ) / ( 60 * 60 * 24 );

	return $rtn;
}

// 무슨요일인지 반환
function get_weekday( $date ) {
	$week_arr = array( "일", "월", "화", "수", "목", "금", "토" );
	$date_arr = explode( "-", $date );
	$weekday = date( "w", mktime( 0, 0, 0, $date_arr[1], $date_arr[2], $date_arr[0] ) );

	$rtn = $week_arr[$weekday];

	return $rtn;
}

###############################################
# 기타등등
###############################################

function makeSeed() {
	list( $usec, $sec ) = explode( ' ', microtime() );
	return (float) $sec + ( (float) $usec * 100000 );
}

function makeRand( $start, $end ) {
	mt_srand( makeSeed() );
	$rtn = mt_rand( $start, $end );

	return $rtn;
}

function dearHash( $str, $length=33, $passtype='' ) {
	if( $passtype && function_exists( $passtype ) ) {
		$rtn = substr( $passtype( $str ), 0, $length );
	}
	else {
		$rtn = substr( md5( "dearmall".$str."#1111" ), 0, $length );
	}

	return $rtn;
}

function makeHash( $length=4 ) {
	$rtn = substr( md5( uniqid( rand() ) ), 0, $length );

	return $rtn;
}

// 스트링 => script의 value
function setScriptValue( $str ) {
	$rtn = addslashes( $str );
	$rtn = str_replace( "\r\n", "\\n", $rtn );
	$rtn = str_replace( "\n", "\\n", $rtn );

	return $rtn;
}

function download( $file, $dir, $str='', $target='exeFrame' ) {
	global $objfunc;

	if( !$str ) $str = $file;
	$rtn = "<a href='/common/download.php?file=".rawurlencode($file)."&dir=".$dir."' target=".$target.">".$str."</a>";

	return $rtn;
}

function str2star( $str, $showcnt=0, $star='*' ) {
	$noChk = array( " ", "\n", "\r" );

	$rtn = "";
	for( $i = 0; $i < strlen( $str ); $i++ ) {
		$val = substr( $str, $i, 1 );

		if( $showcnt && $i < $showcnt ) $rtn .= $val;
		else if(  in_array( $val, $noChk ) ) $rtn .= $val;
		else $rtn .= $star;
	}

	return $rtn;
}

function in_array_args() {
	$args = func_get_args();

	unset( $chk );

	foreach( $args as $k => $v ) {
		if( in_array( $v, $chk ) ) return true;

		$chk[] = $v;
	}

	return false;
}

// 어레이를 mysql in 구문에 넣을수 있도록 변환한다.
function arr2in( $arr ) {
	if( is_array($arr) && count($arr) ) $tmp = implode( "','" ,$arr );
	else $tmp = " ";
	$tmp = eregi_replace( "^|$", "'", $tmp );

	return $tmp;
}

function set2in( $set, $sep='|' ) {
	$set = str_replace( $sep, "','", $set );
	$set = eregi_replace( "^|$", "'", $set );

	return $set;
}

function arr2get( $arr, $name ) {
	$arr = array_work( $arr, "ue" );

	if( is_array( $arr ) && count( $arr ) ) $rtn = "&".$name."[]=".implode( "&".$name."[]=" ,$arr );

	return $rtn;
}

function getmicrotime () {
	list( $usec, $sec ) = explode( " ", microtime() );

	return ((float)$usec + (float)$sec);
}

// 배열값에 특정함수를 실행시킨다.
function array_work( $arr, $func ) {
	if( !is_array( $arr ) ) return "";

	foreach( $arr as $k => $v ) {
		$rtn[$k] = $func( $v );
	}

	return $rtn;
}

function keywordReplace( $keyword, $str, $class='red' ) {
	$keyword = "/".preg_quote( $keyword, "/" )."/i";

	$rtn = preg_replace( $keyword, "<font class=".$class.">\\0</font>", $str );

	return $rtn;
}

function htmlContentReplace( $content ) {
	$content	=	eregi_replace( "<script([^>]*>[^</script>]*</)script>", "<MYSCRIPT\\1MYSCRIPT>", $content );
	$content	=	eregi_replace( "<form([^>]*>.*</)form>", "<MYFORM\\1MYFORM>", $content );

	return $content;
}

function space2br( $str ) {
	$rtn = str_replace( " ", "<BR>", $str );

	return $rtn;
}

function htmlText( $text, $htmlcheck='t' ) {
	global $objfunc;

	$rtn = $objfunc->htmlText( $text, $htmlcheck );
	
	return $rtn;
}

function htmlTextG( $text ) {
	global $objfunc;

	$rtn = $objfunc->htmlText( $text, "g" );
	
	return $rtn;
}

function ue( $text ) {
	$rtn = urlencode( $text );

	return $rtn;
}

function ue4js( $text ) {
	$rtn = rawurlencode( iconv( "EUC-KR", "UTF-8", $text ) );

	return $rtn;
}

function parseUrl( $url, $part ) {
	$arr = parse_url( $url );

	$rtn = $arr[$part];

	return $rtn;
}

function click2copy( $cp_str, $shw_str, $class='' ) {
	$rtn = "<a href=\"javascript:click2copy( '".$cp_str."' );\">".
			( $class ? "<font class='".$class."'>" : "" ).
			$shw_str.
			( $class ? "</font>" : "" ).
			"</a>";

	return $rtn;
}

function click2copy_s( $str, $class='' ) {
	$rtn = click2copy( $str, $str, $class );

	return $rtn;
}

function only_number( $str ) {
	$rtn = eregi_replace( "[^0-9]", "", $str );

	return $rtn;
}



###############################################
# CURL 관련
###############################################

function load_url( $url ) {
	include_once _DOCUMENT_ROOT_."/lib/curl.php";

	$curl = new CURL_CLASS( $url, "DEARMALL" );

	$rtn = $curl->execute();

	if( is_int( $rtn ) && $rtn ) $rtn = "";
	if( $rtn === true ) $rtn = "";
	if( $curl->get_http_code() != 200 ) $rtn = "";

	unset( $curl );

	return $rtn;
}

function load_url_with_post_with_array( $url, $arr ) {
	include_once _DOCUMENT_ROOT_."/lib/curl.php";

	$curl = new CURL_CLASS( $url, "DEARMALL" );

	$curl->type = "post";
	$curl->fields = $curl->postdata_encode( $arr );

	$rtn = $curl->execute();

	if( is_int( $rtn ) && $rtn ) $rtn = "";
	if( $rtn === true ) $rtn = "";
	if( $curl->get_http_code() != 200 ) $rtn = "";

	unset( $curl );

	return $rtn;
}





###############################################
# SUBMIT 관련
###############################################

function var_not_chk() {
	$rtn = false;

	$args = func_get_args();

	foreach( $args as $k => $v ) {
		if( !$v ) $rtn = true;
	}

	return $rtn;
}

function var_empty_chk() {
	$rtn = false;

	$args = func_get_args();

	foreach( $args as $k => $v ) {
		if( $v == "" ) $rtn = true;
	}

	return $rtn;
}

function post_var_chk( $var, $chk='' ) {
	global $_POST;

	if( isset( $_POST[$var] ) && $_POST[$var] == $chk ) $rtn = true;
	else $rtn = false;

	return $rtn;
}

function post_var_trim() {
	global $_POST;

	array_walk( $_POST, '_array_trim' );
}

function _array_trim( &$v, $k ) {
	$v = trim( $v );
}

function form_empty_chk( $var, $only_empty=false, $trim=true ) {
	global $_POST;

	$value = $trim ? trim( $_POST[$var] ) : $_POST[$var];

	if( $only_empty ) $rtn = $value != "" ? true : false;
	else $rtn = $value ? true : false;

	return $rtn;
}

function form_length_chk( $var, $min=0, $max=0, $trim=true ) {
	global $_POST;

	$value = $trim ? trim( $_POST[$var] ) : $_POST[$var];

	$rtn = true;

	if( $min && strlen( $value ) < $min ) $rtn = false;
	if( $max && strlen( $value ) > $max ) $rtn = false;

	return $rtn;
}

function form_regexp_chk( $var, $regexp='', $trim=true ) {
	global $_POST;

	$value = $trim ? trim( $_POST[$var] ) : $_POST[$var];

	$rtn = false;

	if( $regexp && ereg( $regexp, $value ) ) $rtn = true;

	return $rtn;
}

function form_same_chk( $var1, $var2, $trim=true ) {
	global $_POST;

	$value1 = $trim ? trim( $_POST[$var1] ) : $_POST[$var1];
	$value2 = $trim ? trim( $_POST[$var2] ) : $_POST[$var2];

	$rtn = false;

	if( $value1 == $value2 ) $rtn = true;

	return $rtn;
}





###############################################
# 파일 관련 함수
###############################################

function get_explode( $sepa, $str, $key='' ) {
	$arr = explode( $sepa, $str );

	if( $key !== "" ) $rtn = $arr[$key];
	else $rtn = $arr;

	return $rtn;
}

function explode_file( $file ) {
	$rtn = array();

	if( $file ) {
		$files = get_explode( ".", $file );
		$ext = $files[count( $files ) - 1];
		$name = eregi_replace( "[.]".$ext."$", "", $file );

		$rtn[name] = $name;
		$rtn[ext] = $ext;
	}

	return $rtn;
}

function get_file_ext( $file ) {
	$file = basename( $file );

	$arr = explode_file( $file );

	return $arr[ext];
}

function get_file_ext_type( $file ) {
	$ext = strtolower( get_file_ext( $file ) );

	switch( $ext ) {
		case 'jpg' :
		case 'jpeg' :
		case 'gif' :
		case 'bmp' :
		case 'png' :
			$rtn = 'image';
			break;
		case 'html' :
		case 'php' :
		case 'htm' :
			$rtn = 'html';
			break;
		case 'avi' :
		case 'mpg' :
		case 'mpeg' :
		case 'mov' :
		case 'asf' :
		case 'wmv' :
			$rtn = 'movie';
			break;
		case 'xls' :
			$rtn = 'excel';
			break;
		case 'swf' :
			$rtn = 'flash';
			break;
	}

	return $rtn;
}





###############################################
# 카테고리 그룹바이를 위한 실험적 함수 세트 by 정실장 in 2007-06-19
# 일정기간의 테스트 후 생존여부를 결정 예정
###############################################

function setCateGroupBy() {
	global $objdb;
	global $objfile;

	$group_by_dir = $_SERVER["DOCUMENT_ROOT"]."/config/groupby_tmp";

	// 디렉토리 체크
	if( !is_dir( $group_by_dir ) ) @mkdir( $group_by_dir, 0777 );

	// 기존 파일 삭제
	$dir = @dir( $group_by_dir );
	while( false !== $entry = $dir->read() ) {
		$objfile->deleteFile( $group_by_dir."/".$entry );
	}

	// 미생성파일 - 대분류
	$sql = "SELECT largeno FROM good ".
			"WHERE largeno > 0 ".
			"GROUP BY largeno, code ".
			"HAVING COUNT( * ) > 1";
	$files_l = $objdb->sqlArrayOne( $sql );

	$files_l = array_unique( $files_l );

	// 미생성 파일 - 중분류
	$sql = "SELECT CONCAT( largeno, '_', middleno ) FROM good ".
			"WHERE largeno > 0 AND middleno > 0 ".
			"GROUP BY largeno, middleno, code ".
			"HAVING COUNT( * ) > 1";
	$files_m = $objdb->sqlArrayOne( $sql );

	$files_m = array_unique( $files_m );

	// 카테고리
	$sql = "SELECT ".
			"IF( middleno > 0, CONCAT( largeno, '_', middleno ), largeno ) ".
			"FROM category ".
			"WHERE largeno > 0";
	$files = $objdb->sqlArrayOne( $sql );

	// 파일 생성
	foreach( $files as $k => $file ) {
		if( in_array( $file, $files_l ) ) continue;
		if( in_array( $file, $files_m ) ) continue;

		$filename = $group_by_dir."/".$file;

		if( !is_file( $filename ) ) $objfile->fileWrite( $filename, "" );
	}
}

function getCateGroupBy( $largeno=0, $middleno=0, $smallno=0 ) {
	$group_by_dir = $_SERVER["DOCUMENT_ROOT"]."/config/groupby_tmp";

	$rtn = true;

	if( $largeno && $middleno && $smallno ) {
		$rtn = false;
	}
	else if( $largeno && $middleno ) {
		if( is_file( $group_by_dir."/".$largeno."_".$middleno ) ) $rtn = false;
	}
	else if( $largeno ) {
		if( is_file( $group_by_dir."/".$largeno ) ) $rtn = false;
	}
	else {
		$rtn = false;
	}

	return $rtn;
}

function LoginRedirect( $type='' ) {
	global $objfunc;
	if( $type == 'member' ){
		$url = "/member/login.html";
		$_LOGIN_REDIRECT_MSG = "회원페이지입니다\\n로그인하시겠습니까?";
		$objfunc->confirmUrl( $url, $_LOGIN_REDIRECT_MSG );
	} else {
		$_LOGIN_REDIRECT_MSG = '정상적인 접근이 아닙니다.';
		$objfunc->alertBack( $_LOGIN_REDIRECT_MSG );	
	}
}
?>