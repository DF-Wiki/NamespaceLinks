<?php
/*
 * Extension:NamespaceLinks
 *
 * Allows links to default to different namespaces based on their namespace.
 */

require_once 'NamespaceLinks.body.php';

$wgHooks['ParserFirstCallInit'][] = 'NLHooks::init';
$wgHooks['InternalParseBeforeLinks'][] = 'NLHooks::parseLinks';
 
$wgExtensionCredits['parserhook'][] = array(
	'path' => __FILE__,
	'name' => 'NamespaceLinks',
	'author' => 'Lethosor',
	'version' => '0.0',
	'description' => 'Allows links to default to different namespaces based on their namespace.',
	'url' => 'https://github.com/lethosor/mw-namespace-links',
);

$wgNLConfigText = <<< TEXT
*DF2012=DF2012
*DF2012 talk=DF2012
*v0.31=v0.31
*v0.31 talk=v0.31
TEXT;
