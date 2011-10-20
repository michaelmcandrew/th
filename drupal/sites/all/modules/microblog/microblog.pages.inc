<?php
/**
 * @file
 * Page callback file for the microblog module.
 */

/**
 * Menu callback; displays a given users microblog entries
 * If the user viewing the entries is not the author, enable following
 * If the user viewing the entries *is* the author, show subscriptions (those followed)
 */
function microblog_page_user($account) {
  global $user;
  
  $output = '';
  
  drupal_add_js(drupal_get_path('module', 'microblog') . '/microblog.js');
  drupal_set_title($title = t("@name's microblog", array('@name' => $account->name)));
  $items = array();
  if (($account->uid == $user->uid) && user_access('post microblog entries')) {
    module_load_include('inc', 'node', 'node.pages');
    $node = new stdClass(); 
    $node->type = 'microblog';
    $node->uid = $user->uid;
    $node->language = '';
    $output .= render(drupal_get_form('microblog_node_form', $node));
  }
  else if ($account->uid == $user->uid) {
    $items[] = t('You are not allowed to post a new microblog update.');
  }
  else if ($account->uid != $user->uid) {
    $count  = db_query("SELECT COUNT(subscribed) from {microblog_subscription} WHERE subscribed = :subscribed AND subscriber = :subscriber", 
      array('subscribed' => $account->uid, 'subscriber' => $user->uid))->fetchField();
    if ($count > 0) { 
      $items[] = l(t("Stop following @name", array('@name' => $account->name)), 'microblog/'. $account->uid .'/leave', 
        array('attributes' => array('class' => 'microblog-ajax')));
    }
    else { 
      $items[] = l(t("Follow @name", array('@name' => $account->name)), 'microblog/'. $account->uid .'/follow', 
        array('attributes' => array('class' => 'microblog-ajax')));
    }
  }

  $replies = "microblog/". $account->uid ."/replies";
  $items[] = l(t('Replies'), $replies);

  $following = "microblog/". $account->uid ."/following";
  $items[] = l(t('Following'), $following);
  
  $followers = "microblog/". $account->uid ."/followers";
  $items[] = l(t('Followers'), $followers); 
  
  $output .= theme('microblog_links', array('items' => $items));
            
  if ($account->uid == $user->uid) {
    // Find updates from myself and those I subscribe to
    $subquery = db_select('microblog_subscription', 'm');
    $subquery->fields('m', array('subscribed'));
    $subquery->condition('subscriber', $user->uid);
    $query = db_select('node', 'n');
    $query->join('users', 'u', 'n.uid = u.uid');
    $result = $query
      ->fields('n', array('nid', 'uid', 'created', 'title'), 'u', array('picture', 'name'))
      ->condition('n.type', 'microblog')
      ->condition('n.status', 1)
      ->condition(db_or()->condition('n.uid', $user->uid)->condition('n.uid', array($subquery)))
      ->orderBy('n.created', 'DESC')
      ->extend('PagerDefault')
      ->limit(variable_get('default_nodes_main', '10'))
      ->execute();

  }
  else {
    $query = db_select('node', 'n');
    $query->join('users', 'u', 'n.uid = u.uid');
    $result = $query
      ->fields('n', array('nid', 'uid', 'created', 'title'), 'u', array('picture', 'name'))
      ->condition('n.type', 'microblog')
      ->condition('u.uid', $account->uid)
      ->condition('n.status', 1)
      ->orderBy('n.created', 'DESC')
      ->extend('PagerDefault')
      ->limit(variable_get('default_nodes_main', '10'))
      ->execute();
  }
  
  if ($result && !empty($result)) {
    $output .= theme('microblog_list', array('results' => $result));
  }
  else {
    if ($account->uid == $user->uid) {
      drupal_set_message(t('You have not created any microblog entries.'));
    }
    else {
      drupal_set_message(t('!author has not created any microblog entries.', array('!author' => theme('username', $account))));
    }
  }
  
  
  drupal_add_feed('microblog/'. $account->uid .'/feed', t('RSS - !title', array('!title' => $title)));

  return $output;
}

/**
 * Menu callback; displays a Drupal page containing recent microblog entries of all users.
 * aka "Public Timeline"
 */
