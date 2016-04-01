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
	 * Outputs a single sidebar portlet of any kind.
	 */
	private function outputPortlet( $box ) {
		if ( !$box['content'] ) {
			return;
		}

		?>
		<div
			role="navigation"
			class="mw-portlet"
			id="<?php echo Sanitizer::escapeId( $box['id'] ) ?>"
			<?php echo Linker::tooltip( $box['id'] ) ?>
		>
			<h3>
				<?php
				if ( isset( $box['headerMessage'] ) ) {
					echo $this->getMsg( $box['headerMessage'] )->escaped();
				} else {
					echo htmlspecialchars( $box['header'], ENT_QUOTES );
				}
				?>
			</h3>

			<?php
			if ( is_array( $box['content'] ) ) {
				echo '<ul>';
				foreach ( $box['content'] as $key => $item ) {
					echo $this->makeListItem( $key, $item );
				}
				echo '</ul>';
			} else {
				echo $box['content'];
			}?>
		</div>
		<?php
	}

	/**
	 * Outputs the logo and (optionally) site title
	 */
	private function outputLogo( $id = 'p-logo', $imageOnly = false ) {
		?>
		<div id="<?php echo $id ?>" class="mw-portlet" role="banner">
			<?php
			echo Html::element(
				'a',
				array(
					'href' => $this->data['nav_urls']['mainpage']['href'],
					'class' => 'mw-wiki-logo',
				) + Linker::tooltipAndAccesskeyAttribs( 'p-logo' )
			);
			if ( !$imageOnly ) {
				?>
				<a id="p-banner" class="mw-wiki-title" href="<?php echo htmlspecialchars( $this->data['nav_urls']['mainpage']['href'], ENT_QUOTES ) ?>">
					<?php echo $this->getMsg( 'sitetitle' )->escaped() ?>
				</a>
				<?php
			}
			?>
		</div>
		<?php
	}

	/**
	 * Outputs the search form
	 */
	private function outputSearch() {
		?>
		<form
			action="<?php $this->text( 'wgScript' ) ?>"
			role="search"
			class="mw-portlet"
			id="p-search"
		>
			<input type="hidden" name="title" value="<?php $this->text( 'searchtitle' ) ?>" />
			<h3>
				<label for="searchInput"><?php echo $this->getMsg( 'search' )->escaped() ?></label>
			</h3>
			<?php echo $this->makeSearchInput( array( 'id' => 'searchInput' ) ) ?>
			<?php echo $this->makeSearchButton( 'go', array( 'id' => 'searchGoButton', 'class' => 'searchButton' ) ) ?>
			<input type="hidden" name="title" value="<?php $this->text( 'searchtitle' ) ?>"/>
		</form>
		<?php
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
			$this->outputPortlet( $box, true );
		}
	}

	/**
	 * Outputs page-related tools/links
	 */
	private function outputPageLinks() {
		$this->outputPortlet( array(
			'id' => 'p-namespaces',
			'headerMessage' => 'namespaces',
			'content' => $this->data['content_navigation']['namespaces'],
		) );
		$this->outputPortlet( array(
			'id' => 'p-variants',
			'headerMessage' => 'variants',
			'content' => $this->data['content_navigation']['variants'],
		) );
		$this->outputPortlet( array(
			'id' => 'p-views',
			'headerMessage' => 'views',
			'content' => $this->data['content_navigation']['views'],
		) );
		$this->outputPortlet( array(
			'id' => 'p-actions',
			'headerMessage' => 'actions',
			'content' => $this->data['content_navigation']['actions'],
		) );
	}

	/**
	 * Outputs user tools menu
	 */
	private function outputUserLinks() {
		$this->outputPortlet( array(
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
