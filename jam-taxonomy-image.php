<?php
/*
Plugin Name: Jam Taxonomy Image
Plugin URI: http://www.jamviet.com/
Description: This plugin will help you add a image ( thumbnail ) to Taxonomy like Category, Tag, Custom Taxonomy and display image via Widget, or function name 'get_taxonomy_image($term_id)' return URL image.
Version: 1.0
Author: Jam Việt
Author URI: http://www.jamviet.com
*/


define( 'JAM_TAXONOMY_IMAGE_DIR', plugin_dir_path( __FILE__ ) );


include ( JAM_TAXONOMY_IMAGE_DIR . '/lib/taxonomy-metadata.php');

// Inital //

$taxonomy_metadata = new Taxonomy_Metadata;
register_activation_hook( __FILE__, array($taxonomy_metadata, 'setup_blog') );

/*
Admin 
*/

/** Step 2 (from text above). */
add_action( 'admin_menu', 'jam_taxonomy_images' );

/** Step 1. */
function jam_taxonomy_images() {
	add_options_page( 'Taxonomy Image', 'Taxonomy Image', 'manage_options', 'jam-taxonomy-Image', 'jam_taxonomy_plugin_options' );
}

/** Step 3. */
function jam_taxonomy_plugin_options() {
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	
	// SAVE IT 
	if ( isset ($_POST['taxonomy_using_image']) ) {
		update_option( 'taxonomy_using_image',  $_POST['taxonomy_using_image'] );
	}
		
	
	echo '<div class="wrap">';
		$args = array(
		  'public'   => true,
		  'rewrite' => true,
		  'show_ui' => true
		);
		$taxonomies = get_taxonomies($args, 'objects');
		
		echo '<h1>Select Taxonomy</h1>';
		echo '<p>Select Taxonomy to display image, it will add a form to upload image to Taxonomy in edit/add new screen !</p>';
		echo '<p>
		* Remember: I have added new Widget name "Jam Taxonomy List", You can user to display Taxonomy like Category, Tag or Custom taxonomy with description or Image, it is so nice in most case ! But i have not added Css to header to make sure your theme can not be break, if do not know how to make it nicer, visit me !<br>
		If you love to use this function in your custom theme, using this function: 
<pre>
get_taxonomy_image( $term_id );
</pre>
		it will return URL of the image you choose in Edit or Add new screen in Category, Tags or Custom Taxonomy.
		</p>';
		echo '
<form action="" method="post" name="jam_taxonomy_images">
	<table class="form-table">
		<tr>
			<th scope="row">Taxonomy to allow using Image</th>
			<td>
				<fieldset>
				<legend class="screen-reader-text"><span>Choose taxonomy</span></legend>';
				$z_saved  = (array) get_option('taxonomy_using_image');
				foreach ( $taxonomies as $taxonomy ) {
					$checked = '';
					if ( in_array($taxonomy->name, $z_saved) )
						$checked = ' checked=""';
					echo '
					<label title="Choose Taxonomy">
						<input '. $checked .' type="checkbox" value="'. $taxonomy->name .'" name="taxonomy_using_image[]"> '. $taxonomy->labels->name .'
					</label><br>';
				}
					echo '
				</fieldset>
			</td>
		</tr>
		
	</table>
	<button>Save now !</button>
</form>
		';
	echo '</div>';
}

/*
	add thickbox to it ...
*/

	add_action('admin_head', 'jam_add_thickbox_to_admin');


	function jam_add_thickbox_to_admin() {
	
		$screen = get_current_screen();
		if ( $screen->base != 'edit-tags')
			return false;
		wp_enqueue_script('jquery');
		// This will enqueue the Media Uploader script
		wp_enqueue_media();
?>
<script type="text/javascript">
jQuery(document).ready(function($){
    $('#upload-btn').click(function(e) {
        e.preventDefault();
        var image = wp.media({ 
            title: 'Upload Image',
            // mutiple: true if you want to upload multiple files at once
            multiple: false
        }).open()
        .on('select', function(e){
            // This will return the selected image from the Media Uploader, the result is an object
            var uploaded_image = image.state().get('selection').first();
            // We convert uploaded_image to a JSON object to make accessing it easier
            // Output to the console uploaded_image
            console.log(uploaded_image);
            var image_url = uploaded_image.toJSON().url;
            // Let's assign the url value to the input field
            $('#afterupload').val(image_url);
        });
    });
});
</script>
<?php
	}

	/*
	hook to edit _taxonomy
	*/
	$j_saved = get_option('taxonomy_using_image');
	if ( ! empty( $j_saved ) ) {
		foreach ( $j_saved as $k ) {
			#var_dump($k);
			add_action( "{$k}_edit_form_fields", 'jam_edit_taxonomy' );
			add_action( "{$k}_add_form_fields", 'jam_edit_taxonomy' );
			//save
			add_action( "edited_{$k}", 'jam_save_taxonomy', 10, 2 );
			add_filter("manage_edit-{$k}_columns",'jam_manage_my_taxonomy_columns');
			add_filter ("manage_{$k}_custom_column", 'jam_manage_taxonomy_custom_fields', 10,3);

		}
	}
	
	function jam_edit_taxonomy( $tag ) {
		// $tag là object //
		$tagid = 0;
		$img = '';
		if ( is_object($tag) ) {
			$tagid = $tag->term_id;
			$img = get_term_meta($tagid, 'category_thumb', true);
		}
	?>
	<tr class="form-field">
		<th scope="row" valign="top"><label>Taxonomy thumbnail</label></th>
		<td>
			<div class="form-field">
				<a id="upload-btn" href="#">Choose thumbnail</a>
				<div style="cursor:pointer">
					<input type="text" name="category_thumb" value="<?php echo $img ?>" id="afterupload">
				</div>
				<p>You can add any image size, display will depend on CSS or image size that you added before !</p>
			</div>
		</td>
	</tr>
	<?php
	}
	
	/* SAVE IT */
	function jam_save_taxonomy( $tagID ) {
		if ( isset ( $_POST['category_thumb'] ))
			update_term_meta( $tagID, 'category_thumb', $_POST['category_thumb'] );
	}
/*
////////////////////////// END CÁCH HIỂN THỊ CATEGORY ////////////////////////////////////////////
*/

function jam_manage_my_taxonomy_columns($columns)
{
 // add 'My Column'
 $columns['image_cateogory_column'] = 'Thumbnail';
 return $columns;
}

function jam_manage_taxonomy_custom_fields($deprecated,$column_name,$term_id)
{
	if ($column_name == 'image_cateogory_column') {
		if ( $data = get_term_meta( $term_id, 'category_thumb', true) )
		echo '<img src="'. $data .'" width="100" height="auto" />';
	}
}



/*
	DISPLAY !
	@ Avoid dublicated !
*/
if ( ! function_exists('get_taxonomy_image') ) {
	function get_taxonomy_image( $term_id = '' ) {
		if ( $data = get_term_meta( $term_id, 'category_thumb', true) )
			return $data;
		else
			return;
	}
}

/* other lib */
include ( JAM_TAXONOMY_IMAGE_DIR . '/lib/widget.php');