function microblog_page_last() {
  global $user;

  $output = '';
  $items = array();

  if (user_access('edit own microblog')) {
    module_load_include('inc', 'node', 'node.pages');
    $node = new stdClass(); 
    $node->type = 'microblog';
    $node->uid = $user->uid;
    $node->language = '';
    $output .= render(drupal_get_form('microblog_node_form', $node));
  }
  
  $query = db_select('node', 'n');
  $query->join('users', 'u', 'n.uid = u.uid');
  $result = $query
    ->fields('n', array('nid', 'created', 'title', 'uid'), 'u', array('picture', 'name'))
    ->condition('n.type', 'microblog')
    ->condition('n.status', 1)
    ->orderBy('n.sticky', 'DESC')
    ->orderBy('n.created', 'DESC')
    ->extend('PagerDefault')
    ->limit(variable_get('default_nodes_main', 10))
    ->execute();

  if ($result && !empty($result)) {
    $output .= theme('microblog_list', array('results' => $result));
    $has_posts = TRUE;
  }
  else {
    drupal_set_message(t('No microblog entries have been created.'));
  }
  
  drupal_add_feed('microblog/feed', t('RSS - blogs'));

  return $output;
}

/**
 * Menu callback; displays those a user is following
 */
function microblog_page_following($account = NULL) {
  global $user;
  $output = '';

  drupal_add_js(drupal_get_path('module', 'microblog') . '/microblog.js');
  drupal_set_title($title = t("Who @name is following", array('@name' => $account->name)));
  $items = array();
  // should theme these differently - add form, not link, and add replies as tab
  if (($account->uid == $user->uid) && user_access('post microblog entries')) {
    module_load_include('inc', 'node', 'node.pages');
    $node = new stdClass(); 
    $node->type = 'microblog';
    $node->uid = $user->uid;
    $node->language = '';
    $output .= render(drupal_get_form('microblog_node_form', $node));
  }
  else if ($account->uid == $user->uid) {
    $items[] = t('You are not allowed to post a new microblog update.');
  }
  else if ($account->uid != $user->uid) {
    // todo: make follow and unfollow ajax calls, without leaving page?
    $count  = db_query("SELECT COUNT(subscribed) from {microblog_subscription} WHERE subscribed = :subscribed AND subscriber = :subscriber", 
      array('subscribed' => $account->uid, 'subscriber' => $user->uid))->fetchField();    
    
    if ($count > 0) { 
      $items[] = l(t("Stop following @name", array('@name' => $account->name)), 'microblog/'. $account->uid .'/leave', 
        array('attributes' => array('class' => 'microblog-ajax')));
    }
    else { 
      $items[] = l(t("Follow @name", array('@name' => $account->name)), 'microblog/'. $account->uid .'/follow', 
        array('attributes' => array('class' => 'microblog-ajax')));
    }
  }

  $replies = "microblog/". $account->uid ."/replies";
  $items[] = l(t('Replies'), $replies);

  $followers = "microblog/". $account->uid ."/followers";
  $items[] = l(t('Followers'), $followers); 

  $output .= theme('item_list', array('items' => $items));
  
  $query = db_select('users', 'u');
  $query->join('microblog_subscription', 'm', 'u.uid = m.subscribed');
  $result = $query
    ->fields('u', array('picture', 'name', 'uid'), 'm', array('subscriber', 'subscribed'))
    ->condition('subscriber', $account->uid)
    ->extend('PagerDefault')
    ->limit(variable_get('default_nodes_main', 10))
    ->execute()->fetchAll();
  
  if ($result && !empty($result)) {
    $output .= theme('microblog_people_list', array('results' => $result));
  }
  else {
    drupal_set_message(t('@name is not following anyone.', array('@name' => $account->name)));
  }
  
  return $output;
}

/**
 * Menu callback; displays those who are following a given user
 */
