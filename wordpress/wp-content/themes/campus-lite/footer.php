<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Campus Lite
 */
?>
</div><!-- main-container -->

<div class="copyright-wrapper">
        	<div class="container">
                <div class="copyright">
                    	<p><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php esc_attr(bloginfo( 'name' )); ?>  <?php echo date_i18n( __( 'Y', 'campus-lite' ) ); ?>. <?php _e('Powered by WordPress','campus-lite'); ?></p>               
                </div><!-- copyright --><div class="clear"></div>           
            </div><!-- container -->
        </div>
    </div>
        
<?php wp_footer(); ?>

</body>
</html>