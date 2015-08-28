<?php namespace SuperPowers\Library;

use SuperPowers\Core\SuperObject;

class File extends SuperObject {

	function ensureStructure($path, $mode = 0775)
	{
		$dir = dirname($path);

		if (!file_exists($dir))
			return mkdir($dir, $mode, true);
		else
			return true;
	}

	public function deleteDir($dirPath) {
		if (! is_dir($dirPath)) {
			return false;
		}
		if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
			$dirPath .= '/';
		}
		$files = glob($dirPath . '*', GLOB_MARK);
		foreach ($files as $file) {
			if (is_dir($file)) {
				self::deleteDir($file);
			} else {
				unlink($file);
			}
		}
		rmdir($dirPath);
	}
}