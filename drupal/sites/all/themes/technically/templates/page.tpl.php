<?php
$wide_nodes=array(6, 36);
if(in_array($node->nid, $wide_nodes)):
	include 'page--wide-header.tpl.php';
else:

/**
 * @file
 * Default theme implementation to display a single Drupal page.
 *
 * Available variables:
 *
 * General utility variables:
 * - $base_path: The base URL path of the Drupal installation. At the very
 *   least, this will always default to /.
 * - $directory: The directory the template is located in, e.g. modules/system
 *   or themes/bartik.
 * - $is_front: TRUE if the current page is the front page.
 * - $logged_in: TRUE if the user is registered and signed in.
 * - $is_admin: TRUE if the user has permission to access administration pages.
 *
 * Site identity:
 * - $front_page: The URL of the front page. Use this instead of $base_path,
 *   when linking to the front page. This includes the language domain or
 *   prefix.
 * - $logo: The path to the logo image, as defined in theme configuration.
 * - $site_name: The name of the site, empty when display has been disabled
 *   in theme settings.
 * - $site_slogan: The slogan of the site, empty when display has been disabled
 *   in theme settings.
 *
 * Navigation:
 * - $main_menu (array): An array containing the Main menu links for the
 *   site, if they have been configured.
 * - $secondary_menu (array): An array containing the Secondary menu links for
 *   the site, if they have been configured.
 * - $breadcrumb: The breadcrumb trail for the current page.
 *
 * Page content (in order of occurrence in the default page.tpl.php):
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title: The page title, for use in the actual HTML content.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 * - $messages: HTML for status and error messages. Should be displayed
 *   prominently.
 * - $tabs (array): Tabs linking to any sub-pages beneath the current page
 *   (e.g., the view and edit tabs when displaying a node).
 * - $action_links (array): Actions local to the page, such as 'Add menu' on the
 *   menu administration interface.
 * - $feed_icons: A string of all feed icons for the current page.
 * - $node: The node object, if there is an automatically-loaded node
 *   associated with the page, and the node ID is the second argument
 *   in the page's path (e.g. node/12345 and node/12345/revisions, but not
 *   comment/reply/12345).
 *
 * Regions:
 * - $page['help']: Dynamic help text, mostly for admin pages.
 * - $page['highlighted']: Items for the highlighted content region.
 * - $page['content']: The main content of the current page.
 * - $page['sidebar_first']: Items for the first sidebar.
 * - $page['sidebar_second']: Items for the second sidebar.
 * - $page['header']: Items for the header region.
 * - $page['footer']: Items for the footer region.
 *
 * @see template_preprocess()
 * @see template_preprocess_page()
 * @see template_process()
 */
