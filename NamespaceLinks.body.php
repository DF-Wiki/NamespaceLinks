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
	public $title;
	public $ns;
	public $text;
	
	public function __construct ($text) {
		/*
		 * Takes a string of text in wiki link format
		 *
		 * Examples:
		 * [title] -> $title=title, $ns=NS_MAIN, $text=title
		 * [ns:title] -> $title=title, $ns=ns, $text=title
		 * [title|text] -> $title=title, $ns=NS_MAIN, $text=text
		 * [ns:title|text] -> $title=title, $ns=ns, $text=text
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
		// Separate namespace
		$parts = preg_split('/:/', $linkContents, 2);
		if (count($parts) == 1) {
			// NS is main by default
			$this->ns = 'Main';
			$linkNS = '';
		}
		else {
			// Extract namespace from link
			$linkNS = $parts[0];
			$this->ns = $parts[0];
			$linkContents = $parts[1];
		}
		// Check to see if link text is provided
		$parts = preg_split('/\|/', $linkContents, 2);
		if (count($parts) == 1) {
			// No text, so link title is text
			$linkContents = "$linkContents|$linkContents";
		}
		$parts = preg_split('/\|/', $linkContents);
		$this->title = $parts[0];
		$this->text = $parts[1];
	}
	public function render () {
		/*
		 * Render the link
		 *
		 * Returns [[ns:title|text]]
		 */
		return "[[{$this->ns}:{$this->title}|{$this->text}]]";
	}
}

//$l = new NLLink('[[a:b]]');
//print $l->render();

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
		//$text .= "<pre>" . $parser->replaceInternalLinks($text) . "</pre>";
		//$text .= '<br>InternalParseBeforeLinks2<br>';
		//$t = new Title('DF2012:abc');
		//$text .= Linker::link($t);
		//$text .= "<pre>" . var_export($t) . "</pre>";
		$t = Title::newFromText('DF2012:Abc');
		$text .= "<pre>".print_r($t, true)."</pre>";
		return true;
	}
}


