<?php get_header(); ?>
<?php
$options = get_option('eo_theme_options');


/**
 * Set Category sidebar/top text
 *
 */
if(is_home()) {
    $catID = 'home';
    $sidebarContent = <<<EOT
<div class="sidebar-info">
    <div class="sprite sprite-myavatar"></div>
</div>

<h1>Emil Öberg</h1>

<div class="sidebar-socialmedia-links">
    <a href="//twitter.com/emiloberg" class="sprite sprite-link sprite-64 sprite-twitter"></a>
    <a href="//www.facebook.com/EmilOberg" class="sprite sprite-link sprite-64 sprite-facebook"></a>
    <a href="//www.linkedin.com/in/emiloberg" class="sprite sprite-link sprite-64 sprite-linkedin"></a>
</div>

<div class="sidebar-contacts">
    <ul>
        <li>cell +46 739 85 25 85</li>
        <li>work cell +46 704 25 05 41</li>
        <li><a href="mailto:me@emiloberg.se">me@emiloberg.se</a></li>
    </ul>
</div>
EOT;
} elseif (is_category()) {
    $catID = $cat;

    $catTitle = '<h1>' . single_cat_title( $prefix = '', $display = false) . '</h1>';
    $catDesc = category_description();

    if(!empty($catDesc)) {
        $catDesc = '<div class="section-text no-bottom-padding">' . $catDesc . '</div>';
    }


    $sidebarContent = $catTitle . $catDesc;
}



?>

<div id="content" class="has-side">

    <aside id="section-bar">
        <div class="section-image">
            <div class="bar-text">
                <?php echo $sidebarContent; ?>
            </div>
        </div>

    </aside>

    <div id="inner-content" class="wrap clearfix">

        <div id="main" class="clearfix" role="main">


            <?php if (is_category()) { ?>
                <h1 class="archive-title">
                    <span><?php _e( 'Posts Categorized:', 'bonestheme' ); ?></span> <?php single_cat_title(); ?>
                </h1>

            <?php } elseif (is_tag()) { ?>
                <h1 class="archive-title">
                    <span><?php _e( 'Posts Tagged:', 'bonestheme' ); ?></span> <?php single_tag_title(); ?>
                </h1>

            <?php } elseif (is_author()) {
                global $post;
                $author_id = $post->post_author;
                ?>
                <h1 class="archive-title">

                    <span><?php _e( 'Posts By:', 'bonestheme' ); ?></span> <?php the_author_meta('display_name', $author_id); ?>

                </h1>
            <?php } elseif (is_day()) { ?>
                <h1 class="archive-title">
                    <span><?php _e( 'Daily Archives:', 'bonestheme' ); ?></span> <?php the_time('l, F j, Y'); ?>
                </h1>

            <?php } elseif (is_month()) { ?>
                <h1 class="archive-title">
                    <span><?php _e( 'Monthly Archives:', 'bonestheme' ); ?></span> <?php the_time('F Y'); ?>
                </h1>

            <?php } elseif (is_year()) { ?>
                <h1 class="archive-title">
                    <span><?php _e( 'Yearly Archives:', 'bonestheme' ); ?></span> <?php the_time('Y'); ?>
                </h1>
            <?php } ?>

            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class( 'clearfix' ); ?> role="article">

                    <h1 class="h2"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>
                    <a href="<?php the_permalink() ?>" class="excerpt-link"><?php the_excerpt(); ?></a>
                    <p class="byline vcard">
                        Skrivet av <?php echo get_the_author(); ?> för <?php echo human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' sedan'; ?>.
                        <?php print eoPrintCategories(get_the_category()); ?>
                    </p>

                </article> <?php // end article ?>

            <?php endwhile; ?>

                <?php if ( function_exists( 'bones_page_navi' ) ) { ?>
                    <?php bones_page_navi(); ?>
                <?php } else { ?>
                    <nav class="wp-prev-next">
                        <ul class="clearfix">
                            <li class="prev-link"><?php next_posts_link( __( '&laquo; Older Entries', 'bonestheme' )) ?></li>
                            <li class="next-link"><?php previous_posts_link( __( 'Newer Entries &raquo;', 'bonestheme' )) ?></li>
                        </ul>
                    </nav>
                <?php } ?>

            <?php else : ?>

                <article id="post-not-found" class="hentry clearfix">
                    <header class="article-header">
                        <h1><?php _e( 'Oops, Post Not Found!', 'bonestheme' ); ?></h1>
                    </header>
                    <section class="entry-content">
                        <p><?php _e( 'Uh Oh. Something is missing. Try double checking things.', 'bonestheme' ); ?></p>
                    </section>
                    <footer class="article-footer">
                        <p><?php _e( 'This is the error message in the index.php template.', 'bonestheme' ); ?></p>
                    </footer>
                </article>

            <?php endif; ?>

        </div> <?php // end #main ?>


    </div> <?php // end #inner-content ?>

</div> <?php // end #content ?>

<?php get_footer(); ?>
