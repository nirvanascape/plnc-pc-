<?php
class DB_CLASS {
	var $dbobj;
	var $conn;

	var $skip_error;

	var $host;
	var $user;
	var $pass;
	var $dbname;

	var $echoqrytime = false;

	function DB_CLASS( $HOST, $USER, $PASS, $DBNAME, $SKIP_ERROR='' ) {
		$this->host			=	$HOST;
		$this->user			=	$USER;
		$this->pass			=	$PASS;
		$this->dbname		=	$DBNAME;
		$this->skip_error	=	$SKIP_ERROR;

		$this->dbobj = $this->dbConn( $HOST, $USER, $PASS, $DBNAME );

		return $this->dbobj;
	}

	function dbConn( $HOST, $USER, $PASS, $DBNAME ) {
		$this->conn = mysql_connect( $HOST, $USER, $PASS );
		mysql_query("set names euckr");
		@mysql_select_db( $DBNAME, $this->conn );
		return $this->conn;
	}

	function is_conn() {
		if( $this->conn ) return true;
		else return false;
	}

	function sqlRow($query) {
		$result = $this->sqlResult( $query );
		$row = @mysql_fetch_array($result);

		return $row;
	}

	function selectDB($db) {
		@mysql_select_db( $db, $this->conn );
	}

	function ListDBS() {
		return @mysql_list_dbs($this->conn);
	}

	function Close() 	{
		@mysql_close($this->conn);
	}

	function sqlResult($query) {
		if( $this->echoqrytime )
		{
			list( $usec, $sec ) = explode( " ", microtime() );
			$starttime = ((float)$usec + (float)$sec);
			echo "<!-- ";
			echo $query;
			echo " -->";
			echo "\n";
		}
		$result = @mysql_query($query,$this->conn);
		if( $this->echoqrytime )
		{
			list( $usec, $sec ) = explode( " ", microtime() );
			$endtime = ((float)$usec + (float)$sec);
			echo "<!-- ";
			echo "QRY EXECTIME [".($endtime-$starttime)."]";
			echo " -->";
			echo "\n";
		}
		return $result;
	}

	function sqlFetch($result) {
		$row = @mysql_fetch_array($result);

		return $row;
	}

	function sqlExe($query) {
		$iReturn = $this->sqlResult($query);

		return $iReturn;
	}

	// Count, 한컬럼의 데이타를 얻어낸다.
	function sqlRowone($sql) {
		$result = $this->sqlResult($sql);
		$row = @mysql_fetch_array($result);

		return $row[0];
	}

	// 해당 레코드의 갯수를 반환한다.
	function sqlNumrows($query) {
		$result = $this->sqlResult($query);
		$rows = @mysql_num_rows($result);

		return $rows;
	}

	function sqlNumrowsResult($result) {
		$rows = @mysql_num_rows($result);

		return $rows;
	}

	// 게시판에서 현재수보다 1큰 수를 반환하다.
	function sqlMaxno($sql) {
		$result = $this->sqlResult( $sql );
		$row = @mysql_fetch_array($result);

		if( empty( $row[0] ) ) {
			$retvalue = 1;
		}
		else {
			$retvalue = $row[0] + 1;
		}

		return $retvalue;
	}

	function sqlAffRows() {
		return @mysql_affected_rows($this->conn);
	}

	function sqlArray( $sql, $idx='', $idx_array=false ) {
		$result = $this->sqlResult($sql);
		while( $row = @mysql_fetch_array($result) ) {
			if( $idx ) {
				if( $idx_array )	$data[$row[$idx]][] = $row;
				else				$data[$row[$idx]] = $row;
			}
			else $data[] = $row;
		}

		return $data;
	}

	function sqlArrayOne($sql) {
		$result = $this->sqlResult( $sql );
		while( $row = @mysql_fetch_array($result) ) $data[] = $row[0];

		return $data;
	}

	function sqlArrayTwo( $sql, $idx='', $idx2='' ) {
		if( $idx == "" ) $idx = "0";
		if( $idx2 == "" ) $idx2 = "1";

		if( $sql ) {
			$rs = $this->sqlResult( $sql );
			while( $row = @mysql_fetch_array( $rs ) ) {
				$data[$row[$idx]] = $row[$idx2];
			}
		}

		return $data;
	}

	function sqlInsertId() {
		$rtn = @mysql_insert_id($this->conn);

		return $rtn;
	}

	function sqlTableList() {
		$rs		= @mysql_list_tables( $this->dbname, $this->conn );
		$cnt	= @mysql_num_rows( $rs );

		for( $i = 0; $i < $cnt; $i++ ) {
			$rtn[] = @mysql_tablename( $rs, $i );
		}
		
		return $rtn;
	}

	function sqlFieldList( $table, $offset=0, $length=0 ) {
		$rs		= @mysql_list_fields( $this->dbname, $table, $this->conn );
		$cnt	= @mysql_num_fields( $rs );

		for( $i = 0; $i < $cnt; $i++ ) {
			$rtn[] = @mysql_field_name( $rs, $i );
		}

		if( $offset ) {
			if( $length ) {
				$rtn = array_slice( $rtn, $offset, $length );
			}
			else {
				$rtn = array_slice( $rtn, $offset );
			}
		}
		
		return $rtn;
	}

	function sqlFreeResult( &$rs ) {
		if( is_resource( $rs ) ) @mysql_free_result( $rs );
	}
}
?>
