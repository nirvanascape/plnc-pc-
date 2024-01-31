<?
CLASS thumbnail {

	var $source_dir;
	var $source_file;
	var $source_ext;
	var $target_dir;
	var $target_file;
	var $max_width;
	var $max_height;
	var $source_width;
	var $source_height;
	var $new_width;
	var $new_height;

	function sourceImageSize() {
		$size = @getImageSize( $this->source_dir."/".$this->source_file );

		$this->source_width = $size[0];
		$this->source_height = $size[1];

		switch( $size[mime] ) {
			case "image/jpeg" :
				$ext = "JPEG";
				break;
			case "image/gif" :
				$ext = "GIF";
				break;
			case "image/png" :
				$ext = "PNG";
				break;
		}

		$this->source_ext = $ext;
	}
	
	function newImageSize() {
		if( $this->source_width >= $this->max_width && $this->source_height <= $this->max_height ) {
			$WIDTH = $this->max_width;
			$HEIGHT = $this->max_width * ( $this->source_height / $this->source_width );
		}
		else if( $this->source_height >= $this->max_height && $this->source_width <= $this->max_width ) {
			$WIDTH = $this->max_height * ( $this->source_width / $this->source_height );
			$HEIGHT = $this->max_height;
		}
		else if( $this->source_width >= $this->max_width && $this->source_height >= $this->max_height ) {
			if( $this->source_width > $this->source_height ) {
				$WIDTH = $this->max_width;
				$HEIGHT = $this->source_height * ( $this->max_width / $this->source_width );
			}
			else {
				$HEIGHT = $this->max_height;
				$WIDTH = $this->source_width * ( $this->max_height / $this->source_height );
			}
		}
		else {
			$WIDTH = $this->source_width;
			$HEIGHT = $this->source_height;
		}

		$this->new_width = $WIDTH;
		$this->new_height = $HEIGHT;
	}

	function newImage( $source_dir, $source_file, $target_dir, $target_file, $width, $height ) {
		$this->source_dir = $source_dir;
		$this->source_file = $source_file;
		$this->target_dir = $target_dir;
		$this->target_file = $target_file;
		$this->max_width = $width;
		$this->max_height = $height;

		$this->sourceImageSize();
		$this->newImageSize();

		if( $this->source_ext ) {
			switch( $this->source_ext ) {
				case "GIF" :
					$thumb_img = @ImageCreate( $this->new_width, $this->new_height );
					break;
				case "JPEG" :
					$thumb_img = @ImageCreateTrueColor( $this->new_width, $this->new_height );
					break;
				case "PNG" :
					$thumb_img = @ImageCreateTrueColor( $this->new_width, $this->new_height );
					break;
			}

			$trans_color = @imagecolorallocate( $thumb_img, 255, 255, 255 );
			@imagecolortransparent( $thumb_img, $trans_color );

			$newFuncName = "ImageCreateFrom".$this->source_ext;
			if( function_exists( $newFuncName ) ) {
				$full_img = @$newFuncName( $this->source_dir."/".$this->source_file );
			}

			if( $full_img ) {
				@imagecopyresampled( $thumb_img, $full_img, 0, 0, 0, 0, $this->new_width, $this->new_height, ImageSX( $full_img ), ImageSY( $full_img ) ); 
				@ImageDestroy( $full_img );
			}

			$newFuncName = "Image".$this->source_ext;
			if( function_exists( $newFuncName ) ) {
				$new_img = @$newFuncName( $thumb_img, $this->target_dir."/".$this->target_file );
				@ImageDestroy( $thumb_img );
			}
		}
	}

	function newImageNewFileName( $source_dir, $source_file, $target_dir, $new_file_name, $width, $height ) {
		$pos = strrpos( $source_file, "." );
		$ext = substr( $source_file, $pos + 1 );

		$target_file = $new_file_name.".".$ext;

		$this->newImage( $source_dir, $source_file, $target_dir, $target_file, $width, $height );

		return $target_file;
	}

}

/*
$th = new thumbnail();
$th->newImage( "/home/dearmall/master/data_file", "hyun1.jpg", "/home/dearmall/master/data_file", "testtest.jpg", 120, 120 );
*/
?>