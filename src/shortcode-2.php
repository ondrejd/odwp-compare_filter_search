<?php
/**
 * Controller for shortcode "Přehled vlastností společnosti".
 *
 * @author  Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @package odwp-compare_filter_search
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}


if ( ! function_exists( 'odwpcfs_add_shortcode_2' ) ) :
    /**
     * Register shortcode "Přehled vlastností společnosti".
     * @global wpdb $wpdb
     * @param array $atts
     * @param string $content (Optional.)
     * @return string
     */
    function odwpcfs_add_shortcode_2( $atts, $content = null ) {
        global $wpdb;
        $table_name = $wpdb->prefix . ODWPCFS_DBTABLE;

        // Collect attributes
        $attrs = shortcode_atts( array(
            'id' => 0,
            'title' => __( 'Přehled vlastností', ODWPCFS_SLUG ),
            'show_title' => 0,
        ), $atts );

        $sql     = "SELECT * FROM $table_name WHERE 1 ORDER BY `name`, `type` ";
        $filters = $wpdb->get_results( $sql, OBJECT_K );
        $f_vals  = array();
        foreach ( $filters as $filter ) {
            $meta_name = sprintf( 'odwpcfs-metabox-5-%d', $filter->id );
            $f_vals[$filter->id] = get_post_meta( intval( $attrs['id'] ), $meta_name, true );
        }

        // Render template
        ob_start( function() {} );
        include_once( ODWPCFS_PATH . '/templates/shortcode-2.phtml' );
        $html = ob_get_flush();
        return apply_filters( 'odwpcfs-shortcode-2', $html );
    }
endif;
add_shortcode( 'tabulka-vlastnosti', 'odwpcfs_add_shortcode_2' );


// Functions below are for TinyMCE button for this shortcode:

if ( ! function_exists( 'odwpcfs_add_tinymce_btn_shortcode_2' ) ) :
/**
 * Enable easy access to our shortcode via TinyMCE button.
 * @global string $typenow
 */
function odwpcfs_add_tinymce_btn_shortcode_2() {
    global $typenow;

    if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
        return;
    }
    // verify the post type
    if ( ! in_array( $typenow, array( 'post', 'page' ) ) ) {
        return;
    }
    // check if WYSIWYG is enabled
    if ( get_user_option( 'rich_editing' ) == 'true' ) {
        add_filter( 'mce_external_plugins', 'odwpcfs_register_tinymce_plugin_shortcode_2' );
        add_filter( 'mce_buttons', 'odwpcfs_register_tinymce_btn_shortcode_2' );
    }
}
endif;
//add_action( 'admin_head', 'odwpcfs_add_tinymce_btn_shortcode_2' );


if ( ! function_exists( 'odwpcfs_register_tinymce_btn_shortcode_2' ) ) :
    /**
     * Register new TinyMCE buttons.
     * @param array $buttons
     * @return array
     */
    function odwpcfs_register_tinymce_btn_shortcode_2( $buttons ) {
       array_push( $buttons, 'separator', 'odwpcfs_shortcode_2' );
       return $buttons;
    }
endif;


if ( ! function_exists( 'odwpcfs_register_tinymce_plugin_shortcode_2' ) ) :
    /**
     * Register our TinyMCE plugin.
     * @param array $plugins
     * @return array
     */
    function odwpcfs_register_tinymce_plugin_shortcode_2( $plugins ) {
        $plugins['odwpcfs_shortcode_2'] = plugins_url( '/js/tinymce-plugin-shortcode-1.js', ODWPCFS_FILE );
        return $plugins;
    }
endif;
