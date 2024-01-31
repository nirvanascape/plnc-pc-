<?
class FILE_CLASS {

	function uploadFile( $file, $file_name, $savedir, $orgfilename=false ) {
		global $objfunc;
		if( substr( $savedir, -1 ) != "/" ) $savedir .= "/";
		
		if( $file != "none" && !empty($file) ) {
			$pos = strrpos( $file_name, "." );
			$name = substr( $file_name, 0, $pos );
			$ext = substr( $file_name, $pos+1 );

			if( !$this->_chk_upload_file( $file_name ) ) {
				FUNC_CLASS::alert( "�ش� ������ ���ε尡 �Ұ����մϴ�." );
				exit;
			}

			if( !$orgfilename ) {
				$filename = date( "YmdHis" ).".".$ext;
				$i = 1;
				while( file_exists( $savedir.$filename ) ) {
					$filename = date("YmdHis",mktime())."_".$i.".".$ext;
					$i++;
				}
			}
			else {
				$filename = str_replace( " ", "_", $file_name );
				$i = 1;
				while( file_exists( $savedir.$filename ) ) {
					$filename = $name."_".$i.".".$ext;
					$i++;
				}
			}

			//if( !copy( $file, $savedir.$filename ) ) {
//			$objfunc->alert($file."--".$savedir.$filename);
			if( !move_uploaded_file( $file, $savedir.$filename ) ) {
				FUNC_CLASS::alert( "���� ���ε带 �����߽��ϴ�.\\n���ϸ� : ".$file_name );
				exit;
			}

			chmod( $savedir.$filename, 0606 );

			/*
			if( !unlink($file) ) {
				FUNC_CLASS::alert( "�ӽ� ������ ������ �� �����ϴ�." );
				exit;
			}
			*/

			return $filename;
		}
	}

	// �̹��� ���� ���ε��Ҷ� ȣ���ϸ� �ȴ�.
	function imgUploadFile( $file, $file_name, $savedir, $orgfilename=false ) {
		if( $file != "none" && !empty($file) ) {
			$ext		=	strrchr( $file_name, "." );
			$name		=	str_replace( $ext, "", $file_name );
			$ext		=	eregi_replace( "^[.]", "", $ext );
			$name		=	eregi_replace( "[.]*$", "", $name );

			$imginfo	=	getImageSize( $file );
			if( !$imginfo[mime] ) {
				FUNC_CLASS::alert( "IMAGE �� FLASH ���ϸ�\\n���ε尡 �����մϴ�." );
				exit;
			}

			if( !$orgfilename ) {
				$filename = date( "YmdHis" ).".".$ext;
				$i = 1;
				while( file_exists( $savedir.$filename ) ) {
					$filename = date("YmdHis",mktime())."_".$i.".".$ext;
					$i++;
				}
			}
			else {
				$filename = $file_name;
				$i = 1;
				while( file_exists( $savedir.$filename ) ) {
					$filename = $name."_".$i.".".$ext;
					$i++;
				}
			}
			//if( !copy( $file, $savedir.$filename ) ) {
			if( !move_uploaded_file( $file, $savedir.$filename ) ) {
				FUNC_CLASS::alert( "���� ���ε带 �����߽��ϴ�.\\n���ϸ� : ".$file_name );
				exit;
			}

			chmod( $savedir.$filename, 0606 );

			/*
			if( !unlink($file) ) {
				FUNC_CLASS::alert( "�ӽ� ������ ������ �� �����ϴ�." );
				exit;
			}
			*/

			return $filename;
		}
	}

	function uploadFileGood( $objname, $filename ) {
		global $_FILES;

		$save_dir = $_SERVER['DOCUMENT_ROOT']."/good_image/";
		$tmp_name = $_FILES[$objname][tmp_name];
		$name = $_FILES[$objname][name];

		if( $tmp_name ) {
			$ext		=	strrchr( $name, "." );

			$imginfo	=	getImageSize( $tmp_name );

			if( !eregi( "image/", $imginfo[mime] ) ) {
				FUNC_CLASS::alert( "IMAGE ���ϸ�\\n���ε尡 �����մϴ�." );
				exit;
			}

			$save_name = $filename.$ext;
			//if( !copy( $tmp_name, $save_dir."/".$save_name ) ) {
			if( !move_uploaded_file( $tmp_name, $save_dir.$save_name ) ) {
				FUNC_CLASS::alert( "���� ���ε带 �����߽��ϴ�.\\n���ϸ� : ".$save_name );
				exit;
			}
			chmod( $save_dir.$save_name, 0606 );

			/*
			if( !unlink($tmp_name) ) {
				FUNC_CLASS::alert( "�ӽ� ������ ������ �� �����ϴ�." );
				exit;
			}
			*/

			return $save_name;
		}
	}

