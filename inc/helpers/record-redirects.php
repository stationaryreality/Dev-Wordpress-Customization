<?php

/**
 * Record archive redirect
 *
 * Records are accessed through Timeline.
 */

add_action(
'template_redirect',
function(){

    if (!is_post_type_archive('record')) {
        return;
    }


    wp_redirect(
        home_url('/timeline/'),
        301
    );

    exit;

});