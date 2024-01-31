<?
function user_board( $bbsid, $bbs_line, $cut='0', $orderby='sortno', $m_num, $mname, $size ='' ) {
	global $objdb, $objfunc;
	if ($orderby == "sortno")	$orderby = "wdate desc";
	else										$orderby = "wdate asc";
			$sql = "select * from bbs where board_id = '".$bbsid."' order by $orderby limit 0 , $bbs_line";		
			$rs = $objdb->sqlResult( $sql );
	$i = 1;
	$size = explode(',',$size);
	for( $i = 1; $i <= $bbs_line; $i++ ) {
		$row = $objdb->sqlFetch( $rs );
				$url = "/bbs/bbsView.html?idx=$row[idx]&mname=$mname&m_num=$m_num&id=$bbsid";
				$wdate = substr(str_replace("-",".",$row[wdate]),0,10);

		$rtn[no][$i]				=	$row[idx];
		$rtn[title][$i]				=	$cut ? $objfunc->hanCut( $row[title], $cut ) : $row[title];
		$rtn[title][$i]				=	"<A HREF=\"".$url."\">".$rtn[title][$i]."</A> ";
		$rtn[url][$i]				=	$url;
		$rtn[writer][$i]			=	$row[name];
		$rtn[content][$i]		=	$row[comment];
		$rtn[wdate][$i]			=	$wdate;
		$rtn[readnum][$i]		=	$row[see];
		$rtn[clipfile][$i]			=	$row[clipfile] ? "/bbs/clipfile/".urlencode($row[clipfile]) : "/img/bbs/noimage.gif";
		$rtn[img][$i]				=	"<a href=\"".$url."\"><img src=".$rtn[clipfile][$i]." ".$size[0]." ".$size[1]."></a>";

	}

	return $rtn;
}
?>