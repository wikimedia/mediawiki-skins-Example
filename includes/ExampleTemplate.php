<?php
/**
 * BaseTemplate class for the Example skin
 *
 * @ingroup Skins
 */
class ExampleTemplate extends BaseTemplate {
	/**
	 * Outputs the entire contents of the page
	 */
	public function execute() {
		$html = '';
		$html .= $this->get( 'headelement' );

		$html .= Html::rawElement( 'div', [ 'id' => 'mw-wrapper' ],
			Html::rawElement( 'div', [ 'class' => 'mw-body', 'role' => 'main' ],
				$this->getSiteNotice() .
				$this->getNewTalk() .
				$this->getIndicators() .
				Html::rawElement( 'h1',
					[
						'class' => 'firstHeading',
						'lang' => $this->get( 'pageLanguage' )
					],
					$this->get( 'title' )
				) .
				Html::rawElement( 'div', [ 'id' => 'siteSub' ],
					$this->getMsg( 'tagline' )->parse()
				) .
				Html::rawElement( 'div', [ 'class' => 'mw-body-content' ],
					Html::rawElement( 'div', [ 'id' => 'contentSub' ],
						$this->getPageSubtitle() .
						Html::rawElement(
							'p',
							[],
							$this->get( 'undelete' )
						)
					) .
					$this->get( 'bodycontent' ) .
					$this->getClear() .
					Html::rawElement( 'div', [ 'class' => 'printfooter' ],
						$this->get( 'printfooter' )
					) .
					$this->getCategoryLinks() .
					$this->getDataAfterContent() .
					$this->get( 'debughtml' )
				)
			) .
			Html::rawElement( 'div', [ 'id' => 'mw-navigation' ],
				Html::rawElement(
					'h2',
					[],
					$this->getMsg( 'navigation-heading' )->parse()
				) .
				$this->getLogo() .
				$this->getSearch() .
				// User profile links
				Html::rawElement(
					'div',
					[ 'id' => 'user-tools' ],
					$this->getUserLinks()
				) .
				// Page editing and tools
				Html::rawElement(
					'div',
					[ 'id' => 'page-tools' ],
					$this->getPageLinks()
				) .
				// Site navigation/sidebar
				Html::rawElement(
					'div',
					[ 'id' => 'site-navigation' ],
					$this->getSiteNavigation()
				)
			) .
			$this->getFooter()
		);

		$html .= $this->getTrail();
		$html .= Html::closeElement( 'body' );
		$html .= Html::closeElement( 'html' );

		echo $html;
	}

	/**
	 * Generates the logo and (optionally) site title
	 * @return string html
	 */
	protected function getLogo( $id = 'p-logo', $imageOnly = false ) {
		$html = Html::openElement(
			'div',
			[
				'id' => $id,
				'class' => 'mw-portlet',
				'role' => 'banner'
			]
		);
		$html .= Html::element(
			'a',
			[
				'href' => $this->data['nav_urls']['mainpage']['href'],
				'class' => 'mw-wiki-logo',
			] + Linker::tooltipAndAccesskeyAttribs( 'p-logo' )
		);
		if ( !$imageOnly ) {
			$html .= Html::element(
				'a',
				[
					'id' => 'p-banner',
					'class' => 'mw-wiki-title',
					'href' => $this->data['nav_urls']['mainpage']['href']
				] + Linker::tooltipAndAccesskeyAttribs( 'p-logo' ),
				$this->getMsg( 'sitetitle' )->text()
			);
		}
		$html .= Html::closeElement( 'div' );

		return $html;
	}

	/**
	 * Generates the search form
	 * @return string html
	 */
	protected function getSearch() {
		$html = Html::openElement(
			'form',
			[
				'action' => htmlspecialchars( $this->get( 'wgScript' ) ),
				'role' => 'search',
				'class' => 'mw-portlet',
				'id' => 'p-search'
			]
		);
		$html .= Html::hidden( 'title', $this->get( 'searchtitle' ) );
		$html .= Html::rawElement(
			'h3',
			[],
			Html::label( $this->getMsg( 'search' )->escaped(), 'searchInput' )
		);
		$html .= $this->makeSearchInput( [ 'id' => 'searchInput' ] );
		$html .= $this->makeSearchButton( 'go', [ 'id' => 'searchGoButton', 'class' => 'searchButton' ] );
		$html .= Html::closeElement( 'form' );

		return $html;
	}

