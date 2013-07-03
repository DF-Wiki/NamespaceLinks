<?php

if (!defined('NS_MAIN')) {
	define('NS_MAIN', 0);
}

class NLLink {
	/*
	 * A basic link
	 *
	 * Extracts page (with namespace) and text from a wiki link, but not
	 * the suffix (if it exists)
	 */
	public $contents;
	public $hasNS;
	
	public function __construct ($text) {
		/*
		 * Takes a string of text in wiki link format
		 */
		// Safety check
		$matches = array();
		$isLink = preg_match('/\[\[[^]]+\]\]/', $text, $matches);
		if (!$isLink) {
			trigger_error("`$text` does not contain a valid link.");
		}
		$linkText = $matches[0];
		// Remove leading [[ and trailing ]]
		$linkContents = substr($linkText, 2, strlen($linkText) - 4);
		$this->contents = $linkContents;
		
		/* $parts = preg_split('/:/', $linkContents, 2);
		$linkNS = '';
		if (count($parts) == 2) {
			$linkNS = $parts[0];
			$linkContents = $parts[1];
		} */
		
		$wtitle = Title::newFromText($linkContents);
		$this->wtitle = $wtitle;
		
		/* Check for an explicit namespace by comparing the link contents
		 * with $wtitle->mUserCaseDBKey (which is the original, case-sensitive
		 * title). If they aren't equal, then that means a namespace was
		 * parsed out
		 */
		
		
		$hasNS = false;
		
		if (strpos($linkContents, $wtitle->mUserCaseDBKey) > 0 && !$wtitle->mInterwiki) {
			$hasNS = true;
		}
		
		$this->hasNS = $hasNS;
		
	}
	public function render () {
		/*
		 * Render the link
		 *
		 * Returns [[ns:title|text]]
		 */
		return;
	}
}

class NLHooks {
	static public function onLinkBegin ($skin, $target, &$text, &$customAttribs, &$query, &$options, &$ret) {
		//print "$target//$text<br>\n";
		//print_r($target);
		//print $text;
		//print $target->setNamespace(1000) . "\n";
		$text = "$text Hello";
		return true;
	}
	static public function parseLinks (&$parser, &$text) {
		//$t = new Title('DF2012:abc');
		//$text .= Linker::link($t);
		//$text .= "<pre>" . var_export($t) . "</pre>";
		//$t = Title::newFromText('Main:Abc');
		//$text .= "<pre>".print_r($t, true)."</pre>";
		$l = new NLLink('[[wikipedia:Cat]]');
		$text .= "<pre>".print_r($l, true)."</pre>";
		return true;
	}
}


