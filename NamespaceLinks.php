<?php
/*
 * Extension:NamespaceLinks
 *
 * Allows links in specified namespaces to default to namespaces other than the
 * main namespace.
 */

require_once 'NamespaceLinks.body.php';

$wgExtensionMessagesFiles['NamespaceLinks'] = dirname( __FILE__ ) . '/NamespaceLinks.i18n.php';

$wgHooks['ParserFirstCallInit'][] = 'NLHooks::init';
$wgHooks['InternalParseBeforeLinks'][] = 'NLHooks::parseLinks';

$wgExtensionCredits['parserhook'][] = array(
	'path' => __FILE__,
	'name' => 'NamespaceLinks',
	'author' => 'Lethosor',
	'version' => '0.1',
	'description' => 'Allows links to default to different namespaces based on their namespace.',
	'url' => 'https://github.com/lethosor/mw-namespace-links',
);
