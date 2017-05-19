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
		$this->html( 'headelement' );
		?>
		<div id="mw-wrapper">
			<div class="mw-body" role="main">
				<?php
				if ( $this->data['sitenotice'] ) {
					echo Html::rawElement(
						'div',
						array( 'id' => 'siteNotice' ),
						$this->get( 'sitenotice' )
					);
				}
				if ( $this->data['newtalk'] ) {
					echo Html::rawElement(
						'div',
						array( 'class' => 'usermessage' ),
						$this->get( 'newtalk' )
					);
				}
				echo $this->getIndicators();
				echo Html::rawElement(
					'h1',
					array(
						'class' => 'firstHeading',
						'lang' => $this->get( 'pageLanguage' )
					),
					$this->get( 'title' )
				);

				echo Html::rawElement(
					'div',
					array( 'id' => 'siteSub' ),
					$this->getMsg( 'tagline' )->parse()
				);
				?>

				<div class="mw-body-content">
					<?php
					echo Html::openElement(
						'div',
						array( 'id' => 'contentSub' )
					);
					if ( $this->data['subtitle'] ) {
						echo Html::rawelement (
							'p',
							[],
							$this->get( 'subtitle' )
						);
					}
					echo Html::rawelement (
						'p',
						[],
						$this->get( 'undelete' )
					);
					echo Html::closeElement( 'div' );

					$this->html( 'bodycontent' );
					echo $this->clear();
					echo Html::rawElement(
						'div',
						array( 'class' => 'printfooter' ),
						$this->get( 'printfooter' )
					);
					$this->html( 'catlinks' );
					$this->html( 'dataAfterContent' );
					?>
				</div>
			</div>

			<div id="mw-navigation">
				<?php
				echo Html::rawElement(
					'h2',
					[],
					$this->getMsg( 'navigation-heading' )->parse()
				);

				echo $this->getLogo();
				echo $this->getSearch();

				// User profile links
				echo Html::rawElement(
					'div',
					array( 'id' => 'user-tools' ),
					$this->getUserLinks()
				);

				// Page editing and tools
				echo Html::rawElement(
					'div',
					array( 'id' => 'page-tools' ),
					$this->getPageLinks()
				);

				// Site navigation/sidebar
				echo Html::rawElement(
					'div',
					array( 'id' => 'site-navigation' ),
					$this->getSiteNavigation()
				);
				?>
			</div>

			<div id="mw-footer">
				<?php
				echo Html::openElement(
					'ul',
					array(
						'id' => 'footer-icons',
						'role' => 'contentinfo'
					)
				);
				foreach ( $this->getFooterIcons( 'icononly' ) as $blockName => $footerIcons ) {
					echo Html::openElement(
						'li',
						array(
							'id' => 'footer-' . Sanitizer::escapeId( $blockName ) . 'ico'
						)
					);
					foreach ( $footerIcons as $icon ) {
						echo $this->getSkin()->makeFooterIcon( $icon );
					}
					echo Html::closeElement( 'li' );
				}
				echo Html::closeElement( 'ul' );

				foreach ( $this->getFooterLinks() as $category => $links ) {
					echo Html::openElement(
						'ul',
						array(
							'id' => 'footer-' . Sanitizer::escapeId( $category ),
							'role' => 'contentinfo'
						)
					);
					foreach ( $links as $key ) {
						echo Html::rawElement(
							'li',
							array(
								'id' => 'footer-' . Sanitizer::escapeId( $category . '-' . $key )
							),
							$this->get( $key )
						);
					}
					echo Html::closeElement( 'ul' );
				}
				echo $this->clear();
				?>
			</div>
		</div>

		<?php $this->printTrail() ?>
		</body>
		</html>

		<?php
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
				$this->renderAfterPortlet( $name )
			)
		);

		return $html;
	}

	/**
	 * Generates the logo and (optionally) site title
	 * @return string html
	 */
	protected function getLogo( $id = 'p-logo', $imageOnly = false ) {
		$html = Html::openElement(
			'div',
			array(
				'id' => $id,
				'class' => 'mw-portlet',
				'role' => 'banner'
			)
		);
		$html .= Html::element(
			'a',
			array(
				'href' => $this->data['nav_urls']['mainpage']['href'],
				'class' => 'mw-wiki-logo',
			) + Linker::tooltipAndAccesskeyAttribs( 'p-logo' )
		);
		if ( !$imageOnly ) {
			$html .= Html::element(
				'a',
				array(
					'id' => 'p-banner',
					'class' => 'mw-wiki-title',
					'href'=> $this->data['nav_urls']['mainpage']['href']
				) + Linker::tooltipAndAccesskeyAttribs( 'p-logo' ),
				$this->getMsg( 'sitetitle' )->escaped()
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
			array(
				'action' => htmlspecialchars( $this->get( 'wgScript' ) ),
				'role' => 'search',
				'class' => 'mw-portlet',
				'id' => 'p-search'
			)
		);
		$html .= Html::hidden( 'title', htmlspecialchars( $this->get( 'searchtitle' ) ) );
		$html .= Html::rawelement(
			'h3',
			[],
			Html::label( $this->getMsg( 'search' )->escaped(), 'searchInput' )
		);
		$html .= $this->makeSearchInput( array( 'id' => 'searchInput' ) );
		$html .= $this->makeSearchButton( 'go', array( 'id' => 'searchGoButton', 'class' => 'searchButton' ) );
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
					$html .= $this->getPortlet( 'tb', $this->getToolbox(), 'toolbox', 'SkinTemplateToolboxEnd' );
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
	 * Outputs a css clear using the core visualClear class
	 * @return string html
	 */
	protected function clear() {
		return Html::element( 'div', [ 'class' => 'visualClear' ] );
	}
}
