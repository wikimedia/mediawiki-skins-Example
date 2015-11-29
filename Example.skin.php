<?php
/**
 * SkinTemplate class for the Example skin
 *
 * @ingroup Skins
 */
class SkinExample extends SkinTemplate {
	public $skinname = 'example', $stylename = 'Example',
		$template = 'ExampleTemplate', $useHeadElement = true;

	/**
	 * Add CSS via ResourceLoader
	 *
	 * @param $out OutputPage
	 */
	public function initPage( OutputPage $out ) {

		$out->addMeta( 'viewport', 'width=device-width, initial-scale=1.0' );

		$out->addModuleStyles( array(
			'mediawiki.skinning.interface',
			'mediawiki.skinning.content.externallinks',
			'skins.example'
		) );
		$out->addModules( array(
			'skins.example.js'
		) );
	}

	/**
	 * @param $out OutputPage
	 */
	function setupSkinUserCss( OutputPage $out ) {
		parent::setupSkinUserCss( $out );
	}
}
