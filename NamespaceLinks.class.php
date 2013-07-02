<?php

class NamespaceLinks {
	static public function onLinkBegin ($skin, $target, &$text, &$customAttribs, &$query, &$options, &$ret) {
		//print "$target//$text<br>\n";
		//print_r($target);
		//print $text;
		//print $target->setNamespace(1000) . "\n";
		$text = "$text Hello";
		return true;
	}
}


