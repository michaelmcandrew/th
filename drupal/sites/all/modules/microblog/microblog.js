/**
 * @file
 *
 * JS helper functions for Microblog Module
 */
(function ($) {
  Drupal.behaviors.microblogBehavior = {
    attach: function(context, settings) {
      $('a.microblog-ajax', context).click(function() {
        link = $(this);
        url = link.attr('href');
    		$.getJSON(url, function(data) {
    			link.fadeOut("normal", function() {
    				link.replaceWith($(data));
    				Drupal.attachBehaviors(this);
    			});
    		});
    		return false;
    	});
    }
  }
})(jQuery);