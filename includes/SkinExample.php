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
		$data = parent::getTemplateData();
		$tpl = $this->prepareQuickTemplate();
		$tplData = $tpl->execute();
		return $data + $tplData + [
			'html-example-logo' => Html::element(
				'a',
				[
					'href' => self::makeMainPageUrl(),
					'class' => 'mw-wiki-logo',
				] + Linker::tooltipAndAccesskeyAttribs( 'p-logo' )
			),
		];
	}
}
