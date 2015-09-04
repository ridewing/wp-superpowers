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
		$img->save($dest);

		return $dest;
	}

	function clearImagesForPost($postId){
		$base = wp_upload_dir();

		$path = "{$base['basedir']}/generated/{$postId}";
		$this->file->deleteDir($path);
	}
}