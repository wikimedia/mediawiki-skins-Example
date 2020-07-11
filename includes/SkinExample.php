<?php
/**
 * SkinTemplate class for the Example skin
 *
 * @ingroup Skins
 */
class SkinExample extends SkinTemplate {
	public $skinname = 'example',
		$stylename = 'Example',
		$template = 'ExampleTemplate';

	/**
	 * Add CSS via ResourceLoader
	 *
	 * @param OutputPage $out
	 */
	public function initPage( OutputPage $out ) {
		$out->addMeta( 'viewport',
			'width=device-width, initial-scale=1.0, ' .
			'user-scalable=yes, minimum-scale=0.25, maximum-scale=5.0'
		);

		$out->addModuleStyles( [
			'skins.example'
		] );
		$out->addModules( [
			'skins.example.js'
		] );
	}

	/**
	 * @param OutputPage $out
	 */
	function setupSkinUserCss( OutputPage $out ) {
		parent::setupSkinUserCss( $out );
	}
}
