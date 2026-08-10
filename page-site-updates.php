<?php
/*
Template Name: Site Updates
*/

$sup_css_path = get_stylesheet_directory() . '/assets/css/site-updates.css';

if (file_exists($sup_css_path)) {
    wp_enqueue_style(
        'author-child-site-updates',
        get_stylesheet_directory_uri() . '/assets/css/site-updates.css',
        array(),
        filemtime($sup_css_path)
    );
}

$sup_updates = new WP_Query(
    array(
        'post_type'      => 'update',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'no_found_rows'  => true,
    )
);

get_header();
?>

<main id="main" class="site-main sup-updates">
    <div class="sup-wrap">

        <header class="sup-header">
            <?php if (have_posts()) : ?>
                <?php
                while (have_posts()) :
                    the_post();
                    ?>
                    <?php the_title('<h1 class="sup-page-title">', '</h1>'); ?>

                    <?php if (trim(get_the_content())) : ?>
                        <div class="sup-intro">
                            <?php the_content(); ?>
                        </div>
                    <?php else : ?>
                        <p class="sup-intro">
                            Development updates, announcements, and build notes.
                        </p>
                    <?php endif; ?>
                    <?php
                endwhile;

                wp_reset_postdata();
                ?>
            <?php else : ?>
                <h1 class="sup-page-title">Site Updates</h1>

                <p class="sup-intro">
                    Development updates, announcements, and build notes.
                </p>
            <?php endif; ?>
        </header>

        <?php if (!$sup_updates->have_posts()) : ?>
            <div class="sup-empty">
                No updates found.
            </div>
        <?php else : ?>
            <div class="sup-grid">
                <?php
                foreach ($sup_updates->posts as $sup_post) :
                    $sup_link  = get_permalink($sup_post->ID);
                    $sup_title = get_the_title($sup_post->ID);
                    $sup_date  = get_the_date('M j, Y', $sup_post->ID);

                    $sup_excerpt = get_the_excerpt($sup_post->ID);

                    if ($sup_excerpt) {
                        $sup_excerpt = wp_trim_words(
                            wp_strip_all_tags($sup_excerpt),
                            22,
                            '…'
                        );
                    }
                    ?>
                    <article class="sup-card">
                        <header class="sup-card-top">
                            <h2 class="sup-card-title">
                                <a href="<?php echo esc_url($sup_link); ?>">
                                    <?php echo esc_html($sup_title); ?>
                                </a>
                            </h2>
                        </header>

                        <div class="sup-card-body">
                            <span class="sup-card-date">
                                <?php echo esc_html($sup_date); ?>
                            </span>

                            <?php if ($sup_excerpt) : ?>
                                <p class="sup-card-excerpt">
                                    <?php echo esc_html($sup_excerpt); ?>
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