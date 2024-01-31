<?php
class FUNC_CLASS {
	// 특정 레이어에 문자열 밀어넣기
	function putStr($layerid, $str) {
		echo "<script language=javascript>
		<!--
		$layerid.innerHTML=\"$str\";
		//-->
		</script>";
	}
	
	// 확장자 검사
	function chkFileExt( $file ) {
		if( eregi( "(\.jpg|\.jpeg|\.gif|\.bmp|\.png)$", $file ) ) {
			$ext = "image";
		}
		else if( eregi( "(\.swf|\.SWF)$", $file ) ) {
			$ext = "flash";
		}
		else if( eregi( "(\.avi|\.mpg|\.mpeg|\.mpg|\.asf|\.mov|\.wmv)$", $file ) ) {
			$ext = "movie";
		}
		else if( eregi( "(\.xls)$", $file ) ) {
			$ext = "excel";
		}

		return $ext;
	}
	
	
	
	####################
	# 태그 포함 문자열 자르기
	####################

	function tagexplode( $text, &$htmltag, &$htmltext ) {
		$a		=	eregi_replace( "^([^>]*>){1}.*", "\\1", $text );	// tag
		$text	=	eregi_replace( "^[^>]*>{1}(.*)", "\\1", $text );
		if( eregi( "<", $text ) && !eregi( ">", $text ) ) {				// 문자열 "<" 이 있고 ">" 이 없다면 "<" 를 "&lt;" 로 치환
			$text = eregi_replace( "<", "&lt;", $text );
		}

		$b		=	eregi_replace( "^([^<]*){1}.*$", "\\1", $text );		//string
		$text	=	eregi_replace( "^([^<]*){1}(.*)$", "\\2", $text );

		$htmltag[]	=	$a;
		$htmltext[]	=	$b;

		if( strlen( $text ) > 0 ) {
			$this->tagexplode( $text, $htmltag, $htmltext );
		}
	}

	function str_cut( $str, $len ) {
		if( strlen( $str ) > $len ) {
			$str	=	substr( $str, 0, $len );
			$str	=	preg_replace( "/(([\x80-\xFE].)*)[\x80-\xFE]?$/", "\\1", $str );
			$str	.=	"..";
		}

		return $str;
	}

	function hanCut( $text, $len ) {
		$regi = "<.[^>]*>";
		if( eregi( $regi, $text ) ) {
			$this->tagexplode( $text, $tag, $str );
			if( is_array( $tag ) ) {
				$intag = eregi_replace( $regi, "", $tag[0] );	 // 첫번째 인덱스에 tag가 같이 있는지?
				if( $intag ) {										 // 테그가 있다면 인덱스 재정렬
					$arrcnt = count( $tag );
					for( $i = 0; $i < $arrcnt; $i++ ) {
						$tag[$arrcnt - 1 - $i + 1]		= $tag[$arrcnt - 1 - $i];
						$str[$arrcnt - 1 - $i + 1]		= $str[$arrcnt - 1 - $i];
					}

					$tag[0]	=	"";
					$str[0]	=	$intag;
					$tag[1]	=	@eregi_replace( $intag, "", $tag[1] );
				}
			}

			$cstr	=	'';
			$tstr	=	'';
			$tlen	=	$len;
			$idx	=	0;
			for( $i = 0; $i < count( $tag ); $i++ ) {		// 잘라야할 인덱스
				if( strlen( $cstr ) > $len ) {
					$cstr		.=	$str[$i];
					$str[$i]	=	'';
				}
				else {
					$cstr	.=	$str[$i];
					if( $str[$i - 1] != '' ) { 
						$tlen = $tlen - strlen( $str[$i - 1] );
					}
					$tstr	=	$str[$i];
					$idx	=	$i;
				}
			}

			$str[$idx] = $this->str_cut( $tstr, $tlen );

			$rtntext = '';
			for( $i = 0; $i < count( $tag ); $i++ ) {		// 재분배
				$rtntext .= $tag[$i].$str[$i];
			}
		}
		else {
			$rtntext = $this->str_cut( $text, $len );
		}

		return $rtntext;
	}

	####################



	####################
	# 태그포함 문자열 자르기 experimental version by 정실장 in 2007-01-29
	# 비태그영역에 대해 스페셜캐릭터 처리 by 정실장 in 2007-07-31
	# 정실장 외에는 사용을 권장하지 않는다.
	####################