	/**
	 * Generates the sidebar
	 * Set the elements to true to allow them to be part of the sidebar
	 * Or get rid of this entirely, and take the specific bits to use wherever you actually want them
	 *  * Toolbox is the page/site tools that appears under the sidebar in vector
	 *  * Languages is the interlanguage links on the page via en:... es:... etc
	 *  * Default is each user-specified box as defined on MediaWiki:Sidebar; you will still need a foreach loop
	 *    to parse these.
	 * @return string html
	 */
	protected function getSiteNavigation() {
		$html = '';

		$sidebar = $this->getSidebar();
		$sidebar['SEARCH'] = false;
		$sidebar['TOOLBOX'] = true;
		$sidebar['LANGUAGES'] = true;

		foreach ( $sidebar as $name => $content ) {
			if ( $content === false ) {
				continue;
			}
			// Numeric strings gets an integer when set as key, cast back - T73639
			$name = (string)$name;

			switch ( $name ) {
				case 'SEARCH':
					$html .= $this->getSearch();
					break;
				case 'TOOLBOX':
					$html .= $this->getPortlet( 'tb', $this->getToolbox(), 'toolbox' );
					break;
				case 'LANGUAGES':
					if ( $this->data['language_urls'] !== false ) {
						$html .= $this->getPortlet( 'lang', $this->data['language_urls'], 'otherlanguages' );
					}
					break;
				default:
					$html .= $this->getPortlet( $name, $content['content'] );
					break;
			}
		}
		return $html;
	}

	/**
	 * Generates page-related tools/links
	 * You will probably want to split this up and move all of these to somewhere that makes sense for your skin.
	 * @return string html
	 */
	protected function getPageLinks() {
		// Namespaces: links for 'content' and 'talk' for namespaces with talkpages. Otherwise is just the content.
		// Usually rendered as tabs on the top of the page.
		$html = $this->getPortlet(
			'namespaces',
			$this->data['content_navigation']['namespaces']
		);
		// Variants: Language variants. Displays list for converting between different scripts in the same language,
		// if using a language where this is applicable.
		$html .= $this->getPortlet(
			'variants',
			$this->data['content_navigation']['variants']
		);
		// 'View' actions for the page: view, edit, view history, etc
		$html .= $this->getPortlet(
			'views',
			$this->data['content_navigation']['views']
		);
		// Other actions for the page: move, delete, protect, everything else
		$html .= $this->getPortlet(
			'actions',
			$this->data['content_navigation']['actions']
		);

		return $html;
	}

	/**
	 * Generates user tools menu
	 * @return string html
	 */
	protected function getUserLinks() {
		return $this->getPortlet(
			'personal',
			$this->getPersonalTools(),
			'personaltools'
		);
	}

	/**
	 * Generates siteNotice, if any
	 * @return string html
	 */
	protected function getSiteNotice() {
		if ( $this->data['sitenotice'] ) {
			return Html::rawElement(
				'div',
				[ 'id' => 'siteNotice' ],
				$this->get( 'sitenotice' )
			);
		}
		return '';
	}

	/**
	 * Generates new talk message banner, if any
	 * @return string html
	 */
	protected function getNewTalk() {
		if ( $this->data['newtalk'] ) {
			return Html::rawElement(
				'div',
				[ 'class' => 'usermessage' ],
				$this->get( 'newtalk' )
			);
		}
		return '';
	}

	/**
	 * Generates subtitle stuff, if any
	 * @return string html
	 */
	protected function getPageSubtitle() {
		if ( $this->data['subtitle'] ) {
			return Html::rawElement(
				'p',
				[],
				$this->get( 'subtitle' )
			);
		}
		return '';
	}

	/**
	 * Generates category links, if any
	 * @return string html
	 */
	protected function getCategoryLinks() {
		if ( $this->data['catlinks'] ) {
			return $this->get( 'catlinks' );
		}
		return '';
	}

	/**
	 * Generates data after content stuff, if any
	 * @return string html
	 */
	protected function getDataAfterContent() {
		if ( $this->data['dataAfterContent'] ) {
			return $this->get( 'dataAfterContent' );
		}
		return '';
	}