function microblog_page_followers($account = NULL) {
  global $user;
  $output = '';
  drupal_add_js(drupal_get_path('module', 'microblog') . '/microblog.js');
  drupal_set_title($title = t("Who is following @name", array('@name' => $account->name)));
  $items = array();
  // should theme these differently - add form, not link, and add replies as tab
  if (($account->uid == $user->uid) && user_access('post microblog entries')) {
    module_load_include('inc', 'node', 'node.pages');
    $node = new stdClass(); 
    $node->type = 'microblog';
    $node->uid = $user->uid;
    $node->language = '';
    $output .= render(drupal_get_form('microblog_node_form', $node));
  }
  else if ($account->uid == $user->uid) {
    $items[] = t('You are not allowed to post a new microblog update.');
  }
  else if ($account->uid != $user->uid) {
    $count  = db_query("SELECT COUNT(subscribed) from {microblog_subscription} WHERE subscribed = :subscribed AND subscriber = :subscriber", 
      array('subscribed' => $account->uid, 'subscriber' => $user->uid))->fetchField();

    if ($count > 0) { 
      $items[] = l(t("Stop following @name", array('@name' => $account->name)), 'microblog/'. $account->uid .'/leave', 
        array('attributes' => array('class' => 'microblog-ajax')));
    }
    else { 
      $items[] = l(t("Follow @name", array('@name' => $account->name)), 'microblog/'. $account->uid .'/follow', 
        array('attributes' => array('class' => 'microblog-ajax')));
    }
  }

  $replies = "microblog/". $account->uid ."/replies";
  $items[] = l(t('Replies'), $replies);

  $followers = "microblog/". $account->uid ."/following";
  $items[] = l(t('Following'), $followers); 

  $output .= theme('item_list', array('items' => $items));
  
  $query = db_select('users', 'u');
  $query->join('microblog_subscription', 'm', 'u.uid = m.subscriber');
  $result = $query
    ->fields('u', array('picture', 'name', 'uid'), 'm', array('subscriber', 'subscribed'))
    ->condition('subscribed', $account->uid)
    ->extend('PagerDefault')
    ->limit(variable_get('default_nodes_main', 10))
    ->execute()->fetchAll();
  
  if ($result && !empty($result)) {
    $output .= theme('microblog_people_list', array('results' => $result));
  }
  else {
    drupal_set_message(t('No one is following ' . $account->name . '.'));
  }
  
  return $output;
}


/**
 * Menu callback; displays an RSS feed containing recent microblog entries of a given user.
 */
function microblog_feed_user($account) {
  $query = db_select('node', 'n');
  $query->join('users', 'u', 'n.uid = u.uid');
  $result = $query
    ->fields('n', array('nid', 'created', 'title', 'uid'), 'u', array('picture', 'name'))
    ->condition('n.type', 'microblog')
    ->condition('n.uid', $account->uid)
    ->condition('n.status', 1)
    ->orderBy('n.created', 'DESC')
    ->execute();
  
  $channel['title'] = $account->name ."'s microblog";
  $channel['link'] = url('microblog/'. $account->uid, array('absolute' => TRUE));

  $items = array();
  foreach($result as $row) {
    $items[] = $row->nid;
  }
  node_feed($items, $channel); 
  // todo: replace node_feed or theme it? Need to remove extraneous stuff
}

/**
 * Menu callback; displays an RSS feed containing recent microblog entries of all users.
 */
function microblog_feed_last() {
  $query = db_select('node', 'n');
  $query->join('users', 'u', 'n.uid = u.uid');
  $result = $query
    ->fields('n', array('nid', 'created', 'title', 'uid'), 'u', array('picture', 'name'))
    ->condition('n.type', 'microblog')
    ->condition('n.status', 1)
    ->orderBy('n.created', 'DESC')
    ->execute()->fetchAll();
    print_r($query->__toString());
  $channel['title'] = variable_get('site_name', 'Drupal') .' microblogs';
  $channel['link'] = url('microblog', array('absolute' => TRUE));

  $items = array();
  foreach($result as $row) {
    $items[] = $row->nid;
  }
  node_feed($items, $channel); 
  // todo: replace node_feed or theme it? Need to remove extraneous stuff
}

/**
 * Menu callback; shows replies to user being viewed
 */
