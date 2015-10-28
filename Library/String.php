<?php namespace SuperPowers\Library;

use SuperPowers\Core\SuperObject;

class String extends SuperObject {

	public function markWords($text, $words, $length = null) {

		$words = array_map('preg_quote', $words);

		$_words = array();
		foreach($words as $word) {
			if(!empty($word)){
				$_words[] = $word;
			}
		}

		$text = preg_replace('/(' . implode('|', $_words) .')/iu', '<span class="search-hit">\0</span>', $text);
		$firstHit = mb_stripos($text, '<span class="search-hit">');

		if ($firstHit > 0)
		{
			$firstHit -= 150;

		}

		if ($firstHit < 0)
		{
			$firstHit = 0;
		}

		if($length != null) {
			$text = $this->post->limitContent($text, $length + 20, $firstHit);
		}
		return $text;
	}

}