<?php namespace SuperPowers;

class File extends SuperObject {

	function ensureStructure($path, $mode = 0775)
	{
		$dir = dirname($path);

		if (!file_exists($dir))
			return mkdir($dir, $mode, true);
		else
			return true;
	}
}