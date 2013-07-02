<?php



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
		$text .= '<br>InternalParseBeforeLinks';
		return true;
	}
}


