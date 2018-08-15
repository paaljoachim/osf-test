<?php

// A Test site for spirituellfilm.no 

// Include Beans. Do not remove the line below.
require_once( get_template_directory() . '/lib/init.php' );

/*
 * Remove this action and callback function if you do not whish to use LESS to style your site or overwrite UIkit variables.
 * If you are using LESS, make sure to enable development mode via the Admin->Appearance->Settings option. LESS will then be processed on the fly.
 */
/*add_action( 'beans_uikit_enqueue_scripts', 'beans_child_enqueue_uikit_assets' );
function beans_child_enqueue_uikit_assets() {
	beans_compiler_add_fragment( 'uikit', get_stylesheet_directory_uri() . '/style.less', 'less' );

}*/

// Remove this action and callback function if you are not adding CSS in the style.css file.
add_action( 'wp_enqueue_scripts', 'beans_child_enqueue_assets' );
function beans_child_enqueue_assets() {
	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css' );
	
}



/*------ CUSTOM CODE for OSF test theme -----*/


// Remove site description
beans_remove_action( 'beans_site_title_tag' );

// Remove the post title.
//beans_remove_action( 'beans_post_title' );

// remove page titles suggested by Tonya: https://community.getbeans.io/discussion/remove-page-titles-but-not-post-titles/ */
beans_add_smart_action('beans_before_posts_loop', 'remove_page_title_on_pages_only' );
function remove_page_title_on_pages_only() {
  if ( is_singular() && is_page() ) {
   remove_all_actions( 'beans_post_header' );
  }
}


// Remove breadcrumbs.
beans_remove_action( 'beans_breadcrumb' );


// Removes featured image on single post AND a page.
add_action( 'wp', 'beans_child_setup_document' );
function beans_child_setup_document() {
   if ( is_single() or is_page() ) {					
        beans_remove_action( 'beans_post_image' );
    }
}

/* --- Resize featured images seen on the blog page: 
https://community.getbeans.io/discussion/default-featured-image-size/ --- */
add_filter( 'beans_edit_post_image_args', 'example_post_image_edit_args' );
function example_post_image_edit_args( $args ) {
    return array_merge( $args, array(
        'resize' => array( 300, true ),
    ) );
} 


/* ----------- Excerpt for the blog page. -------------- */
add_filter( 'the_content', 'hyperindian_modify_post_content' );
function hyperindian_modify_post_content( $content ) {
    // Stop here if we are on a single view.
    if ( is_singular() )
        return $content;

    // Return the excerpt() if it exists other truncate.
    if ( has_excerpt() )
        $content = '<p>' . get_the_excerpt() . '</p>';
    else
        $content = '<p>' . wp_trim_words( get_the_content(), 60, '...' ) . '</p>';

    // Return content and readmore.
    return $content . '<p>' . beans_post_more_link() . '</p>';
}

/* Modify Continue reading text link to Read more.
https://community.getbeans.io/discussion/modify-wordpress-language/ */
add_filter( 'beans_post_more_link_text_output', 'example_modify_read_more' );
function example_modify_read_more() {
 return 'Read more..';
}



/* ------ POST META ----*/

// Example code to remove or add fields.
add_filter( 'beans_post_meta_items', 'beans_child_remove_post_meta_items' );
function beans_child_remove_post_meta_items( $items ) {

// Remove
 unset( $items['author'] );
 unset( $items['comments'] );
 return $items;
}

// Remove the post meta categories below the content.
beans_remove_action( 'beans_post_meta_categories' );

// Remove the post meta tags below the content.
beans_remove_action( 'beans_post_meta_tags' );

// Removing prefixes for date, author, categories and tags.
beans_remove_output( 'beans_post_meta_date_prefix' );


// End blog page customizations


//Navigation menu - using Uikit code to move nav left.
beans_remove_attribute( 'beans_primary_menu', 'class', 'uk-float-right' );
beans_add_attribute( 'beans_primary_menu', 'class', 'uk-float-left' );

