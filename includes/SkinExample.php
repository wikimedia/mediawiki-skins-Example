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
		return $data + $tplData;
	}
}
