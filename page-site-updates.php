<?php
/*
Template Name: Site Updates
*/

get_header();
?>

<main class="entry-content site-updates-page">

    <article class="page-content">

        <h1><?php the_title(); ?></h1>

        <?php while (have_posts()) : the_post(); ?>

            <?php the_content(); ?>

        <?php endwhile; ?>

    </article>

    <hr class="homepage-divider">

    <section class="updates-archive">

        <h2 class="page-section-title">
            Development Updates
        </h2>

        <?php

        $updates = new WP_Query([
            'post_type'      => 'update',
            'posts_per_page' => -1,
            'orderby'        => 'date',
            'order'          => 'DESC'
        ]);

        if ($updates->have_posts()) :

            echo '<div class="tag-posts-grid">';

            while ($updates->have_posts()) :

                $updates->the_post();
                ?>

                <div class="tag-post-item">

                    <a
                        href="<?php the_permalink(); ?>"
                        class="tag-post-thumbnail"
                    >

                        <?php if (has_post_thumbnail()) : ?>

                            <img
                                src="<?php the_post_thumbnail_url('medium'); ?>"
                                alt="<?php the_title(); ?>"
                            >

                        <?php endif; ?>

                    </a>

                    <a
                        href="<?php the_permalink(); ?>"
                        class="tag-post-title"
                    >
                        <?php the_title(); ?>
                    </a>

                    <p class="tag-post-excerpt">
                        <?php the_excerpt(); ?>
                    </p>

                    <small class="update-date">
                        <?php echo get_the_date(); ?>
                    </small>

                </div>

                <?php

            endwhile;

            echo '</div>';

            wp_reset_postdata();

        else :

            echo '<p>No updates found.</p>';

        endif;

        ?>

    </section>

</main>

<?php get_footer(); ?>