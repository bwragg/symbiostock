<?php
/**
 * The Template for displaying all image product pages.
 *
 * @package symbiostock
 * @since symbiostock 1.0
 */
get_header(); 
?>
		
        <div class="row-fluid">
        
            <div id="primary" class="content-area span12">
            
            <?php
			//add support for YOAST SEO
			 if ( function_exists('yoast_breadcrumb') ) {
            yoast_breadcrumb('<span class="text-info" id="breadcrumbs">','</span><hr />');
            } ?>          
                <div id="content" class="site-content" role="main">
                
				
                <?php while ( have_posts() ) : the_post(); ?>
   
                    <?php get_template_part( 'content-datasheet', 'single' ); ?>    				
    
                <?php endwhile; // end of the loop. ?>
    
                </div><!-- #content .site-content -->
            </div><!-- #primary .content-area -->
			
		</div>
        
<?php get_footer(); ?>