?>
<div id="page-wrapper">
	<div id="page">
		<div id="header">
			<div class="section clearfix">
				<div id="header-left">
					<?php if ($logo): ?>
						<a href="<?php print $front_page; ?>" title="<?php print t('TechHub'); ?>" rel="home" id="logo">
							<img src="<?php print $logo; ?>" alt="<?php print t('TechHub'); ?>" />
						</a>
					<?php endif; ?>
				</div>
				<div id="header-middle">
					<?php if ($main_menu || $secondary_menu): ?>
						<div id="navigation">
							<?php print theme('links__system_main_menu', array('links' => $main_menu, 'attributes' => array('id' => 'main-menu', 'class' => array('links', 'inline', 'clearfix')))); ?>
							<?php print theme('links__system_secondary_menu', array('links' => $secondary_menu, 'attributes' => array('id' => 'secondary-menu', 'class' => array('links', 'inline', 'clearfix')))); ?>
						</div> <!-- end of nav -->
					<?php endif; ?>
				</div>

				<?php print render($page['header']); ?>
			</div>
		</div><!-- end of header -->
		
		<?php if ($messages): ?>
			<div class="nine-fourty">
				<?php print $messages; ?>
			</div>
		<?php endif; ?>
		
		<div id="main-wrapper">
			<div id="main" class="clearfix">
				<?php if ($page['sidebar_first']): ?>
					<div id="sidebar-first" class="column sidebar">
						<div class="section">
							<?php print render($page['sidebar_first']); ?>
						</div>
					</div> <!-- end of left col -->
				<?php endif; ?>
		
				<div id="content" class="column">
					<div class="section">	
						<div id="title-area">	
							<?php
						
							//printing icons
						
							//overrides for specific paths should be put in this array
							$specific_path_overrides=array(
								'events' => 'event',
								'memberships' => 'memberships',
								'messages' => 'memberships',
								'blog' => 'blog',
							);
						
							if (isset($node_content) && $node_content['field_icon']) {

							    print render($node_content['field_icon']);

							} elseif ((arg(0)=='user' && arg(2)==FALSE)) {

							    $icon['path']=drupal_get_path('theme', 'technically').'/images/icon-profile-grey.png';
								print theme_image($icon);

							} elseif ($node->type=='blog' || $node->type=='event') {

							    $icon['path']=drupal_get_path('theme', 'technically')."/images/icon-{$node->type}-grey.png";
								print theme_image($icon);

							} elseif (in_array(arg(0), array_keys($specific_path_overrides))) {
								$image=$specific_path_overrides[arg(0)];
							    $icon['path']=drupal_get_path('theme', 'technically')."/images/icon-{$image}-grey.png";
								print theme_image($icon);
						
							} else {

								$icon['path']=drupal_get_path('theme', 'technically').'/images/icon-about-grey.png';
								print theme_image($icon);
						    
							}
							?>
					
							<?php 
							if($node->type=='blog' || $node->type=='event' || (arg(0)=='user' && arg(2)==FALSE)) : ?>
					
							<?php $class=(isset($node->type))?$node->type:'profile'; ?>
								<span class="<?php print $class; ?>-title"><?php print $class; ?></span>	
						</div>
						
						<?php else : ?>
							<?php print render($title_prefix); ?>
							<?php if ($title): ?><h1 class="title" id="page-title"><?php print $title; ?></h1><?php endif; ?>
							<?php print render($title_suffix); ?>

						<?php endif; ?>

						<?php if ($tabs): ?><div class="tabs"><?php print render($tabs); ?></div><?php endif; ?>

						<?php print render($page['help']); ?>

						<?php if ($action_links): ?><ul class="action-links"><?php print render($action_links); ?></ul><?php endif; ?>

						<?php print render($page['content']); ?> 

						<?php if ($page['content_left']): ?>
							<div id="content-left" class="column content-block">
								<?php print render($page['content_left']); ?>
							</div>
						<?php endif; ?>
			
						<?php if ($page['content_right']): ?>
							<div id="content-right" class="column content-block">
								<?php print render($page['content_right']); ?>
							</div>
						<?php endif; ?>
	
						
						<?php if ($page['content_below']): ?>
							<div id="content-below" class="column content-block">
								<?php print render($page['content_below']); ?>
							</div>
						<?php endif; ?>
					</div>
				</div> <!-- end of content -->

				<?php if ($page['sidebar_second']): ?>
					<div id="sidebar-second" class="column sidebar">
						<div class="section">
							<?php print render($page['sidebar_second']); ?>
						</div>
					</div> <!-- end of right col -->
				<?php endif; ?>
			</div> <!-- end of main -->
		</div> <!-- end of main wrapper -->

		<div id="footer">
			<div class="section">
				<a id="partners-link" href="/partners"></a>
				<?php print render($page['footer']); ?>
				<p class="copyright">&copy; TechHub <?php echo date("Y"); ?></p>
				<p class="credit">Website by <a href="http://thirdsectordesign.org" target="_blank">Third Sector Design</a></p>
			</div>
		</div> <!-- end of footer -->

	</div> <!-- end of page -->
</div> <!-- end of page wrapper-->
<?php endif;?>
