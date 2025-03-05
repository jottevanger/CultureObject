<?php
/**
 * Plugin Name: Culture Object
 * Plugin URI: http://cultureobject.co.uk
 * Description: A framework as a plugin to enable sync of culture objects into WordPress.
 * Version: 4.2
 * Author: Liam Gladdy / Thirty8 Digital
 * Text Domain: culture-object
 * Requires PHP: 8.1
 * Requires at least: 6.2
 * Author URI: https://github.com/lgladdy
 * License: Apache 2 License
 */

require_once 'vendor/autoload.php';

register_activation_hook( __FILE__, array( 'CultureObject\CultureObject', 'check_versions' ) );
register_activation_hook( __FILE__, array( 'CultureObject\CultureObject', 'regenerate_permalinks' ) );
register_deactivation_hook( __FILE__, array( 'CultureObject\CultureObject', 'regenerate_permalinks' ) );
$cos = new \CultureObject\CultureObject();

function cos_get_instance() {
	global $cos;
	return $cos;
}

function cos_get_remapped_field_name( $field_key ) {
	global $cos;
	return $cos->helper->cos_get_remapped_field_name( $field_key );
}

function cos_remapped_field_name( $field_key ) {
	global $cos;
	return $cos->helper->cos_remapped_field_name( $field_key );
}

function cos_get_field( $field_key ) {
	$id = get_the_ID();
	if ( ! $id ) {
		return false;
	}
	return get_post_meta( $id, $field_key, true );
}

function cos_the_field( $field_key ) {
	echo wp_kses_post( cos_get_field( $field_key ) );
}


function co_add_custom_box() {
	add_meta_box(
		'co_augmentations',                 // Unique ID
		'CultureObject augmentations',      // Box title
		'co_augmentations_html',  // Content callback, must be of type callable
		'object'                            // Post type
	);
}

function co_augmentations_html( $post ) {
	$description_str = get_post_meta( $post->ID, 'description_str', true );
	$_aug_odour = get_post_meta( $post->ID, '_aug_odour', true );
	?>
	<button name="co_odour_enrich" id="co_odour_enrich">How do I smell?</button>
	<br /><br />
	<h3>Odeuropa terms *</h3>
	<br />
	<textarea name="co_odour" id="co_odour" class="postbox"  rows="10" cols="50"><?php echo $_aug_odour; ?></textarea>
	<input type="hidden" id="inputData" value="<?php echo $description_str; ?>" />
	<input type="hidden" id="responseData" value="salt lemon acid sharp sulphur" />
	<p>* actually the APILayer Keyword Extraction API.</p>
	<script>
	jQuery(document).ready(function() {
		jQuery('#co_odour_enrich').click(function(e) {
			var inputData = jQuery('#inputData').val();
			var response = jQuery('#responseData').val();

			alert("I'm busy sending the following description to our cool enrichment service\n\n" + inputData);
			jQuery.ajax({
                    url: 'https://api.apilayer.com/keyword',
					xhrFields: { withCredentials: true },
                    type: 'POST',
					headers: {'apikey': '##your key here##'},
                    data: { data: inputData },
                    success: function(response) {
						alert("Cool, I got something back!\n\n");
						if(typeof(response)==="object"){
							let op = "";
							for(i=0;i<response.result.length;i++){
								op += response.result[i].text+"\n";
							}
							jQuery('#co_odour').val(op);
						}
                    },
                    error: function(error) {
                        jQuery('#co_odour').val('Error: ' + error.responseText);
                    }
                });

//			document.getElementById('co_odour').value =responseData;
			return false;
			//			alert();
                /*
				*/
		});
	});
       

</script>
	<?php
}

function co_save_postdata( $post_id ) {
	if ( array_key_exists( 'co_odour', $_POST ) ) {
		update_post_meta(
			$post_id,
			'_aug_odour',
			$_POST['co_odour']
		);
	}
}