<?php
/**
 * Add body classes if certain regions have content.
 */
function technically_preprocess_html(&$variables) {
	drupal_add_css(path_to_theme() . '/css/ie.css', array('group' => CSS_THEME, 'browsers' => array('IE' => 'lte IE 7', '!IE' => FALSE), 'preprocess' => FALSE));
	drupal_add_css(path_to_theme() . '/css/ie6.css', array('group' => CSS_THEME, 'browsers' => array('IE' => 'IE 6', '!IE' => FALSE), 'preprocess' => FALSE));
	$variables['classes_array'][]='th';
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
 * Implements theme_menu_tree().
 */
function technically_menu_tree($variables) {
		return '<ul class="menu clearfix">' . $variables['tree'] . '</ul>';
}

/**
 * Implements theme_field__field_type().
 */

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

/** 
 * Used to change the term id's on the resources block to term names instead
 */
function technically_preprocess_views_view_summary(&$vars) {
	//  print_r($vars['view']->name);                                                                                                                                                                                                         
	if($vars['view']->name == 'blog_tags' && $vars['view']->current_display == 'block') {
		$items = array();
		foreach($vars['rows'] as $result){
			if(is_numeric($result->link)) {
				$term_object = taxonomy_term_load($result->link);
				$result->link = $term_object->name;
				$result->url = strtolower($result->url);
				$items[] = $result;
			}
		}
		$vars['rows'] = $items;
	}
}

