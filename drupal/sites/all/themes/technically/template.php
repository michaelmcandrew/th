<?php

/**
 * Add body classes if certain regions have content.
 */
function technically_preprocess_html(&$variables) {
  drupal_add_css(path_to_theme() . '/css/ie.css', array('group' => CSS_THEME, 'browsers' => array('IE' => 'lte IE 7', '!IE' => FALSE), 'preprocess' => FALSE));
  drupal_add_css(path_to_theme() . '/css/ie6.css', array('group' => CSS_THEME, 'browsers' => array('IE' => 'IE 6', '!IE' => FALSE), 'preprocess' => FALSE));
}

function technically_preprocess_page(&$variables) {
        if (arg(0) == 'node') {
                $variables['node_content'] =& $variables['page']['content']['system_main']['nodes'][arg(1)];
        }
}
/**
 * Override or insert variables into the node template.
 */
function technically_preprocess_node(&$variables) {
  if ($variables['view_mode'] == 'full' && node_is_page($variables['node'])) {
    $variables['classes_array'][] = 'node-full';
  }
}
/**
 * Override or insert variables into the block template.
 */
// function technically_preprocess_block(&$variables) {
//   // In the header region visually hide block titles.
//   if ($variables['block']->region == 'header') {
//     $variables['title_attributes_array']['class'][] = 'element-invisible';
//   }
// }

/**
 * Implements theme_menu_tree().
 */
function technically_menu_tree($variables) {
  return '<ul class="menu clearfix">' . $variables['tree'] . '</ul>';
}

/**
 * Implements theme_field__field_type().
 */

function technically_theme(&$existing, $type, $theme, $path) {
   $hooks['user_login_block'] = array(
     'template' => 'templates/user-login-block',
     'render element' => 'form',
   );
   return $hooks;
 }
function technically_preprocess_user_login_block(&$vars) {
	unset($vars['form']['name']['#title']);
	unset($vars['form']['pass']['#title']);
	$vars['form']['name']['#attributes'] = array('placeholder' => 'username');
	$vars['form']['pass']['#attributes'] = array('placeholder' => 'password');
	$vars['rendered'] = drupal_render_children($vars['form']);
}

function technically_field__field_message_categories($variables) {
	return technically_correct_message_links($variables, 'messages/%/all');
}
function technically_field__field_message_tags($variables) {
	return technically_correct_message_links($variables, 'messages/all/%');
}

function technically_correct_message_links($variables, $link){
	$output = '';
	// Render the label, if it's not hidden.
	if (!$variables['label_hidden']) {
		$output .= '<p class="field-label">' . $variables['label'] . ': </p>';
	}

	// Render the items.
	$output .= ($variables['element']['#label_display'] == 'inline') ? '<ul class="links inline">' : '<ul class="links">';
	foreach ($variables['items'] as $delta => $item) {
		$term=strtolower($item['#title']);		
		$output .= '<li class="taxonomy-term-reference-' . $delta . '"' . $variables['item_attributes'][$delta] . '>' . l($term, str_replace('%', $term, $link)) . '</li>';
	}
	$output .= '</ul>';

	// Render the top-level DIV.
	$output = '<div class="' . $variables['classes'] . (!in_array('clearfix', $variables['classes_array']) ? ' clearfix' : '') . '">' . $output . '</div>';

	return $output;
}

// function technically_process_field(&$variables, $hook){
// 	if($variables['element']['#field_name'] == 'field_message_tags') {
// 		print_r(($variables['items']));exit;
// 	}	
// }
