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

if (!function_exists('sup_get_update_count_label')) {
    function sup_get_update_count_label($count) {
        $count = (int) $count;

        return 1 === $count ? '1 update' : sprintf('%d updates', $count);
    }
}

if (!function_exists('sup_render_update_card')) {
    function sup_render_update_card($update_post) {
        $link  = get_permalink($update_post->ID);
        $title = get_the_title($update_post->ID);
        $date  = get_the_date('M j, Y', $update_post->ID);

        $has_thumbnail = has_post_thumbnail($update_post->ID);

        $thumbnail_url = '';
        $thumbnail_alt = $title;

        if ($has_thumbnail) {
            $thumbnail_url = get_the_post_thumbnail_url($update_post->ID, 'medium_large');

            $thumbnail_id  = get_post_thumbnail_id($update_post->ID);
            $thumbnail_alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);

            if (empty($thumbnail_alt)) {
                $thumbnail_alt = $title;
            }
        }

        $excerpt = get_the_excerpt($update_post->ID);

        if ($excerpt) {
            $excerpt = wp_trim_words(
                wp_strip_all_tags($excerpt),
                22,
                '…'
            );
        }

        $fallback_month = get_the_date('M', $update_post->ID);
        $fallback_day   = get_the_date('d', $update_post->ID);
        ?>
        <article class="sup-card">
            <a class="sup-media" href="<?php echo esc_url($link); ?>">
                <?php if ($has_thumbnail && $thumbnail_url) : ?>
                    <img
                        src="<?php echo esc_url($thumbnail_url); ?>"
                        alt="<?php echo esc_attr($thumbnail_alt); ?>"
                    >
                <?php else : ?>
                    <span class="sup-media-fallback" aria-hidden="true">
                        <span class="sup-media-month">
                            <?php echo esc_html($fallback_month); ?>
                        </span>

                        <span class="sup-media-day">
                            <?php echo esc_html($fallback_day); ?>
                        </span>
                    </span>
                <?php endif; ?>
            </a>

            <div class="sup-card-body">
                <span class="sup-card-date">
                    <?php echo esc_html($date); ?>
                </span>

                <h3 class="sup-card-title">
                    <a href="<?php echo esc_url($link); ?>">
                        <?php echo esc_html($title); ?>
                    </a>
                </h3>

                <?php if ($excerpt) : ?>
                    <p class="sup-card-excerpt">
                        <?php echo esc_html($excerpt); ?>
                    </p>
                <?php endif; ?>
            </div>
        </article>
        <?php
    }
}

/*
|--------------------------------------------------------------------------
| Query updates and group them by year
|--------------------------------------------------------------------------
*/

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

$sup_groups = array();

if ($sup_updates->have_posts()) {
    foreach ($sup_updates->posts as $sup_post) {
        $sup_year = get_the_date('Y', $sup_post->ID);

        if (!isset($sup_groups[$sup_year])) {
            $sup_groups[$sup_year] = array();
        }

        $sup_groups[$sup_year][] = $sup_post;
    }
}

wp_reset_postdata();

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

        <?php if (empty($sup_groups)) : ?>
            <div class="sup-empty">
                No updates found.
            </div>
        <?php else : ?>

            <?php if (count($sup_groups) > 1) : ?>
                <nav class="sup-year-nav" aria-label="Update years">
                    <?php foreach (array_keys($sup_groups) as $sup_year) : ?>
                        <a href="#sup-year-<?php echo esc_attr($sup_year); ?>">
                            <?php echo esc_html($sup_year); ?>
                        </a>
                    <?php endforeach; ?>
                </nav>
            <?php endif; ?>

            <?php foreach ($sup_groups as $sup_year => $sup_posts) : ?>
                <section class="sup-year-section" id="sup-year-<?php echo esc_attr($sup_year); ?>">
                    <header class="sup-year-header">
                        <h2 class="sup-year-title">
                            <?php echo esc_html($sup_year); ?>
                        </h2>

                        <span class="sup-year-count">
                            <?php echo esc_html(sup_get_update_count_label(count($sup_posts))); ?>
                        </span>
                    </header>

                    <div class="sup-grid">
                        <?php foreach ($sup_posts as $sup_post) : ?>
                            <?php sup_render_update_card($sup_post); ?>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>

        <?php endif; ?>

    </div>
</main>

<?php get_footer(); ?>