// Register footer and social menu
register_nav_menu('footer-menu', __( 'Footer Menu', 'osf-beans'));
register_nav_menu('social-menu', __( 'Social Menu', 'osf-beans'));

// Add the footer menu
beans_add_smart_action( 'beans_footer_prepend_markup', 'fast_monkey_footer_menu' );
function fast_monkey_footer_menu() {
	wp_nav_menu( array( 'theme_location' => 'footer-menu',
						'container' => 'nav',
	 					'container_class' => 'tm-footer-menu uk-margin-bottom', // Added uk-margin-bottom from kkthemes code.
						'menu_class' => 'uk-navbar-nav',
											
						'depth' => 1, // For drop down menus change to 0
					));
}




/*----- Copyright information bottom left and right ----*/
  
  // LEFT text
  add_filter( 'beans_footer_credit_text_output', 'modify_left_copyright' );
  function modify_left_copyright() {
  	// Add your copyright html, text, Dynamic date and times etc.
  	 ?><p>© <?php echo date('Y'); ?> OSF test site <a href="<?php echo admin_url();?>" title="Login to the backend of WordPress." />Login.</a></p>
  	<?php
  }
  
  // RIGHT text 
  add_action( 'beans_footer_credit_right_text_output', 'modify_right_copyright' );
  function modify_right_copyright() {
   	?> Built by <a href="http://easywebdesigntutorials.com/" target="_blank" title="Easy Web Design Tutorials"> Paal Joachim</a> with <a href="http://www.getbeans.io/" title="Beans Framework for WordPress" target="_blank">Beans WordPress Framework</a>.
   	<?php
  } 



/* ------------------- Widget locations ----------------*/

/* Widget areas */
// Register a widget area below header.
add_action( 'widgets_init', 'flipster_below_header_widget_area' );

function flipster_below_header_widget_area() {

    beans_register_widget_area( array(
        'name' => 'Below Header',
        'id' => 'below-header',
        'beans_type' => 'stack'
    ) );
}

beans_add_smart_action('beans_main_prepend_markup', 'flipster_below_header_widget_output');
//Display the Widget area
function flipster_below_header_widget_output() {
	?>
	<div class="tm-below-header-widget-area">
			<?php echo beans_widget_area( 'below-header' ); ?>
	</div>
	<?php
}

// Register a widget area before footer.
add_action( 'widgets_init', 'before_footer_widget_area' );

function before_footer_widget_area() {

    beans_register_widget_area( array(
        'name' => 'Before Footer',
        'id' => 'before-footer',
        'beans_type' => 'stack'
    ) );
}

beans_add_smart_action('beans_footer_prepend_markup', 'before_footer_widget_output');
//Display the Widget area
function before_footer_widget_output() {
	?>
	<div class="tm-before-footer-widget-area">
			<?php echo beans_widget_area( 'before-footer' ); ?>
	</div>
	<?php
}


// Register a widget area below blogroll.
add_action( 'widgets_init', 'flipster_below_blogroll_widget_area' );

function flipster_below_blogroll_widget_area() {

    beans_register_widget_area( array(
        'name' => 'Below Blogroll',
        'id' => 'below-blogroll',
        'beans_type' => 'stack'
    ) );
}

beans_add_smart_action('beans_posts_pagination_before_markup', 'flipster_below_blogroll_widget_output');
//Display the Widget area
function flipster_below_blogroll_widget_output() {
	?>
	<div class="tm-below-blogroll-widget-area">
			<?php echo beans_widget_area( 'below-blogroll' ); ?>
	</div>
	<?php
}

// Register a widget area below post content.
add_action( 'widgets_init', 'flipster_below_post_widget_area' );

function flipster_below_post_widget_area() {

    beans_register_widget_area( array(
        'name' => 'Below Post',
        'id' => 'below-post',
        'beans_type' => 'stack'
    ) );
}

