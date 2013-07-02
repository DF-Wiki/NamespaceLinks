<?php
/*
 * Extension:NamespaceLinks
 *
 * Allows links to default to different namespaces based on their namespace.
 */

$wgAutoloadClasses['NamespaceLinks'] = dirname(__FILE__) . '/NamespaceLinks.class.php';

$wgHooks['LinkBegin'][] = 'NamespaceLinks::onLinkBegin';
 
$wgExtensionCredits['parserhook'][] = array(
	'path' => __FILE__,
	'name' => 'NamespaceLinks',
	'author' => 'Lethosor',
	'version' => '0.0',
	'description' => 'Allows links to default to different namespaces based on their namespace.',
	'url' => 'https://github.com/lethosor/mw-namespace-links',
);

