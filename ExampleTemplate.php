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
					?>
					<div id="siteNotice"><?php $this->html( 'sitenotice' ) ?></div>
					<?php
				}
				if ( $this->data['newtalk'] ) {
					?>
					<div class="usermessage"><?php $this->html( 'newtalk' ) ?></div>
					<?php
				}
				echo $this->getIndicators();
				?>

				<h1 class="firstHeading" lang="<?php $this->text( 'pageLanguage' ); ?>">
					<?php $this->html( 'title' ) ?>
				</h1>
				<div id="siteSub"><?php echo $this->getMsg( 'tagline' )->parse() ?></div>
				<div class="mw-body-content">
					<div id="contentSub">
						<?php
						if ( $this->data['subtitle'] ) {
							?>
							<p><?php $this->html( 'subtitle' ) ?></p>
							<?php
						}
						if ( $this->data['undelete'] ) {
							?>
							<p><?php $this->html( 'undelete' ) ?></p>
							<?php
						}
						?>
					</div>

					<?php
					$this->html( 'bodycontent' );
					$this->clear();
					?>
					<div class="printfooter">
						<?php $this->html( 'printfooter' ); ?>
					</div>
					<?php
					$this->html( 'catlinks' );
					$this->html( 'dataAfterContent' );
					?>
				</div>
			</div>

			<div id="mw-navigation">
				<h2><?php echo $this->getMsg( 'navigation-heading' )->parse() ?></h2>
				<?php
				$this->outputLogo();
				$this->outputSearch();
				echo '<div id="user-tools">';
					$this->outputUserLinks();
				echo '</div><div id="page-tools">';
					$this->outputPageLinks();
				echo '</div><div id="site-navigation">';
					$this->outputSiteNavigation();
				echo '</div>';
				?>
			</div>

			<div id="mw-footer">
				<ul id="footer-icons" role="contentinfo">
					<?php
					foreach ( $this->getFooterIcons( 'icononly' ) as $blockName => $footerIcons ) {
						?>
						<li id="footer-<?php echo htmlspecialchars( $blockName, ENT_QUOTES ) ?>ico">
							<?php
							foreach ( $footerIcons as $icon ) {
								echo $this->getSkin()->makeFooterIcon( $icon );
							}
							?>
						</li>
						<?php
					}
					?>
				</ul>
				<?php
				foreach ( $this->getFooterLinks() as $category => $links ) {
					?>
					<ul id="footer-<?php echo htmlspecialchars( $category, ENT_QUOTES ) ?>" role="contentinfo">
						<?php
						foreach ( $links as $key ) {
							?>
							<li id="footer-<?php echo htmlspecialchars( $category, ENT_QUOTES ) ?>-<?php echo htmlspecialchars( $key, ENT_QUOTES ) ?>"><?php $this->html( $key ) ?></li>
						<?php
						}
						?>
					</ul>
					<?php
				}
				$this->clear();
				?>
			</div>
		</div>

		<?php $this->printTrail() ?>
		</body></html>

		<?php
	}

	/**
	 * Creates a single sidebar portlet of any kind
	 * @return string
	 */
	private function assemblePortlet( $box ) {
		if ( !$box['content'] ) {
			return;
		}

		$content = Html::openElement(
			'div',
			array(
				'role' => 'navigation',
				'class' => 'mw-portlet',
				'id' => Sanitizer::escapeId( $box['id'] )
			) + Linker::tooltipAndAccesskeyAttribs( $box['id'] )
		);
		$content .= Html::element(
			'h3',
			[],
			isset( $box['headerMessage'] ) ? $this->getMsg( $box['headerMessage'] )->text() : $box['header'] );
		if ( is_array( $box['content'] ) ) {
			$content .= Html::openElement( 'ul' );
			foreach ( $box['content'] as $key => $item ) {
				$content .= $this->makeListItem( $key, $item );
			}
			$content .= Html::closeElement( 'ul' );
		} else {
			$content .= $box['content'];
		}
		$content .= Html::closeElement( 'div' );

		return $content;
	}

	/**
	 * Outputs the logo and (optionally) site title
	 */
	private function outputLogo( $id = 'p-logo', $imageOnly = false ) {
		echo Html::openElement(
			'div',
			array(
				'id' => $id,
				'class' => 'mw-portlet',
				'role' => 'banner'
			)
		);
		echo Html::element(
			'a',
			array(
				'href' => $this->data['nav_urls']['mainpage']['href'],
				'class' => 'mw-wiki-logo',
			) + Linker::tooltipAndAccesskeyAttribs( 'p-logo' )
		);
		if ( !$imageOnly ) {
			echo Html::element(
				'a',
				array(
					'id' => 'p-banner',
					'class' => 'mw-wiki-title',
					'href'=> $this->data['nav_urls']['mainpage']['href']
				) + Linker::tooltipAndAccesskeyAttribs( 'p-logo' ),
				$this->getMsg( 'sitetitle' )->escaped()
			);
		}
		echo Html::closeElement( 'div' );
	}

	/**
	 * Outputs the search form
	 */
	private function outputSearch() {
		echo Html::openElement(
			'form',
			array(
				'action' => htmlspecialchars( $this->get( 'wgScript' ) ),
				'role' => 'search',
				'class' => 'mw-portlet',
				'id' => 'p-search'
			)
		);
		echo Html::hidden( 'title', htmlspecialchars( $this->get( 'searchtitle' ) ) );
		echo Html::rawelement(
			'h3',
			[],
			Html::label( $this->getMsg( 'search' )->escaped(), 'searchInput' )
		);
		echo $this->makeSearchInput( array( 'id' => 'searchInput' ) );
		echo $this->makeSearchButton( 'go', array( 'id' => 'searchGoButton', 'class' => 'searchButton' ) );
		echo Html::closeElement( 'form' );
	}

	/**
	 * Outputs the sidebar
	 * Set the elements to true to allow them to be part of the sidebar
	 */
	private function outputSiteNavigation() {
		$sidebar = $this->getSidebar();

		$sidebar['SEARCH'] = false;
		$sidebar['TOOLBOX'] = true;
		$sidebar['LANGUAGES'] = true;

		foreach ( $sidebar as $boxName => $box ) {
			if ( $boxName === false ) {
				continue;
			}
			echo $this->assemblePortlet( $box, true );
		}
	}

	/**
	 * Outputs page-related tools/links
	 */
	private function outputPageLinks() {
		$links = $this->assemblePortlet( array(
			'id' => 'p-namespaces',
			'headerMessage' => 'namespaces',
			'content' => $this->data['content_navigation']['namespaces'],
		) );
		$links .= $this->assemblePortlet( array(
			'id' => 'p-variants',
			'headerMessage' => 'variants',
			'content' => $this->data['content_navigation']['variants'],
		) );
		$links .= $this->assemblePortlet( array(
			'id' => 'p-views',
			'headerMessage' => 'views',
			'content' => $this->data['content_navigation']['views'],
		) );
		$links .= $this->assemblePortlet( array(
			'id' => 'p-actions',
			'headerMessage' => 'actions',
			'content' => $this->data['content_navigation']['actions'],
		) );
		echo $links;
	}

	/**
	 * Outputs user tools menu
	 */
	private function outputUserLinks() {
		echo $this->assemblePortlet( array(
			'id' => 'p-personal',
			'headerMessage' => 'personaltools',
			'content' => $this->getPersonalTools(),
		) );
	}

	/**
	 * Outputs a css clear using the core visualClear class
	 */
	private function clear() {
		echo '<div class="visualClear"></div>';
	}
}
