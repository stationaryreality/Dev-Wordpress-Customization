<?php

add_filter(
'wpseo_breadcrumb_links',
function($links){


    if (is_singular('record')) {


        $timeline = get_page_by_path('timeline');


        if ($timeline) {


            $new = [

                [
                    'url'=>home_url('/'),
                    'text'=>'Home'
                ],

                [
                    'url'=>get_permalink($timeline),
                    'text'=>'Timeline'
                ]

            ];


            $new[] = end($links);


            return $new;

        }

    }


    return $links;


});