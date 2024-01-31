<?php
class FUNCDB_CLASS {
//=================//
//DB FUNC//
//=================//

	// 초기 관리자테이블 생성함수 //
	function InstallNewDB($AdminId, $AdminPw) {
	global $objdb;
	//관리자
		$RealPass = $AdminPw;
		$AdminPw = sha1($AdminPw.'blue');
		$sql = "CREATE TABLE `authorised_user` (
				 `name` varchar(20) NOT NULL,
				 `password` varchar(40) NOT NULL,
				 `real_pass` varchar(40) NOT NULL,
				 `email` varchar(50) default NULL,
				  PRIMARY KEY  (`name`)
				)
				";
		$objdb->sqlExe($sql);
		$sql = "INSERT INTO authorised_user(name, password, real_pass)VALUES('$AdminId','$AdminPw','$RealPass')";
		$objdb->sqlExe($sql);

	}
}
?>