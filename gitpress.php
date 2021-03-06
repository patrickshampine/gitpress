<?php
/*
Plugin Name: gitpress
Plugin URI: https://github.com/patrickshampine/gitpress
Description: Simple GitHub atom feed parser as a WordPress plugin.
Version: 0.1
Author: Patrick Shampine
Author URI: http://patrickshampine.com
Author Email: patrick@patrickshampine.com
*/

function gitpress() {
  $rss = fetch_feed('https://github.com/patrickshampine.atom');

  if (!is_wp_error($rss)) {
    $maxitems = $rss->get_item_quantity(10);
    $rss_items = $rss->get_items(0, $maxitems);
  }

  $count = 0;

  echo '<div class="gitrss-container">';

  foreach($rss_items as $history) {

    $data = $history->data;
    $content = $data['child']['http://www.w3.org/2005/Atom'];
    $published = $content['published'][0]['data'];
    $link = $content['link'][0]['attribs']['']['href'];
    $title = $content['title'][0]['data'];

    $active = '';

    if($count === 0) {
      $active = 'gitrss-active';
    }

    echo '<div class="gitrss '.$active.'" data-gitrss="'.$count.'" target="_blank">';
    echo '<h6><a href="'.$link.'">'.$title.'</a></h6>';
    echo '</div>';

    $count++;

    if($count >= 10) {
      break;
    }
  }

  echo '</div>';
}

function gitpressScripts() { ?>
  <style>
    .gitrss-container {
      position: absolute;
      overflow: hidden;
      width: 100%;
      height: 50px;
      bottom: 0;
      background-color: lightgray;
    }
    .gitrss {
      display:none;
      position: relative;
      width: 100%;
      height: inherit;
      margin: 0 auto;
      text-align: center;
    }
    .gitrss h6 {
      padding: 10px;
      font-size: 1.1em;
    }
    .gitrss-active {
      display: block;
    }
  </style>
  <script>
  jQuery(document).ready(function($) {
    var gitrss = $('.gitrss');

    function fade() {
      var current = $('.gitrss-active');
      var currentIndex = gitrss.index(current),
        nextIndex = currentIndex + 1;
      
      if (nextIndex >= gitrss.length) {
        nextIndex = 0;
      }
      
      var next = gitrss.eq(nextIndex);
      
      next.fadeIn(2500, function() {
        $(this).addClass('gitrss-active');
      });
      
      current.fadeOut(2500, function() {
        $(this).removeClass('gitrss-active');
        setTimeout(fade, 5000);
      });
    }

    fade();
  });
  </script><?php

}
add_action('wp_head', 'gitpressScripts');
