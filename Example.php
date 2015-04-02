<?php
/**
 * This PHP entry point is deprecated. Please use wfLoadExtension() and the extension.json file
 * instead. See https://www.mediawiki.org/wiki/Manual:Extension_registration for more details.
 */
if ( !function_exists( 'wfLoadSkin' ) ) {
	die( 'The Example skin requires MediaWiki 1.25 or newer.' );
}

wfLoadSkin( 'Example' );