	/**
	 * Generates a block of navigation links with a header
	 *
	 * @param string $name
	 * @param array|string $content array of links for use with makeListItem,
	 * or a block of text
	 * @param null|string|array|bool $msg
	 *
	 * @return string html
	 */
	protected function getPortlet( $name, $content, $msg = null ) {
		if ( $msg === null ) {
			$msg = $name;
		} elseif ( is_array( $msg ) ) {
			$msgString = array_shift( $msg );
			$msgParams = $msg;
			$msg = $msgString;
		}
		$msgObj = wfMessage( $msg );
		if ( $msgObj->exists() ) {
			if ( isset( $msgParams ) && !empty( $msgParams ) ) {
				$msgString = $this->getMsg( $msg, $msgParams )->parse();
			} else {
				$msgString = $msgObj->parse();
			}
		} else {
			$msgString = htmlspecialchars( $msg );
		}

		// HACK: Compatibility with extensions still using SkinTemplateToolboxEnd
		$hookContents = '';
		if ( $name == 'tb' ) {
			if ( isset( $boxes['TOOLBOX'] ) ) {
				ob_start();
				// We pass an extra 'true' at the end so extensions using BaseTemplateToolbox
				// can abort and avoid outputting double toolbox links
				// Avoid PHP 7.1 warning from passing $this by reference
				$template = $this;
				Hooks::run( 'SkinTemplateToolboxEnd', [ &$template, true ] );
				$hookContents = ob_get_contents();
				ob_end_clean();
				if ( !trim( $hookContents ) ) {
					$hookContents = '';
				}
			}
		}
		// END hack

		$labelId = Sanitizer::escapeId( "p-$name-label" );

		if ( is_array( $content ) ) {
			$contentText = Html::openElement( 'ul' );
			foreach ( $content as $key => $item ) {
				$contentText .= $this->makeListItem(
					$key,
					$item,
					[ 'text-wrapper' => [ 'tag' => 'span' ] ]
				);
			}
			// Add in SkinTemplateToolboxEnd, if any
			$contentText .= $hookContents;
			$contentText .= Html::closeElement( 'ul' );
		} else {
			$contentText = $content;
		}

		$html = Html::rawElement( 'div', [
				'role' => 'navigation',
				'class' => 'mw-portlet',
				'id' => Sanitizer::escapeId( 'p-' . $name ),
				'title' => Linker::titleAttrib( 'p-' . $name ),
				'aria-labelledby' => $labelId
			],
			Html::rawElement( 'h3', [
					'id' => $labelId,
					'lang' => $this->get( 'userlang' ),
					'dir' => $this->get( 'dir' )
				],
				$msgString
			) .
			Html::rawElement( 'div', [ 'class' => 'mw-portlet-body' ],
				$contentText .
				$this->getAfterPortlet( $name )
			)
		);

		return $html;
	}

	/* DEPRECATED FUNCTIONS: remove if you're not intending to support versions of mw under their requirements */

	/**
	 * Get a div with the core visualClear class, for clearing floats
	 *
	 * @return string html
	 * @since 1.29
	 */
	protected function getClear() {
		return Html::element( 'div', [ 'class' => 'visualClear' ] );
	}

	/**
	 * Renderer for getFooterIcons and getFooterLinks
	 *
	 * @param string $iconStyle $option for getFooterIcons: "icononly", "nocopyright"
	 * @param string $linkStyle $option for getFooterLinks: "flat"
	 *
	 * @return string html
	 * @since 1.29
	 */
	protected function getFooter( $iconStyle = 'icononly', $linkStyle = 'flat' ) {
		$validFooterIcons = $this->getFooterIcons( $iconStyle );
		$validFooterLinks = $this->getFooterLinks( $linkStyle );

		$html = '';

		if ( count( $validFooterIcons ) + count( $validFooterLinks ) > 0 ) {
			$html .= Html::openElement( 'div', [
				'id' => 'footer-bottom',
				'role' => 'contentinfo',
				'lang' => $this->get( 'userlang' ),
				'dir' => $this->get( 'dir' )
			] );
			$footerEnd = Html::closeElement( 'div' );
		} else {
			$footerEnd = '';
		}
		foreach ( $validFooterIcons as $blockName => $footerIcons ) {
			$html .= Html::openElement( 'div', [
				'id' => 'f-' . Sanitizer::escapeId( $blockName ) . 'ico',
				'class' => 'footer-icons'
			] );
			foreach ( $footerIcons as $icon ) {
				$html .= $this->getSkin()->makeFooterIcon( $icon );
			}
			$html .= Html::closeElement( 'div' );
		}
		if ( count( $validFooterLinks ) > 0 ) {
			$html .= Html::openElement( 'ul', [ 'id' => 'f-list', 'class' => 'footer-places' ] );
			foreach ( $validFooterLinks as $aLink ) {
				$html .= Html::rawElement(
					'li',
					[ 'id' => Sanitizer::escapeId( $aLink ) ],
					$this->get( $aLink )
				);
			}
			$html .= Html::closeElement( 'ul' );
		}

		$html .= $this->getClear() . $footerEnd;

		return $html;
	}

	/**
	 * Allows extensions to hook into known portlets and add stuff to them
	 *
	 * @param string $name
	 *
	 * @return string html
	 * @since 1.29
	 */
	protected function getAfterPortlet( $name ) {
		$html = '';
		$content = '';
		Hooks::run( 'BaseTemplateAfterPortlet', [ $this, $name, &$content ] );

		if ( $content !== '' ) {
			$html = Html::rawElement(
				'div',
				[ 'class' => [ 'after-portlet', 'after-portlet-' . $name ] ],
				$content
			);
		}

		return $html;
	}

	/**
	 * Get the basic end-page trail including bottomscripts, reporttime, and
	 * debug stuff. This should be called right before outputting the closing
	 * body and html tags.
	 *
	 * @return string
	 * @since 1.29
	 */
	function getTrail() {
		$html = MWDebug::getDebugHTML( $this->getSkin()->getContext() );
		$html .= $this->get( 'bottomscripts' );
		$html .= $this->get( 'reporttime' );

		return $html;
	}
}