	// ���� �̸����� ������ ����
	function copyFile( $file_name, $savedir1, $savedir2 ) {
		if( !empty($file_name) ) {
			$pos = strrpos( $file_name, "." );
			$name = substr( $file_name, 0, $pos );
			$ext = substr( $file_name, $pos + 1 );

			$i = 1;
			$filename = $name.".".$ext;
			while( file_exists($savedir2.$filename) ) {
				$filename = $name."_".$i.".".$ext;
				$i++;
			}

			@copy( $savedir1.$file_name, $savedir2.$filename );

			return $filename;
		}
	}

	// �ٸ� �̸����� ������ ����
	function copyFileWithName( $source_file, $savedir1, $target_file_name_no_ext, $savedir2 ) {
		if( !empty($source_file) ) {
			$pos = strrpos( $source_file, "." );
			$name = substr( $source_file, 0, $pos );
			$ext = substr( $source_file, $pos + 1 );

			$target_file = $target_file_name_no_ext.".".$ext;

			@copy( $savedir1.$source_file, $savedir2.$target_file );

			return $target_file;
		}
	}


	/*
	 ������ �ٿ�ε� �ް��� �Ҷ� ����ϴ� �Լ��̴�. 
	 ������ �ڱ� �ڽ��� �������� ���÷��� �ϸ鼭 
	 �Լ��� ȣ���ϴ� ������� �ϸ�ȴ�.
	*/
	function downloadFile($filename, $saveDir) {
		$filepath=$saveDir.$filename;
		$encode_file = $filename;
		$fileSize = @filesize( $filepath );

		if( eregi( ".(ph|inc|php[0-9a-z]*|phtml|inc|cgi|asp|html|htm|bak|sql|old)$", $filename ) ) exit;

		if( !($filename && $saveDir && is_file( $filepath ) ) ) exit;

		$HTTP_USER_AGENT = $_SERVER["HTTP_USER_AGENT"];

		header("Cache-Control: cache, must-revalidate");
		if( strstr( $HTTP_USER_AGENT, "MSIE 6." ) ) { 
			/*
			header("Content-type: application/octetstream"); 
			header("Content-disposition: filename=$encode_file"); 
			*/
			Header("Content-type: application/x-msdownload");
			Header("Content-Disposition: attachment; filename=$encode_file");
			Header("Content-Transfer-Encoding: binary");
			Header("Content-Length: $fileSize");
			Header("Pragma: no-cache");
			Header("Expires: 0"); 
		} 
		else if( strstr( $HTTP_USER_AGENT, "MSIE 5.5" ) ) { 
			header("Content-Type: doesn/matter"); 
			header("Content-disposition: filename=$encode_file"); 
			header("Content-Transfer-Encoding: binary");
			Header("Content-Length: $fileSize");
			header("Pragma: no-cache"); 
			header("Expires: 0"); 
		} 
		else if( strstr( $HTTP_USER_AGENT, "MSIE 5.0" ) ) { 
			Header("Content-type: file/unknown"); 
			header("Content-Disposition: attachment; filename=$encode_file"); 
			Header("Content-Description: PHP3 Generated Data"); 
			Header("Content-Length: $fileSize");
			header("Pragma: no-cache"); 
			header("Expires: 0"); 
		} 
		else { 
			Header("Content-type: file/unknown"); 
			header("Content-Disposition: attachment; filename=$encode_file"); 
			Header("Content-Description: PHP3 Generated Data"); 
			Header("Content-Length: $fileSize");
			header("Pragma: no-cache"); 
			header("Expires: 0"); 
		} 

		//@readfile($filepath);
		$fp = fopen( $filepath, "r" );
		do {
			$data = fread( $fp, 8192 );
			if( strlen($data) == 0 ) {
				break;
			}
			echo $data;
		} while(true);
		fclose ($fp);

		exit;
	}


	// ������ �����Ҷ� ȣ���ϸ� �ȴ�.
	function deleteFile( $file ) {
		if( eregi( "/shopuser/|/skintype/|/shopadmin/", $file ) ) return;

		if( file_exists( $file ) ) @unlink( $file );
	}

