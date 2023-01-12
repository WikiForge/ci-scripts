<?php

use MediaWiki\MediaWikiServices;
use Wikimedia\Rdbms\DBQueryError;
use Wikimedia\Rdbms\DBUnexpectedError;

$wgWikimediaJenkinsCI = true;

define( 'CW_DB', 'wikidb' );

require_once "$IP/extensions/CreateWiki/includes/WikiInitialise.php";
$wi = new WikiInitialise();

$wi->setVariables(
	"$IP/cache",
	[
		''
	],
	[
		'127.0.0.1' => ''
	]
);

$wi->config->settings += [
	'cwClosed' => [
		'default' => false,
	],
	'cwInactive' => [
		'default' => false,
	],
	'cwPrivate' => [
		'default' => false,
	],
	'cwExperimental' => [
		'default' => false,
	],
];

$wgCreateWikiGlobalWiki = 'wikidb';
$wgCreateWikiDatabase = 'wikidb';
$wgCreateWikiCacheDirectory = "$IP/cache";

$wgHooks['MediaWikiServices'][] = 'insertWiki';

function insertWiki( MediaWikiServices $services ) {
	try {
		$dbw = wfDBConnection();

		$dbw->insert(
			'cw_wikis',
			[
				'wiki_dbname' => 'wikidb',
				'wiki_dbcluster' => 'c1',
				'wiki_sitename' => 'TestWiki',
				'wiki_language' => 'en',
				'wiki_private' => (int)0,
				'wiki_creation' => $dbw->timestamp(),
				'wiki_category' => 'uncategorised',
				'wiki_closed' => (int)0,
				'wiki_deleted' => (int)0,
				'wiki_locked' => (int)0,
				'wiki_inactive' => (int)0,
				'wiki_inactive_exempt' => (int)0,
				'wiki_url' => 'http://127.0.0.1:9412'
			],
			__METHOD__,
			[ 'IGNORE' ]
		);
	} catch ( DBQueryError | DBUnexpectedError $e ) {
		return;
	}
}

function wfDBConnection() {
	return MediaWikiServices::getInstance()
		->getDBLoadBalancer()
		->getMaintenanceConnectionRef( DB_PRIMARY );
}

$wi->readCache();
$wi->config->extractAllGlobals( $wi->dbname );
$wgConf = $wi->config;

?>