//Display the Widget area
function flipster_widget_after_post_content( $content ) {
	$output =  $content;
	$output .=  '<div class="tm-below-post-widget-area">';
	$output .=   beans_widget_area( 'below-post' );
	$output .=  '</div>';
	return $output;
}




/*----------- Other custom modifications ------------*/


/* Back To Top button */

add_action( 'wp_footer', 'back_to_top' );
 function back_to_top() {
 echo '<a id="totop" href="#" data-btn-alt="Topp">⬆︎</a>';
 }

add_action( 'wp_head', 'back_to_top_style' );
 function back_to_top_style() {
 echo '<style type="text/css">
 #totop {
 position: fixed;
 right: 30px;
 bottom: 30px;
 display: none;
 outline: none;
 text-decoration: none;
 font-size: 26px;
 background: rgba(42, 64, 67, 0.2); 
 padding: 10px 20px 5px 20px; 
 border-radius: 5px;
 border: 1px solid #ccc;
 box-shadow: 0 0 1px #000;
 color: #fff;
 z-index: 100;
 }
 
 #totop:hover {
 background: rgba(42, 64, 67, 1);
 }
 
 #totop:hover:after{
 content: attr(data-btn-alt);
 font-size: 16px;
 color: #fff;
 padding-left: 5px;
 }
 </style>';
 
 }

add_action( 'wp_footer', 'back_to_top_script' );
 function back_to_top_script() {
 echo '<script type="text/javascript">
 jQuery(document).ready(function($){
 $(window).scroll(function () {
 if ( $(this).scrollTop() > 1500 ) 
 $("#totop").fadeIn();
 else
 $("#totop").fadeOut();
 });

$("#totop").click(function () {
 $("body,html").animate({ scrollTop: 0 }, 1400 );
 return false;
 });
 });
 </script>';
 }




/* Bigger embed size http://cantonbecker.com/work/musings/2011/how-to-change-automatic-wordpress-youtube-embed-size-width/ */
add_filter( 'embed_defaults', 'bigger_embed_size' );
function bigger_embed_size()
{ 
 return array( 'width' => 910, 'height' => 590 );
}


// Add support for editor stylesheet - using twenty Sixteens editor stylesheet.
add_editor_style( 'assets/css/editor-style.css' );


/* --------- Bottom of backend Admin screen -  Custom admin footer credits https://github.com/gregreindel/greg_html5_starter -----*/

add_filter( 'admin_footer_text', create_function( '$a', 'return \'<span id="footer-thankyou">Site managed by <a href="http://www.easywebdesigntutorials.com" target="_blank">Paal Joachim Romdahl </a><span> | Powered by <a href="http://www.wordpress.org" target="_blank">WordPress</a>\';' ) );



// Modify the WordPress login screen.
//
// https://github.com/JiveDig/baseline/blob/master/functions.php
/**
 * Change login logo
 * Max image width should be 320px
 * @link http://andrew.hedges.name/experiments/aspect_ratio/
 */
add_action('login_head',  'tsm_custom_dashboard_logo');
function tsm_custom_dashboard_logo() {
	echo '<style  type="text/css">
		body.login {
		   background-color: #fff;
		}
		
		#login {
		  margin: 0 auto;
		  padding: 25px;
		}
		
		.login h1 a {
			background-image:url(' . get_stylesheet_directory_uri() . '/images/osf-logo.jpg)  !important;
			background-size: 300px auto !important;
			width: 100% !important;
			height: 150px !important;
			
		}
		
		#login form { 
		 box-shadow:0 2px 3px #444 !important;
		 border-radius: 7px;
		 background: #eeecec;
		 font-size: 18px;
		}
		
		.login #nav {
		 font-size: 18px;		 
		}
		
	</style>';
}

// Change login link
add_filter('login_headerurl','tsm_loginpage_custom_link');
function tsm_loginpage_custom_link() {
	return get_site_url();
}