	function hanCutWithTag( $text, $len ) {
		$arr = preg_split( "/(<[^>]*>)/", $text, -1, PREG_SPLIT_DELIM_CAPTURE );	

		unset( $rtn );

		foreach( $arr as $k => $str ) {
			if( eregi( "^<[^>]*>$", $str ) ) $rtn[] = $str;
			else {
				if( $len > 0 ) {
					if( strlen( $str ) > $len ) {
						$rtn[] = $this->str_cut( $str, $len );
					}
					else {
						$rtn[] = $str;
					}

					$len -= strlen( $str );
				}
			}
		}

		$rtntext = implode( "", $rtn );
	
		return $rtntext;
	}

	####################




	function htmlText( $text, $htmlcheck='t' ) {
		if( $htmlcheck == "y" ) {
			$text  = stripslashes( $text );
		}
		else if( $htmlcheck == "t" ) {
			$text = htmlspecialchars( $text );
			//$text = stripslashes( $text );
		}
		else if( $htmlcheck == "g" ) {
			$text = stripslashes( $text );
			$text = htmlspecialchars( $text );
		}
		else {
			$text = stripslashes( $text );
			$text = htmlspecialchars( $text );
			$text = nl2br( $text );
		}
		
		return $text;
	}

	function sendMail( $to, $from, $fromname, $subject, $body, $htmlcheck, $clipfile='' ) {
		global $dear;
		global $objfile;

		if( $dear[settingstate] != "서비스중" && $dear[settingstate] != "자체운영" ) return;


		$content = $_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"]." - ".$subject."\n";
		$objfile->fileWrite( "/tmp/maillog_".date( "Ymd" ), $content, "a" );

		$fromname = strip_tags( $fromname );

		$body = $this->htmlText( $body, $htmlcheck );
		
		$boundary = "----=_".uniqid( "part" );
		
		$header	=				"Return-Path: ".$from."\n";
		$header	.=				"From:  \"".$fromname."\"<".$from.">\n";
		$header	.=				"X-Mailer: DearMALL MAIL ".
									"from ".$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"]." ".
									"at ".$_SERVER["REMOTE_ADDR"].( $GLOBALS["_USERID"] ? "(".$GLOBALS["_USERID"].")" : "" ).
									"\n";
		$header	.=				"Reply-To: ".$from."\n";

		if( $clipfile && $clipfile[name] != "" && $clipfile[size] !="" ) {
			$header		.=		"MIME-Version: 1.0\n";
			$header		.=		"Content-Type: multipart/mixed;\n";
			$header		.=		"	boundary=\"".$boundary."\"\n\n";

			$bodytext		=		"This is a multi-part message in MIME format.\n\n";
			$bodytext		.=		"--".$boundary."\n";
			$bodytext		.=		"Content-Type: text/html; charset=\"ks_c_5601-1987\"\n";
			$bodytext		.=		"Content-Transfer-Encoding: base64\n\n";
			$bodytext		.=		chunk_split( base64_encode($body) )."\n\n";

			$filename = basename( $clipfile[name] );
			$fp = fopen( $clipfile[tmp_name], "r" );
			$file = fread( $fp, $clipfile[size] );
			fclose( $fp );

			$bodytext		.=		"--".$boundary."\n";
			$bodytext		.=		"Content-Type: ".$clipfile[type].";\n";
			$bodytext		.=		"	name=\"".$filename."\"\n";
			$bodytext		.=		"Content-Transfer-Encoding: base64\n";
			$bodytext		.=		"Content-Disposition: attachment;\n";
			$bodytext		.=		"	filename=\"".$filename."\"\n\n";
			$bodytext		.=		chunk_split( base64_encode( $file ) )."\n\n";
			$bodytext		.=		"--".$boundary."--";
		}
		else {
			$header		.=		"Content-Type: text/html; charset=euc-kr\n";
			$bodytext		=		$body;
		}

		@mail( $to, $subject, $bodytext, $header );
	}

	function sendManyMail( $from, $fromname, $subject, $body, $htmlcheck, $bcc ) {
		global $dear;
		global $objfile;

		if( $dear[settingstate] != "서비스중" || $dear[settingstate] != "서비스중" ) return;

		$content = $_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"]." - ".$subject."\n";
		$objfile->fileWrite( "/tmp/maillog_".date( "Ymd" ), $content, "a" );

		$fromname = strip_tags( $fromname );
		
		if( is_array($bcc) ) $bcc = implode( ",", $bcc );
		else $bcc = $bcc;
		
		$bodytext	 = $this->htmlText( $body, $htmlcheck );

		$header	=				"Return-Path: ".$from."\n";
		$header	.=				"From:  \"".$fromname."\"<".$from.">\n";
		$header	.=				"X-Mailer: DearMALL MAIL from ".$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"]."\n";
		$header	.=				"Reply-To: ".$from."\n";

		$header	.=		"Content-Type: text/html; charset=euc-kr\n";
		$header	.=		"Bcc:".$bcc."\n";

		@mail( '', $subject, $bodytext, $header );
	}

