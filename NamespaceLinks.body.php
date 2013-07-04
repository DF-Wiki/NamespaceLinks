<?php

function NLReplaceLinks ($text, $nsText) {
	/*
	 * Assign all links in the given text with no namespace the namespace
	 * given by $nsText
	 */
	$links = array();
	preg_match_all('/\[\[[^]]+\]\]/', $text, $links);
	$links = $links[0];
	foreach ($links as $linkText) {
		$link = new NLLink($linkText);
		if (!$link->hasNS) {
			$link->nsText = $nsText;
		}
		$text = str_replace($linkText, $link->render(), $text);
	}
	return $text;
	
}

function NLParseConfig ($text) {
	$text = preg_replace('/\n+/', "\n", $text);
	$text = str_replace("\r", "", $text);
	$lines = preg_split('/\n/', $text);
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
	public $title;
	public $text;
	public $ns;
	public $nsText;
	
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
		
		$parts = preg_split('/\|/', $linkContents, 2);
		$title = '';
		$text = '';
		if (count($parts) == 2) {
			$title = $parts[0];
			$text = $parts[1];
		}
		else {
			$title = $text = $parts[0];
		}
		
		$wtitle = Title::newFromText($title);
		$this->wtitle = $wtitle;
		
		/* Check for an explicit namespace by comparing the link contents
		 * with $wtitle->mUserCaseDBKey (which is the original, case-sensitive
		 * title). If they aren't equal, then that means a namespace was
		 * parsed out
		 */
		
		
		$hasNS = false;
		$nsText = '';
		
		if (strpos($linkContents, $wtitle->mUserCaseDBKey) > 0 ||
		    $wtitle->mInterwiki ||
		    $wtitle->mNamespace) {
			$hasNS = true;
			$title = preg_split('/:/', $title, 2);
			$nsText = $title[0];
			$title = $title[1];
		}
		
		$this->hasNS = $hasNS;
		
		$this->ns = $wtitle->mNamespace;
		$this->nsText = $nsText;
		$this->title = $title;
		$this->text = $text;
		
		
	}
	public function render () {
		/*
		 * Render the link
		 *
		 * Returns [[ns:title|text]]
		 */
		return "[[{$this->nsText}:{$this->title}|{$this->text}]]";
	}
}

class NLHooks {
	static public function parseLinks (&$parser, &$text) {
		$currentNS = $parser->mTitle->getNamespace();
		$currentNSName = ''; // fill in
		$defaultNSName = 'Masterwork';
		$oldText = $text;
		$newText = NLReplaceLinks($text, $defaultNSName);
		$text = $newText;
		return true;
	}
}


