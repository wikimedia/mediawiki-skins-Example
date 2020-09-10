<?php
/**
 * BaseTemplate class for the Example skin
 * This code will be migrated into SkinExample. Please use SkinExample
 * and skin.mustache from now on.
 *
 * @ingroup Skins
 */
class ExampleTemplate extends BaseTemplate {
	/**
	 * Outputs the entire contents of the page
	 * @return array of template data
	 */
	public function execute() {
		return [
			'html-example-footer' => $this->getFooterHTML(),
		];
	}

	/**
	 * @return string html
	 */
	protected function getFooterHTML() {
		$validFooterIcons = $this->getFooterIcons( 'icononly' );
		$validFooterLinks = $this->getFooterLinks( null );

		$html = '';
		$iconsHTML = '';
		if ( count( $validFooterIcons ) > 0 ) {
			$iconsHTML .= Html::openElement( 'ul', [ 'id' => "footer-icons" ] );
			foreach ( $validFooterIcons as $blockName => $footerIcons ) {
				$iconsHTML .= Html::openElement( 'li', [
					'id' => Sanitizer::escapeIdForAttribute(
						"footer-{$blockName}ico"
					),
					'class' => 'footer-icons'
				] );
				foreach ( $footerIcons as $icon ) {
					$iconsHTML .= $this->getSkin()->makeFooterIcon( $icon );
				}
				$iconsHTML .= Html::closeElement( 'li' );
			}
			$iconsHTML .= Html::closeElement( 'ul' );
		}

		$linksHTML = '';
		if ( count( $validFooterLinks ) > 0 ) {
			$linksHTML .= Html::openElement( 'div', [ 'id' => "footer-list" ] );
			foreach ( $validFooterLinks as $category => $links ) {
				$linksHTML .= Html::openElement( 'ul',
					[ 'id' => Sanitizer::escapeIdForAttribute(
						"footer-{$category}"
					) ]
				);
				foreach ( $links as $link ) {
					$linksHTML .= Html::rawElement(
						'li',
						[ 'id' => Sanitizer::escapeIdForAttribute(
							"footer-{$category}-{$link}"
						) ],
						$this->get( $link )
					);
				}
				$linksHTML .= Html::closeElement( 'ul' );
			}
			$linksHTML .= Html::closeElement( 'div' );
		}

		$html .= $iconsHTML . $linksHTML;

		return $html;
	}
}