	/*
	 날짜 구하기이다.. 수시로, 년, 월, 일, 날짜와 시간을 초로 반환한다.
	 구분자는 $part로 하고 year, month, day, all, time으로 한다.
	 사용법은 $part를 넘기면서 return 값을 받으면 된다.
	*/
	function dateSeek($str) {
		if($str == "year") return date('Y', mktime());
		else if($str == "month") return date('m', mktime());
		else if($str == "day") return date('d', mktime());
		else if($str == "all") return date('Y-m-d', mktime());
		else if($str == "time") return mktime();
		else {
			$tmpArr=explode("-",$str);
			return mktime(0,0,0,$tmpArr[1], $tmpArr[2], $tmpArr[0]);
		}
	}

	function getPeriod( $day1, $day2, $rtn_type='' ) {
		$arr1 = explode( "-", $day1 );
		$arr2 = explode( "-", $day2 );
		
		$tday1 = mktime( 0, 0, 0, $arr1[1], $arr1[2], $arr1[0] );
		$tday2 = mktime( 0, 0, 0, $arr2[1], $arr2[2], $arr2[0] );

		$period = ( $tday2 - $tday1 ) / ( 60 * 60 * 24 );

		return $period;
	}

	function getDatePM( $date, $pm, $pm_type='d' ) {
		$dateARR = explode( "-", $date );

		switch( $pm_type ) {
			case "Y" :
				$rtnDate = date( "Y-m-d", mktime( 0, 0, 0, $dateARR[1], $dateARR[2], $dateARR[0] + $pm ) );
				break;
			case "m" :
				$rtnDate = date( "Y-m-d", mktime( 0, 0, 0, $dateARR[1] + $pm, $dateARR[2], $dateARR[0] ) );
				break;
			case "d" :
				$rtnDate = date( "Y-m-d", mktime( 0, 0, 0, $dateARR[1], $dateARR[2] + $pm, $dateARR[0] ) );
				break;
		}

		return $rtnDate;
	}

