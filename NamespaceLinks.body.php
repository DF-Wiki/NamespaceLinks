<?php

$wgNLConfigMap = array();
$wgNLEnabled = true;

function NLReplaceLinks ($text, $nsText) {
	/*
	 * Assign all links in the given text with no namespace the namespace
	 * given by $nsText
	 */
	$links = array();
	preg_match_all('/\[\[[^]]+\]\]/', $text, $links);
	$links = $links[0];
	foreach ($links as $linkText) {
		if ($linkText[2] == '#') {
			// Skip section links within current article (e.g. [[#section]])
			continue;
		}
		if ($linkText[2] == '/') {
			continue;
		}
		// Replace multiple leaading :'s with a single :
		$linkText = preg_replace('/^\[\[:+/', '[[:', $linkText);
		if (preg_match('/::/', $linkText)) {
			// Skip links that have double colons, like MediaWiki
			continue;
		}
		$link = new NLLink($linkText);
		if (!$link->valid) {
			continue;
		}
		if ($link->ns == NS_IMAGE || $link->ns == NS_CATEGORY) {
			continue;
		}
		if (!$link->hasNS) {
			$link->nsText = $nsText;
		}
		$text = str_replace($linkText, $link->render(), $text);
	}
	return $text;

}

function NLNSNameToID ($nsName) {
	$t = Title::newFromText("$nsName:Dummy text");
	return $t->mNamespace;
}

function NLParseConfig ($text) {
	/*
	 * Parses configuration text, in the following format:
	 *
	 * *ns=defaultns
	 * *ns2=defaultns2
	 * ...
	 *
	 * Returns an array of old_ns_id => new_ns_name pairs (for convenience)
	 */
	$map = array();
	$text = preg_replace('/\n+/', "\n", $text);
	$text = str_replace("\r", "", $text);
	$lines = preg_split('/\n/', $text);
	foreach ($lines as $line) {
		$line = preg_replace('/^\*/', '', $line);
		$parts = preg_split('/=/', $line, 2);
		if (count($parts) == 1) { // bad syntax
			continue;
		}
		$map[NLNSNameToID($parts[0])] = $parts[1];
	}
	return $map;
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
		$this->valid = true;
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

		if ($text == '') {
			$this->valid = false;
			return;
		}

		if ($text[0] == ':') {
			/* For links like [[:Page]] (as in MediaWiki:Searchmenu-new)
			 * or [[:Category:Name]] (for escaping category links),
			 * the colon can safely be left off the text displayed
			 * in the link.
			 */
			$text = substr($text, 1);
		}

		$wtitle = Title::newFromText($title);
		$this->wtitle = $wtitle;

		if ($wtitle == null) {
			$this->valid = false;
			return;
		}

		/* Check for an explicit namespace by comparing the link contents
		 * with $wtitle->getUserCaseDBKey() (which is the original, case-sensitive
		 * title). If they aren't equal, a namespace was parsed out.
		 */


		$hasNS = false; // Whether the link has an *explicit* namespace
		$nsText = '';
		if ($wtitle->getUserCaseDBKey() == '') {
			$this->valid = false;
			return;
		}

		// replace spaces with _ to match getUserCaseDBKey()
		if (strpos(strtr($linkContents, ' ', '_'), $wtitle->getUserCaseDBKey()) > 1 ||
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
		if (!$this->hasNS && !$this->nsText) {
			$str = "[[{$this->title}|{$this->text}]]";
		}
		else {
			$str = "[[{$this->nsText}:{$this->title}|{$this->text}]]";
		}
		return preg_replace('/([^:]+):+([^|]+)/', '$1:$2', $str);
	}
}

class NLHooks {
	static public function init (&$parser) {
		global $wgNLConfigMap, $wgLanguageCode;
		$wgNLConfigMap = NLParseConfig(wfMessage('namespace-links')->inLanguage($wgLanguageCode)->plain());

		$parser->setFunctionHook('nlenable', 'NLHooks::enable');
		$parser->setFunctionHook('nlenabled', 'NLHooks::enabled');

		return true;
	}
	static public function parseLinks (&$parser, &$text) {
		global $wgNLEnabled;
		if (!$wgNLEnabled) {
			return true;
		}
		global $wgNLConfigMap;
		$currentNSID = $parser->mTitle->getNamespace();
		$defaultNSName = NS_MAIN;
		if (array_key_exists($currentNSID, $wgNLConfigMap)) {
			$defaultNSName = $wgNLConfigMap[$currentNSID];
		}
		$oldText = $text;
		$newText = NLReplaceLinks($text, $defaultNSName);
		$text = $newText;
		return true;
	}
	static public function enable (&$parser, $set_enabled='1') {
		global $wgNLEnabled;
		$set_enabled = strtolower($set_enabled);
		switch ($set_enabled) {
			case '0':
			case 'no':
			case 'false':
			case 'n':
				$enabled = false;
				break;
			default:
				$enabled = true;
				break;
		}
		$wgNLEnabled = $enabled;
	}
	static public function enabled (&$parser) {
		global $wgNLEnabled;
		$args = func_get_args();
		if (count($args) <= 2) {
			// {{#nlenabled:}} or {{#nlenabled:1}}
			return $wgNLEnabled ? ($args[1] ? $args[1] : '1') : '';
		}
		else {
			// {{#nlenabled:1|0}}
			if ($wgNLEnabled) {
				return $args[1];
			}
			else {
				return $args[2];
			}
		}
	}
}

