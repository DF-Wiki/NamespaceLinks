<?php
/*
 * Extension:NamespaceLinks
 *
 * Allows links to default to different namespaces based on their namespace.
 */

require_once 'NamespaceLinks.body.php';

$wgHooks['InternalParseBeforeLinks'][] = 'NLHooks::parseLinks';
 
$wgExtensionCredits['parserhook'][] = array(
	'path' => __FILE__,
	'name' => 'NamespaceLinks',
	'author' => 'Lethosor',
	'version' => '0.0',
	'description' => 'Allows links to default to different namespaces based on their namespace.',
	'url' => 'https://github.com/lethosor/mw-namespace-links',
);

//$t = new Title('DF2012:abc');
//print $t->getNamespace();
