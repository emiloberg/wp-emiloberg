<?php get_header(); ?>


            <?php
            if(has_post_thumbnail()) {
                $contentClass = "has-lead-image";
            } else {
                $contentClass = "no-lead-image";
            }
            ?>

			
			<div id="content" class="<?php print $contentClass; ?>">

				<div id="inner-content" class="wrap clearfix <?php echo (has_post_thumbnail() ? 'has-leadimg' : 'no-leadimg'); ?>">

					<div id="main" class="clearfix" role="main">

						<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

                            <?php

                            if(has_post_thumbnail()) {
                                $leadImageMeta = get_post_meta(get_post_thumbnail_id($post->ID));
                                eo_print_single_post_main_image($post->ID, $leadImageMeta, 'leadimage-wide', 'wide-lead-image');
                            }
                            ?>

							<article id="post-<?php the_ID(); ?>"
                                    <?php post_class('main-article clearfix ' .
                                        'push-article-' .
                                        eo_get_class_from_leadimg_crop($leadImageMeta['leadimg_start_article_at'][0]));
                                    ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">
  							<div class="inner-article">


								<header class="article-header">
									<h1 class="entry-title single-title" itemprop="headline"><?php the_title(); ?></h1>
								</header> <?php // end article header ?>
								
								<div class="author">
									<p>
										Skrivet av <?php echo get_the_author(); ?> <?php echo human_time_diff( get_the_time('U'), current_time('timestamp') ) . ' sedan'; ?>
                                        <?php
                                        //print the_tags('<span class="tags"><span class="amp">&amp;</span> taggat ', ' ', '</span>');
                                        ?>
                                        <?php print eoPrintCategories(get_the_category(), 'div'); ?>
									</p>
								</div>

								<section class="entry-content clearfix" itemprop="articleBody">
									<?php the_content(); ?>
								</section> <?php // end article section ?>

								<footer class="article-footer">

								</footer> <?php // end article footer ?>

								<?php comments_template(); ?>

							</div> <?php // end inner-main ?>
							</article> <?php // end article ?>


						<?php endwhile; ?>

						<?php else : ?>

							<article id="post-not-found" class="hentry clearfix">
									<header class="article-header">
										<h1><?php _e( 'Oops, Post Not Found!', 'bonestheme' ); ?></h1>
									</header>
									<section class="entry-content">
										<p><?php _e( 'Uh Oh. Something is missing. Try double checking things.', 'bonestheme' ); ?></p>
									</section>
									<footer class="article-footer">
											<p><?php _e( 'This is the error message in the single.php template.', 'bonestheme' ); ?></p>
									</footer>
							</article>

						<?php endif; ?>


                        <?php

                        //TODO: WORKING HERE (Remove 1 == 2)
                        $nextPost = get_adjacent_post(true);
                        if(has_post_thumbnail($nextPost->ID) && 1 == 2) {
                        $nextPostLeadImageMeta = get_post_meta(get_post_thumbnail_id($nextPost->ID));

                            ?>
                            <footer class="next-post">
                                <a href="<?php print get_permalink($nextPost->ID) ?>">
                                    <?php eo_print_single_post_main_image($nextPost->ID, $nextPostLeadImageMeta, 'next-post-wide', 'wide-next-image', false); ?>
                                </a>
                                <div class="texts">
                                    <p class="pre-heading">Read this next</p>
                                    <h1><?php print $nextPost->post_title ?></h1>
                                    <p><?php print $nextPost->post_excerpt ?></p>
                                </div>
                            <footer>
                            <?
                        }


                        ?>

					</div> <?php // end #main ?>

				</div> <?php // end #inner-content ?>

			</div> <?php // end #content ?>


<?php get_template_part('foot', 'article'); ?>

<?php get_footer(); ?>
