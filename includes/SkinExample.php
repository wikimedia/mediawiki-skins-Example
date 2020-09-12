<?php
/**
 * SkinTemplate class for the Example skin
 *
 * @ingroup Skins
 */
class SkinExample extends SkinMustache {
	public $template = 'ExampleTemplate';

	/**
	 * @inheritDoc
	 */
	public function getTemplateData() {
		return parent::getTemplateData() + [
			'example-main-page-url' => self::makeMainPageUrl(),
		];
	}
}