function microblog_page_replies($account = NULL) {
  global $user;
  $output = '';
  drupal_add_js(drupal_get_path('module', 'microblog') . '/microblog.js');
  if (($account->uid == $user->uid) && user_access('post microblog entries')) {
    module_load_include('inc', 'node', 'node.pages');
    $node = new stdClass(); 
    $node->type = 'microblog';
    $node->uid = $user->uid;
    $node->language = '';
    $output .= render(drupal_get_form('microblog_node_form', $node));
  }
  else if ($account->uid == $user->uid) {
    $items[] = t('You are not allowed to post a new microblog update.');
  }
  else if ($account->uid != $user->uid) {
    // todo: make follow and unfollow ajax calls, without leaving page?
    $count  = db_query("SELECT COUNT(subscribed) from {microblog_subscription} WHERE subscribed = :subscribed AND subscriber = :subscriber", 
      array('subscribed' => $account->uid, 'subscriber' => $user->uid))->fetchField();
    if ($count > 0) { 
      $items[] = l(t("Stop following @name", array('@name' => $account->name)), 'microblog/'. $account->uid .'/leave', 
        array('attributes' => array('class' => 'microblog-ajax')));
    }
    else { 
      $items[] = l(t("Follow @name", array('@name' => $account->name)), 'microblog/'. $account->uid .'/follow', 
        array('attributes' => array('class' => 'microblog-ajax')));
    }
  }
  $timeline = "microblog/". $user->uid ."/";
  $items[] = l(t('Timeline'), $timeline);


  $output .= theme('item_list', array('items' => $items));
  
  $query = db_select('node', 'n');
  $query->join('users', 'u', 'n.uid = u.uid');
  $result = $query
    ->fields('n', array('nid', 'created', 'title', 'uid'), 'u', array('picture', 'name'))
    ->condition('n.type', 'microblog')
    ->condition('n.status', 1)
    ->condition('n.title', '%@' . $account->name . '%', 'LIKE')
    ->orderBy('n.sticky', 'DESC')
    ->orderBy('n.created', 'DESC')
    ->extend('PagerDefault')
    ->limit(variable_get('default_nodes_main', 10))
    ->execute()->fetchAll();
  
  if ($result && !empty($result)) {
    $output .= theme('microblog_list', array('results' => $result));
  }
  else {
    drupal_set_message(t('There are no replies to @name\'s posts.', array('@name' => $account->name)));
  }
  
  drupal_add_feed(url('microblog/feed'), t('RSS - blogs'));

  return $output;
}

/**
 * Menu callback; enables user to follow local user, displays results
 * gets as a parameter the user to follow
 */
function microblog_follow_user($subscribed = NULL) {
  global $user;
  $output = '';
  if (!$subscribed || $subscribed->uid == 0) {
    $output .= t("Sorry, something bad happened. No user to follow.");
    print drupal_json_encode($output); 
    exit();
  }
    
  if ($user->uid == $subscribed->uid) {
    $output .= t("Silly rabbit, you can't follow yourself.");
    print drupal_json_encode($output); 
    exit();
  }
  if (db_query("SELECT subscriber FROM {microblog_subscription} WHERE subscriber = :subscriber AND subscribed = :subscribed", 
    array('subscriber' => $user->uid, 'subscribed' => $subscribed->uid))->fetchField()) {
    $output .= t("But you're already following " . $subscribed->name . "!");
    print drupal_json_encode($output); 
    exit();
  }
  $time = time();
  $fields = array(
    'subscriber' => $user->uid,
    'subscribed' => $subscribed->uid,
    'token' => NULL,
    'secret' => NULL,
    'created' => $time,
    'modified' => $time,
  );
  $result = db_insert('microblog_subscription')->fields($fields)->execute();

  if ($result) {  
    $output .= l(t("Stop following @name", array('@name' => $subscribed->name)), 'microblog/'. $subscribed->uid .'/leave', 
      array('attributes' => array('class' => 'microblog-ajax')));
    print drupal_json_encode($output); 
    exit();
  }
  
  $output .= t('Guess the db query failed.'); 
  print drupal_json_encode($output); 
  exit();
} 
 
 /**
 * Menu callback; enables user to stop following local user, displays results
 * gets as a parameter the user to unfollow
 */
function microblog_leave_user($subscribed = NULL) {
  global $user;
  $output = '';
  if (!$subscribed || $subscribed->uid == 0) {
    $output .= t("Sorry, something bad happened. No user to unfollow.");
    print drupal_json_encode($output); 
    exit();
  }
    
  if ($user->uid == $subscribed->uid) {
    $output .= t("Silly rabbit, you can't stop following yourself.");
    print drupal_json_encode($output); 
    exit();
  }
  
  $result = db_delete('microblog_subscription')
    ->condition('subscriber', $user->uid)
    ->condition('subscribed', $subscribed->uid)
    ->execute();

  if ($result) {  
    $output .= l(t("Follow @name", array('@name' => $subscribed->name)), 'microblog/'. $subscribed->uid .'/follow', 
        array('attributes' => array('class' => 'microblog-ajax')));
    print drupal_json_encode($output); 
    exit();
  }
  
  $output .= t('Guess the db query failed.'); 
    print drupal_json_encode($output); 
    exit(); 
} 

