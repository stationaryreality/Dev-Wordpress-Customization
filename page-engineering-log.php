<?php
/*
Template Name: Engineering Log
*/

$eng_css_path = get_stylesheet_directory() . '/assets/css/engineering-log.css';

if (file_exists($eng_css_path)) {
    wp_enqueue_style(
        'author-child-engineering-log',
        get_stylesheet_directory_uri() . '/assets/css/engineering-log.css',
        array(),
        filemtime($eng_css_path)
    );
}

$eng_notes = new WP_Query(
    array(
        'post_type'      => 'note',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,
    )
);

get_header();
?>

<main id="main" class="site-main eng-logs">
    <div class="eng-wrap">

        <header class="eng-header">
            <?php if (have_posts()) : ?>
                <?php
                while (have_posts()) :
                    the_post();
                    ?>
                    <?php the_title('<h1 class="eng-page-title">', '</h1>'); ?>

                    <?php if (trim(get_the_content())) : ?>
                        <div class="eng-intro">
                            <?php the_content(); ?>
                        </div>
                    <?php else : ?>
                        <p class="eng-intro">
                            Engineering logs, technical notes, and build documentation.
                        </p>
                    <?php endif; ?>
                    <?php
                endwhile;

                wp_reset_postdata();
                ?>
            <?php else : ?>
                <h1 class="eng-page-title">Engineering Logs</h1>

                <p class="eng-intro">
                    Engineering logs, technical notes, and build documentation.
                </p>
            <?php endif; ?>
        </header>

        <?php if (!$eng_notes->have_posts()) : ?>
            <div class="eng-empty">
                No engineering logs found.
            </div>
        <?php else : ?>
            <div class="eng-grid">
                <?php
                foreach ($eng_notes->posts as $eng_post) :
                    $eng_link  = get_permalink($eng_post->ID);
                    $eng_title = get_the_title($eng_post->ID);
                    $eng_date  = get_the_date('M j, Y', $eng_post->ID);

                    $eng_excerpt = get_the_excerpt($eng_post->ID);

                    if ($eng_excerpt) {
                        $eng_excerpt = wp_trim_words(
                            wp_strip_all_tags($eng_excerpt),
                            22,
                            '…'
                        );
                    }
                    ?>
                    <article class="eng-card">
                        <header class="eng-card-top">
                            <h2 class="eng-card-title">
                                <a href="<?php echo esc_url($eng_link); ?>">
                                    <?php echo esc_html($eng_title); ?>
                                </a>
                            </h2>
                        </header>

                        <div class="eng-card-body">
                            <span class="eng-card-date">
                                <?php echo esc_html($eng_date); ?>
                            </span>

                            <?php if ($eng_excerpt) : ?>
                                <p class="eng-card-excerpt">
                                    <?php echo esc_html($eng_excerpt); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </article>
                    <?php
                endforeach;
                ?>
            </div>
        <?php endif; ?>

        <?php wp_reset_postdata(); ?>

    </div>
</main>

<?php get_footer(); ?>