	// ���� ����
	function fileWrite( $file_name, $text, $mode='w' ) {
		$rtn = false;

		$fp = fopen( $file_name, $mode );
		if( $fp ) {
			fwrite( $fp, $text );
			fclose( $fp );

			$rtn = true;
		}

		return $rtn;
	}

	// addslashes
	function addSlash( $text ) {
		$text = addslashes($text);
		$text = eregi_replace( "\\\'", "'", $text );

		return $text;
	}

	// ������ ȭ�ϳ����� �о ���ڿ��� �����Ѵ�.
    function fileRead($file){
        if(!$fp = fopen("$file","r")) echo "file open error";
        $file_size = filesize($file);
        $text = fread($fp,$file_size);
        fclose($fp);
        return $text;
    }//func. read_file()

	// ���� �ý��� ��� ���
	function getSyspath($file) {
		return str_replace($file, "", $_SERVER["DOCUMENT_ROOT"].$PHP_SELF);
	}

	// ��Ʈ �ý��� ���� ��� ���
	function getServerSys() {
		return $_SERVER["DOCUMENT_ROOT"];
	}


	function _chk_upload_file( $file_name ) {
		$rtn = true;

		$ext = array( 'cgi', 'htm', 'inc', 'js', 'lib', 'php', 'phtml', 'pl', 'sh' );
		$regex = "\.".implode( "|\.", $ext );
		if( eregi( $regex, $file_name ) ) $rtn = false;

		if( $file_name[0] == "." ) $rtn = false;

		return $rtn;
	}

	function upload_file( $tmp_name, $name, $save_dir, $overwrite=false, $mkdir=false ) {
		if( !( $tmp_name && $name ) ) {
			FUNC_CLASS::alert( "[���Ͼ��ε� ����] �������� ����", false );
			
			return "";
		}

		$save_dir = eregi_replace( "\/$", "", $save_dir );

		if( $mkdir ) {
			if( !is_dir( $save_dir ) ) {
				mkdir( $save_dir );
				chmod( $save_dir, 0757 );
			}
		}

		if( !is_dir( $save_dir ) ) {
			FUNC_CLASS::alert( "[���Ͼ��ε� ����] ���丮 ����", false );

			return "";
		}

		if( !is_writable( $save_dir ) ) {
			FUNC_CLASS::alert( "[���Ͼ��ε� ����] ���丮 ���� ����", false );

			return "";
		}

		$file = explode_file( $name );

		if( !$this->_chk_upload_file( $name ) ) {
			FUNC_CLASS::alert( "[���Ͼ��ε� ����] ���ε� �Ұ��� ����", false );
			
			return "";
		}

		$file[name] = str_replace( " ", "_", $file[name] );

		$file_name = $file[name].".".$file[ext];

		if( $overwrite ) {
			if( is_file( $save_dir."/".$file_name ) && !is_writable( $save_dir."/".$file_name ) ) {
				FUNC_CLASS::alert( "[���Ͼ��ε� ����] ���� ����� �Ұ�", false );

				return "";
			}
		}
		else {
			$i = 1;
			while( is_file( $save_dir."/".$file_name ) ) {
				$file_name = $file[name]."[".$i."].".$file[ext];

				$i++;
			}
		}

		if( !move_uploaded_file( $tmp_name, $save_dir."/".$file_name ) ) {
			FUNC_CLASS::alert( "[���Ͼ��ε� ����] ".$file_name, false );

			return "";
		}

		chmod( $save_dir."/".$file_name, 0606 );

		return $file_name;
	}

	function delete_file( $file ) {
		if( eregi( "/shopuser/|/skintype/|/shopadmin/", $file ) ) return;
		if( !is_file( $file ) ) return;
		if( !is_writable( $file ) ) return;

		@unlink( $file );
	}

	function write_file( $file, $text, $mode='w' ) {
		$rtn = false;

		$fp = fopen( $file, $mode );

		if( $fp ) {
			fwrite( $fp, $text );
			fclose( $fp );

			$rtn = true;
		}

		return $rtn;
	}

	function read_file( $file ) {
		$fp = fopen( $file, "r" );

		if( $fp ) {
			$filesize = filesize( $file );
			$text = fread( $fp, $filesize );
			fclose( $fp );
		}

		return $text;
	}




}
?>