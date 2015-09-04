<?php namespace SuperPowers\Library;

use SuperPowers\Core\SuperObject;

class Request extends SuperObject {

	function create($url, $data = null) {
		// OK cool - then let's create a new cURL resource handle
		$ch = curl_init();

		// Now set some options (most are optional)

		// Set URL to download
		curl_setopt($ch, CURLOPT_URL, $url);

		// Set a referer
		//curl_setopt($ch, CURLOPT_REFERER, "http://www.example.org/yay.htm");

		// User agent
		//curl_setopt($ch, CURLOPT_USERAGENT, "MozillaXYZ/1.0");

		// Include header in result? (0 = yes, 1 = no)
		curl_setopt($ch, CURLOPT_HEADER, 0);

		// Should cURL return or print out the data? (true = return, false = print)
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		// Timeout in seconds
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);

		// Download the given URL, and return output
		$output = curl_exec($ch);

		// Close the cURL resource, and free system resources
		curl_close($ch);

		return json_decode($output, true);
	}


	function file_get_contents_curl($url)
	{
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

		$data = curl_exec($ch);
		curl_close($ch);

		return $data;
	}

	function getMeta($url) {

		$cache = $this->cache->getPageMeta($url);
		if(!empty($cache)){
			return $cache;
		}

		$html = $this->file_get_contents_curl($url);

		$doc = new \DOMDocument();
		@$doc->loadHTML($html);
		$nodes = $doc->getElementsByTagName('title');

		$title = $nodes->item(0)->nodeValue;

		$metas = $doc->getElementsByTagName('meta');

		$values = array(
			'title' => $title
		);

		for ($i = 0; $i < $metas->length; $i++) {
			$meta = $metas->item($i);

			$id = $meta->getAttribute('name');

			if (empty($id)) {
				$id = $meta->getAttribute('property');
			}

			if (!empty($id)) {
				$values[$id] = $meta->getAttribute('content');
			}
		}

		$this->cache->storePageMeta($url, $values);

		return $values;
	}




}