	// 페이지를 뒤로 강제로 Back 시키고자 할때 사용하면 된다. 사용법은 왜 Back을 시키는지 이유를 인자로 넘기면 된다.
	function alertBack($msg) {
		echo "<script language=javascript>
		<!--
		alert(\"".$msg."\");
		history.back();
		//-->
		</script>";
		exit;
	}

	// 페이지를 이동시킬때 쓴다. 사용법은 이동할 url과 메시지이다.
	function goUrl($url, $msg='') {
		if(!empty($msg)) {
			echo "
				<script>
					alert(\"$msg\");
				</script>
				";
		}
		echo "<meta http-equiv='refresh' content=\"0;URL=$url\">";
		exit;
	}

	function goHeader($url) {
		//Header("Location:$url");
		$this->goUrl($url);
		exit;
	}

	// confirm
	function confirmUrl( $url, $msg='' ) {
		if( !empty( $msg ) ) {
			echo "
				<script>
					if( confirm(\"".$msg."\") )
						self.location = \"".$url."\";
					else
						history.back();
				</script>
				";
		}
		echo "<meta http-equiv='refresh' content=\"0;URL=$url\">";
		exit;
	}

	// 온리 알러트
	function alert($msg, $exit=true) {
		echo "<script language=javascript>
		<!--
		alert(\"".$msg."\");
		//-->
		</script>";
		if($exit) exit;
	}

	// 온리 클로즈
	function selfClose($exit=true) {
		echo "<script language=javascript>
		<!--
		self.close();
		//-->
		</script>";
		if($exit) exit;
	}

	function reloadObject( $obj, $exit=true ) {
		echo	"<SCRIPT>".
				"if( typeof( ".$obj." ) == 'object' ) ".$obj.".location.reload();".
				"</SCRIPT>";

		if( $exit ) exit;
	}

	function goObject( $obj, $url, $exit=true ) {
		echo "<script language=javascript>".$obj.".location = '".$url."';</script>";

		if( $exit ) exit;
	}

	function closeObject( $obj, $exit=true ) {
		echo "<script language=javascript>".$obj.".close();</script>";

		if( $exit ) exit;
	}

	function selectObject( $obj, $exit=true ) {
		echo "<script language=javascript>".$obj.".select();</script>";

		if( $exit ) exit;
	}

	function focusObject( $obj, $exit=true ) {
		echo "<script language=javascript>".$obj.".focus();</script>";

		if( $exit ) exit;
	}
	
	// 창을 뛰웠다가 닫을때 사용된다. 
	function alertClose( $msg ) {
		echo "<script language=javascript>
		<!--
		alert(\"".$msg."\");
		self.close();
		//-->
		</script>";
		exit;
	}

	// 창을 뛰웠다가 닫을때 사용된다. 
	function alertCloseReload($msg) {
		if($msg) {
			echo "<script language=javascript>
			<!--
			alert(\"".$msg."\");
			opener.document.location.reload();
			self.close();
			//-->
			</script>";
			exit;
		}
		else {
			echo "<script language=javascript>
			<!--
			opener.document.location.reload();
			self.close();
			//-->
			</script>";
			exit;
		}
	}

	// 창을 뛰웠다가 닫을때 사용된다. 
	function alertCloseReload2($msg) {
		if($msg) {
			echo "<script language=javascript>
			<!--
			alert(\"".$msg."\");
			opener.opener.document.location.reload();
			opener.close();
			self.close();
			//-->
			</script>";
			exit;
		}
		else {
			echo "<script language=javascript>
			<!--
			opener.opener.document.location.reload();
			opener.close();
			self.close();

			//-->
			</script>";
			exit;
		}
	}

	function alertCloseGourl($msg, $url) {
		if(empty($msg)) {
			echo "<script language=javascript>
			<!--
			opener.document.location='$url';
			self.close();
			//-->
			</script>";
			exit;

		}
		else {
			echo "<script language=javascript>
			<!--
			alert(\"".$msg."\");
			opener.document.location='$url';
			self.close();
			//-->
			</script>";
			exit;
		}
	}

	function goParent( $url, $msg='' ) {
		if( empty($msg) ) {
			echo "
			<SCRIPT>
			<!--
			parent.document.location = '$url';
			//-->
			</SCRIPT>
			";
		}
		else {
			echo "
			<SCRIPT>
			<!--
			alert(\"".$msg."\");
			parent.document.location = '$url';
			//-->
			</SCRIPT>
			";
		}

		exit;
	}

	function reloadParent( $msg='' ) {
		global $_PAGEMODE;

		if( $msg )				$msgSTR	=	"alert( \"".$msg."\" );";
		if( $_PAGEMODE )	$pmSTR	=	"parent.opener.location.reload();";

		echo "
		<SCRIPT>
		<!--
		$msgSTR
		$pmSTR
		parent.location.reload();
		//-->
		</SCRIPT>
		";

		exit;
	}

	function reloadParentUrl( $msg='' ) {
		global $_PAGEMODE;

		if( $msg )				$msgSTR	=	"alert( \"".$msg."\" );";
		if( $_PAGEMODE )	$pmSTR	=	"parent.opener.location.reload();";

		echo "
		<SCRIPT>
		<!--
		$msgSTR
		$pmSTR
		parent.location.href = parent.location.href;
		//-->
		</SCRIPT>
		";

		exit;
	}

	function closeParent( $msg='' ) {
		global $_PAGEMODE;

		if( $msg )				$msgSTR	=	"alert( \"".$msg."\" );";
		if( $_PAGEMODE )	$pmSTR	=	"parent.opener.location.reload();";

		echo "
		<SCRIPT>
		<!--
		$msgSTR
		$pmSTR
		parent.close();
		//-->
		</SCRIPT>
		";

		exit;
	}

	function replaceUrl( $url ) {
		echo "<script>self.location.replace( '".$url."' );</script>";
		exit;
	}
	
	function replaceParent( $url ) {
		echo "<script>parent.location.replace( '".$url."' );</script>";
		exit;
	}

	function onlyFrame( $url ) {
		echo "<script>if( parent.frames.length == 0 ) window.location.replace( '".$url."' );</script>";
	}

	// 랜덤 문자열 유일키 발생(상품코드로 사용) / 총 50자인데.. 필요한 만큼만 자르자.
	function getCode( $len=10 ) {
		$SID = md5(uniqid(rand()));
		$code = substr($SID, 0, $len);
		return $code;
	}

	// 세션키 생성
	function getSession( $date='' ) {
		if( !$date ) $date = date( "ymdHis" );

		$rtn = $date.substr( microtime(), 4, 3 );

		return $rtn;
	}

	// 루트 Url 얻기
	function getSeverUrl() {
		return "http://".$_SERVER["HTTP_HOST"];
	}

	function flashLoad( $urlpath, $width, $height ) {
		/*
		if( $object_tags ) {
			echo "
				<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0\" width=\"$width\" height=\"$height\">
					<param name=movie value=\"$urlpath\">
					<param name=quality value=high>
					<PARAM NAME=wmode VALUE=transparent>
					<param name='menu' value='false'>
			";
		}

		echo "
				<embed src=\"$urlpath\" quality=high pluginspage=\"http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash\" type=\"application/x-shockwave-flash\" width=\"$width\" height=\"$height\">
				</embed> 
		";

		if( $object_tags ) {
			echo "
				</object>
			";
		}
		*/

		echo "<SCRIPT>load_flash( '".$urlpath."', '".$width."', '".$height."' );</SCRIPT>";
	}

	function flashLoadRtn( $urlpath, $width, $height ) {
		$rtn = "<SCRIPT>load_flash( '".$urlpath."', '".$width."', '".$height."' );</SCRIPT>";
		/*
		$rtn = "
			<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=5,0,0,0\"  width=\"$width\" height=\"$height\">
		        <param name=movie value=\"$urlpath\">
				<param name=quality value=high>
				<PARAM NAME=wmode VALUE=transparent>
				<param name='menu' value='false'> 
				<embed src=\"$urlpath\" quality=high pluginspage=\"http://www.macromedia.com/shockwave/download/index.cgi?P1_Prod_Version=ShockwaveFlash\" type=\"application/x-shockwave-flash\" width=\"$width\" height=\"$height\">
				</embed> 
			</object>
		";
		*/

		return $rtn;
	}

	// 데이타를 가져올 첫 시작 포인트를 반환한다. 페이징에 필요하다.
	function getDbStartNo($PageNo, $PageSize) {
		return ($PageNo-1)*$PageSize;
	}	

	// Paging 함수
	function adminPaging( $PageNo, $PageSize, $totalrows, $whereqry='', $qrystring='' ,$page_info , $board_id='x') {
		$lastpgno=ceil($totalrows/$PageSize);
		echo "<TABLE border=0 cellpadding=0 cellspacing='15'><TR><TD>";
		if($lastpgno!=0) {
			if($PageNo>10) {
					$prevPage=floor(($PageNo-1)/10)*10;
				echo "<a href=\"$PHP_SELF?page=$page_info&page_no=$prevPage&id=$board_id&whereqry=$whereqry{$qrystring}\"><img src='/admin/img/adpage_img/bbsbtn_pre2.gif' height='12'></A>";
			}
			else{
				echo "<img src='/admin/img/adpage_img/bbsbtn_pre2.gif' height='12'>";
			}
			if($PageNo > 1){
				echo "<a href=\"$PHP_SELF?page=$page_info&page_no=".($PageNo-1)."&id=$board_id&whereqry=$whereqry{$qrystring}\"><img border='0' src='/admin/img/adpage_img/bbsbtn_pre.gif' height='12' hspace='6'>";
			}
			else{
				echo "<img border='0' src='/admin/img/adpage_img/bbsbtn_pre.gif' height='12' hspace='6'>";
			}
			echo "</TD>";
			$i=0;

			$startpage=floor(($PageNo-1)/10)*10+1;
			while($i<10 && $startpage<=$lastpgno){
				if($PageNo<>$startpage) {
					echo "<TD><a href=\"$PHP_SELF?page=$page_info&page_no=$startpage&id=$board_id&whereqry=$whereqry{$qrystring}\"><font color=5D5D5D>$startpage</font></a></TD>";
				}
				else{
					echo "<TD><font class='red bold'>$startpage</font></TD>";
				}
				$i++;
				$startpage=$startpage+1;
			}

			$nextPage=floor(($PageNo-1)/10)*10+11;
			echo "<TD>";
			if($PageNo < $lastpgno){
				echo "<a href=\"$PHP_SELF?page=$page_info&page_no=".($PageNo+1)."&id=$board_id&whereqry=$whereqry{$qrystring}\"><img border='0' src='/admin/img/adpage_img/bbsbtn_next.gif 'height='12' hspace='6'></a>";
			}
			else{
				echo "<img border='0' src='/admin/img/adpage_img/bbsbtn_next.gif 'height='12' hspace='6'>";
			}
			if($nextPage<=$lastpgno) {
				echo "<a href=\"$PHP_SELF?page=$page_info&page_no=$nextPage&id=$board_id&whereqry=$whereqry{$qrystring}\"><img src='/admin/img/adpage_img/bbsbtn_next2.gif' height='12'></A>";
			}
			else{
				echo "<img src='/admin/img/adpage_img/bbsbtn_next2.gif' height='12'>";
			}
		}
		echo "</TD></TR></TABLE>";
	}

	function adminPaging2( $PageNo, $PageSize, $totalrows, $whereqry='', $qrystring='' ) {
		$lastpgno=ceil($totalrows/$PageSize);
		echo "<TABLE border='0' cellpadding='0' cellspacing='3' id='paging'><TR>";
		if($lastpgno!=0) {

			if($PageNo>10) {
					$prevPage=floor(($PageNo-1)/10)*10;
				echo "<TD><a href=\"$PHP_SELF?PageNo=$prevPage&whereqry=$whereqry{$qrystring}\"><img border='0' src='/img/common/bbsbtn_pre02.gif'></a></TD>";
			}
			else{
				echo "<TD><img border='0' src='/img/common/bbsbtn_pre02.gif'></TD>";
			}
			if($PageNo > 1){
				echo "<TD><a href=\"$PHP_SELF?PageNo=".($PageNo-1)."&whereqry=$whereqry{$qrystring}\"><img border='0' src='/img/common/bbsbtn_pre01.gif'></TD>";
			}
			else{
				echo "<TD><img border='0' src='/img/common/bbsbtn_pre01.gif'></TD>";
			}
			$i=0;

			$startpage=floor(($PageNo-1)/10)*10+1;
			echo "<TD class='pad'>";
			while($i<10 && $startpage<=$lastpgno){
				if($PageNo<>$startpage) {
					echo "<a href=\"$PHP_SELF?PageNo=$startpage&whereqry=$whereqry{$qrystring}\"><font color=5D5D5D>$startpage</font></a> ";
				}
				else{
					echo "<font class='this'>$startpage</font> ";
				}
				$i++;
				$startpage=$startpage+1;
			}
			echo "</TD>";
			$nextPage=floor(($PageNo-1)/10)*10+11;
			if($PageNo < $lastpgno){
				echo "<TD><a href=\"$PHP_SELF?PageNo=".($PageNo+1)."&whereqry=$whereqry{$qrystring}\"><img border='0' src='/img/common/bbsbtn_next01.gif'></a></TD>";
			}
			else{
				echo "<TD><img border='0' src='/img/common/bbsbtn_next01.gif'></TD>";
			}
			if($nextPage<=$lastpgno) {
				echo "<TD><a href=\"$PHP_SELF?PageNo=$nextPage&whereqry=$whereqry{$qrystring}\"><img border='0' src='/img/common/bbsbtn_next02.gif'></a></TD>";
			}
			else{
				echo "<TD><img border='0' src='/img/common/bbsbtn_next02.gif'></TD>";
			}
			echo "<TD class='total'>(<font class='this'>".$PageNo."</font>/".$lastpgno.")</TD>";
		}
		echo "</TR></TABLE>";
	}
    
	function adminPaging3( $PageNo, $PageSize, $totalrows, $whereqry='', $qrystring='' ) {
		$lastpgno=ceil($totalrows/$PageSize);
		echo "<div id='paging' class='paging'><ul class='clearfix'>";
		if($lastpgno!=0) {

			if($PageNo>10) {
					$prevPage=floor(($PageNo-1)/10)*10;
				echo "<li><a href=\"$PHP_SELF?PageNo=$prevPage&whereqry=$whereqry{$qrystring}\" class='start'><img src='../images/start.png' alt='first button'><span class='hide'>첫페이지</span></a></li>";
			}
			else{
				echo "<li><a style='pointer-events:none'><img src='../images/start.png' alt='first button'></a></li>";
			}
			if($PageNo > 1){
				echo "<li><a href=\"$PHP_SELF?PageNo=".($PageNo-1)."&whereqry=$whereqry{$qrystring}\" class='prev'><img src='../images/prev.png' alt='previous button'><span class='hide'>이전페이지</span></a></li>";
			}
			else{
				echo "<li><a style='pointer-events:none'><img src='../images/prev.png' alt='previous button'><span class='hide'>이전페이지</span></a></li>";
			}
			$i=0;

			$startpage=floor(($PageNo-1)/10)*10+1;
			echo "<li>";
			while($i<10 && $startpage<=$lastpgno){
				if($PageNo<>$startpage) {
					echo "<a href=\"$PHP_SELF?PageNo=$startpage&whereqry=$whereqry{$qrystring}\" class='pad active'>$startpage</a> ";
				}
				else{
					echo "<a class='pad' style='pointer-events:none'>$startpage</a> ";
				}
				$i++;
				$startpage=$startpage+1;
			}
			echo "</li>";
			$nextPage=floor(($PageNo-1)/10)*10+11;
			if($PageNo < $lastpgno){
				echo "<li><a href=\"$PHP_SELF?PageNo=".($PageNo+1)."&whereqry=$whereqry{$qrystring}\" class='next'><img src='../images/next.png' alt='next button'><span class='hide'>다음페이지</span></a></li>";
			}
			else{
				echo "<li><a style='pointer-events:none'><img src='../images/next.png' alt='next button'><span class='hide'>다음페이지</span></a></li>";
			}
			if($nextPage<=$lastpgno) {
				echo "<li><a href=\"$PHP_SELF?PageNo=$nextPage&whereqry=$whereqry{$qrystring}\" class='end'><img src='../images/end.png' alt='end button'><span class='hide'>마지막페이지</span></a></li>";
			}
			else{
				echo "<li><a style='pointer-events:none'><img src='../images/end.png' alt='end button'><span class='hide'>마지막페이지</span></a></li>";
			}
			echo "<li class='total' style='display:none;'>(<font class='this'>".$PageNo."</font>/".$lastpgno.")</li>";
		}
		echo "</ul></div>";
	}
    
	function adminPaging4( $PageNo, $PageSize, $totalrows, $whereqry='', $qrystring='' ) {
		$lastpgno=ceil($totalrows/$PageSize);
		echo "<div id='board-paging' class='board-paging'>";
		if($lastpgno!=0) {

			if($PageNo>10) {
					$prevPage=floor(($PageNo-1)/10)*10;
				echo "<button type='button' class='first' onclick='location.href=\"$PHP_SELF?PageNo=$prevPage&whereqry=$whereqry{$qrystring}\'>
                    <span class='blind'>첫 패이지로</span></button>";
			}
			else{
				echo "<button class='first'><span class='blind'>첫 패이지로</span></button>";
			}
			if($PageNo > 1){
				echo "<button type='button' class='prev' onclick='location.href=\"$PHP_SELF?PageNo=".($PageNo-1)."&whereqry=$whereqry{$qrystring}\'>
                    <span class='blind'>이전 페이지로</span></button>";
			}
			else{
				echo "<button class='prev'><span class='blind'>이전 패이지로</span></button>";
			}
			$i=0;

			$startpage=floor(($PageNo-1)/10)*10+1;
//			echo "<li>";
			while($i<10 && $startpage<=$lastpgno){
				if($PageNo<>$startpage) {
					echo "<a href=\"$PHP_SELF?PageNo=$startpage&whereqry=$whereqry{$qrystring}\">$startpage</a> ";
				}
				else{
					echo "<a>$startpage</a> ";
				}
				$i++;
				$startpage=$startpage+1;
			}
//			echo "</li>";
			$nextPage=floor(($PageNo-1)/10)*10+11;
			if($PageNo < $lastpgno){
				echo "<button type='button' class='next' onclick='location.href=\"$PHP_SELF?PageNo=".($PageNo+1)."&whereqry=$whereqry{$qrystring}\'>
                    <span class='blind'>다음 페이지로</span></button>";
			}
			else{
				echo "<button class='next'><span class='blind'>다음 패이지로</span></button>";
			}
			if($nextPage<=$lastpgno) {
				echo "<button type='button' class='last' onclick='location.href=\"$PHP_SELF?PageNo=$nextPage&whereqry=$whereqry{$qrystring}\'>
                    <span class='blind'>마지막 페이지로</span></button>";
			}
			else{
				echo "<button class='last'><span class='blind'>마지막 패이지로</span></button>";
			}
			echo "<li class='total' style='display:none;'>(<font class='this'>".$PageNo."</font>/".$lastpgno.")</li>";
		}
		echo "</div>";
	}
	
	function backPaging( $qryString ) {
		global $PHP_SELF;
		global $PageNo;

		if( $PageNo > 1 ) $this->goUrl($PHP_SELF."?PageNo=".( $PageNo - 1 ).$qryString);
	}

	function htmlString( $str ) {
		return htmlspecialchars( $str );
	}

	function jsString( $str ) {
		$search		= Array( "\\", "'", "&", "\"", "\n" );
		$replace		= Array( "\\\\", "\\'", "&amp;", "&quot;", "\\n" );
		$str			= str_replace( $search, $replace, $str );

		return $str;
	}


//======================================================//
//							//
//	메세지 띄운후 다른 페이지로 이동하는 함수	//
//							//
//======================================================//


	function DoMessageLocation($message, $location_url){

	echo "
	<script language=\"javascript\">
	<!--
		alert(\"$message\");
		location.href='$location_url';
	//-->
	</script>
	";
	exit;	//스크립트 종료
	}

	function nexti($ca_code,$ca_step,$ca_group,$ca_parent,$mode=""){
	  echo 'ca_code='.$ca_code.'&ca_step='.$ca_step.'&ca_group='.$ca_group.'&ca_parent='.$ca_parent.'&mode='.$mode;  
	}

	function select_read(){
		global $ca_code,$mode;

		$sql = "SELECT ca_title,ca_comment,ca_step,ca_group,ca_parent FROM category WHERE ca_code=$ca_code";
		$result = mysql_query( $sql );

		$row = mysql_fetch_array( $result );

		$ca_title=htmlspecialchars($row[ca_title]);
		$ca_comment=$row[ca_comment];

		$ca_step = $row[ca_step];
		$ca_group=$row[ca_group];	
		$ca_parent=$row[ca_parent];



		// 제목에 따옴표(') 가 들어 갔을 경우 
		$ca_title = str_replace(chr(34)," &#34 ",$ca_title);

			return array ($ca_title,$ca_comment,$ca_step,$ca_group,$ca_parent);

	}// end function select_read()


	//===============================================
	// 부모 값 찾기 
	// "컴퓨터/언어/PHP"의 분류 찾기 위한 함수 
	//===============================================
	function parent_read($ca_parent,$ca_title1){
		global $objfunc;
		global $ca_list;

		$result=mysql_query(" select ca_code,ca_parent,ca_title from category where ca_code='$ca_parent'");
		$row=mysql_fetch_array($result);
		$ca_title=$row[ca_title];
		$ca_parent=$row[ca_parent];
		$ca_list=$ca_list.".".$ca_title;

			if($ca_parent==0){
				$ca_list3=$ca_title1.$ca_list;
				$ca_list3=explode(".",$ca_list3);

				for ($i=count($ca_list3)-1;$i>=0;$i--){
					if ($i == 2){
						$name = '대분류 => ';					
					}else if($i == 1){
						$name = '<br> 중분류 => ';
					}else{
						$name = '<br> 소분류 => ';
					}
					print $name.'<b>'.$ca_list3[$i]."</b> ";
				}
				//return $ca_list; 동작 안함!
			}else{
				$this->parent_read($ca_parent,$ca_title1); // 재귀 호출 
			}
		}

	function post_var_trim() {
		global $_POST;

		array_walk( $_POST, '_array_trim' );
	}
	
	function post_var_chk( $var, $chk='' ) {
	global $_POST;

	if( isset( $_POST[$var] ) && $_POST[$var] == $chk ) $rtn = true;
	else $rtn = false;

	return $rtn;
	}

	function getColorPicker( $obj1, $obj2, $obj3 ) {
	//$rtn = "<img src='/shopadmin/img2/ico_cp.gif' align=absmiddle class=hand onclick=\"getColorPicker( 'opener.".$obj1."', 'opener.".$obj2."', 'opener.".$obj3."' );\">";
	$rtn = "<a href=\"javascript:getColorPicker( 'opener.".$obj1."', 'opener.".$obj2."', 'opener.".$obj3."' );\"><img src='/admin/img/ico_cp.gif' align=absmiddle border=0></a>";

	return $rtn;
	}

	function getColorPickerSet( $form, $objname, $val, $addedStyle='', $sepa='' ) {
	global $objfunc;

	$new_objname = "elements[\\'".$objname."\\']";
	
	$rtn = "<input type=text name=".$objname." size=9 class='box imeinactive' value=\"".$this->htmlText($val)."\" style='color:".$val."' ".$addedStyle."> ";
	$rtn .= $sepa;
	$rtn .= $this->getColorPicker( $form.".".$new_objname, $form.".".$new_objname, $form.".".$new_objname.".style.color" );

	return $rtn;
	}

}
?>