<?php namespace SuperPowers\Library;

use SuperPowers\Core\SuperObject;
use Intervention\Image\ImageManagerStatic as InterventionImage;
use ColorThief\ColorThief;

class Image extends SuperObject {

	/**
	 * @param string $path
	 * @return string
	 */
	function getExtension($path) {
		$type = exif_imagetype($path);
		return $this->getExtensionFromType($type);
	}

	function getExtensionFromType($type) {
		switch ($type) {
			case IMAGETYPE_GIF:
				return 'gif';
			case IMAGETYPE_JPEG:
				return 'jpg';
			case IMAGETYPE_PNG:
				return 'png';
			case IMAGETYPE_BMP:
				return 'bmp';
			case IMAGETYPE_ICO:
				return 'ico';
		}
	}

	function getColor($path) {
		return ColorThief::getColor($path);
	}

	function uploadBase64Image($name, $data) {

		$upload_dir       = wp_upload_dir();
		$upload_path      = str_replace( '/', DIRECTORY_SEPARATOR, $upload_dir['path'] ) . DIRECTORY_SEPARATOR;

		$imageName = md5( $name . microtime() ) . '_' . $name;

		$data = explode(',',$data);

		$imageData = base64_decode($data[1]);

		$f = finfo_open();
		$mime_type = finfo_buffer($f, $imageData, FILEINFO_MIME_TYPE);
		$ext = str_replace('image/', '', $mime_type);

		$filepath = "{$upload_path}{$imageName}.{$ext}";
		$fileurl = "{$upload_dir['url']}{$imageName}.{$ext}";

		file_put_contents( $filepath, $imageData );

		return array(
			'url' => $fileurl,
			'type' => $mime_type,
			'file' => $filepath
		);
	}

	function saveBase64Image($imageStringData, $nameWithoutExtention)
	{
		$data = explode(',',$imageStringData);

		$imageData = base64_decode($data[1]);
		$image = imagecreatefromstring($imageData);

		if($image) {
			$f = finfo_open();
			$mime_type = finfo_buffer($f, $imageData, FILEINFO_MIME_TYPE);
			$ext = str_replace('image/', '', $mime_type);

			$dest = $this->getBaseDir() . "/{$nameWithoutExtention}.{$ext}";
			$img = InterventionImage::make($image);
			$this->file->ensureStructure($dest);
			$img->save($dest, 100);

			return $dest;
		}

		return false;
	}

	function create($source, $dest, $width, $height, $fit = true) {

		$img = InterventionImage::make($source);

		if($fit) {
			$img->fit($width, $height);
		} else {
			$img->resize($width, $height, function ($constraint) {
				$constraint->aspectRatio();
			});
		}

		$this->file->ensureStructure($dest);
		$img->save($dest, 100);

		return $dest;
	}

	function clearImagesForPost($postId){
		$base = wp_upload_dir();

		$path = "{$base['basedir']}/generated/{$postId}";
		$this->file->deleteDir($path);
	}

	function getBaseUrl(){
		$base = wp_upload_dir();
		return $base['baseurl'];
	}

	function getBaseDir(){
		$base = wp_upload_dir();
		return $base['basedir'];
	}
}