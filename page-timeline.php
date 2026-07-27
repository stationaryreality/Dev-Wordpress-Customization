<?php

get_header();

?>


<main class="timeline">


<h1>
Site Development Timeline
</h1>


<p>
Chronological history of engineering work,
architecture decisions, and milestones.
</p>



<?php


$args = [

'post_type'=>'record',

'posts_per_page'=>-1,

'meta_key'=>'create_time',

'orderby'=>'meta_value',

'order'=>'ASC'

];


$query = new WP_Query($args);



if ($query->have_posts()) :


while ($query->have_posts()) :

$query->the_post();


?>


<article class="timeline-item">


<div class="timeline-date">

<?php

$date=get_field('create_time');

echo esc_html(
date('Y-m-d',strtotime($date))
);

?>

</div>



<h2>

<a href="<?php the_permalink(); ?>">

<?php the_title(); ?>

</a>

</h2>



<?php

$project=get_field('primary_project');

if($project):

?>

<div class="timeline-category">

<?php


if(is_object($project)){

echo esc_html($project->name);

}

?>

</div>

<?php endif; ?>



</article>



<?php


endwhile;


endif;


wp_reset_postdata();


?>


</main>


<?php

get_footer();
