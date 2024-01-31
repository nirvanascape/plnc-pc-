<?php
session_start();
	if(!isset($_SESSION['LoginAdmin'])){
		echo "<script> alert('올바른 접근 방식이 아닙니다.');
				self.location.replace('/admin/');</script>";
	}

class page
{
	var $content;
	var $title = 'Admin';
	var $buttons = array(	
							'사이트 정보'  => '/admin/admin/index.html',
							'회원관리' => '/admin/member/index.html',
							'게시판 관리'	 => '/admin/bbs/index.html',
							'상품관리'	 => '/admin/good/index.html',
							'팝업창 관리'  => '/admin/popup/index.html'
							
							);
	function __set( $name, $value )
	{
		$this->$name = $value;
	}
	
	function Display()
	{
		echo "<html>\n<head>\n";
		$this->DisplayTitle();
		$this->DisplayStyles();
		echo "</head>\n<body>\n";
		$this->DisplayHeader();
		$this->DisplayMenu( $this->buttons );
		$this->DisplayLeftMenu();
		echo $this->content;
		$this->DisplayFooter();
		echo "</body>\n</html>\n";
	}
	
	function DisplayTitle()
	{
		echo '<title>'.$this->title.'</title>';
	}
	function DisplayStyles()
	{
?>
	<link href='/common/admin.css' rel='stylesheet' type='text/css'>
<?php
	}
	
	function DisplayHeader()
	{
?>
		<div align='center'>
		
		<TABLE border='0' cellpadding='0' cellspacing='0' class='adminSize'>
		<TR height='40' valign='bottom'>
			<TD><img src='/admin/img/logo.gif' hspace='10'/></TD>
			<TD align='right' style='padding-right:10px;'>
				<a href='/' target='_blank' style='text-decoration:none; color:#FFFFFF'><img src='/admin/img/btn_home.gif' /></a>
				<a href='/admin/logout.php' style='text-decoration:none; color:#FFFFFF'><img src='/admin/img/btn_logout.gif' /></a>
			</TD>
		</TR>
		</TABLE>
		<div class='adminSize adminBdr'></div>

<?php
	}

	function DisplayMenu( $buttons )
	{
		echo "<TABLE class='adminSize adminTop' cellpadding='0' cellspacing='0' border='0'>\n";
		echo "<TR>\n";
		$width = 100/count( $buttons );
		foreach( $buttons as $name=>$url )
		{
			$this->DisplayButton( $width, $name, $url, !$this->IsURLCurrentPage( $url ) );
		}
		echo "</TR>\n";
		echo "</TABLE>\n";
	}

	function IsURLCurrentPage( $url )
	{
		$ThisPage = $_SERVER['PHP_SELF'];
		$ThisUrl = dirname($url);
		if( eregi( $ThisUrl, $ThisPage ) == false )
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	function DisplayButton( $widht, $name, $url, $active=true )
	{
		if( $active )
		{
			echo "<TD width='".htmlentities( $width )."'>
					<a href='$url'><span class='menu'>$name</span></a></TD>";
		}
		else
		{
			echo "<TD widht='".htmlentities( $width )."'>
					<span class='thismenu'>$name</span></TD>";
		}
	}

	function DisplayLeftMenu()
	{
		global $MainPage;
		global $objdb;
		global $objfuncdb;
		global $objfunc;
		global $objfile;

		echo "<TABLE class='adminSize' border='0'>
				<TR><TD width='160' valign = 'top'>";
		include 'left.html';
		include $_SERVER['DOCUMENT_ROOT'].'/admin/left_ban.html';
		echo "</TD><TD>";
		if( $MainPage ){
		include $MainPage;
		}
		echo "</TD></TR></TABLE>";
	}

	function DisplayFooter()
	{
		global $objdb;
		$sql = "SELECT domain FROM authorised_user";
		$domain = $objdb->sqlRowOne( $sql );
?>
		<div class='adminSize adminBdr' style='margin-top:30px;'></div>
		<div class='adminSize adminFoot'>Copyright(c) <?=$domain?> All rights reserved.</div>

	</div>
<iframe name='exeFrame' width=0 height=0></iframe>
<?php
	}
}
?>
