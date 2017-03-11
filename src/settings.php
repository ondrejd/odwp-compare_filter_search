<?php
/**
 * Controller file for the settings page.
 *
 * @author  Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @package odwp-compare_filter_search
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}


if ( !function_exists( 'odwpcfs_settings_page_render' ) ) :
    /**
     * Render admin settings page.
     * @global wpdb $wpdb
     * @link https://codex.wordpress.org/Class_Reference/wpdb#SELECT_Generic_Results
     */
    function odwpcfs_settings_page_render() {
        global $wpdb;
        $table_name = $wpdb->prefix . ODWPCFS_DBTABLE;
        $messages = array();
        
        // Save settings
        $submit = filter_input( INPUT_POST, 'submit' );
        if ( ! empty( $submit ) ) {
            $action = 'insert';
        }

        $default_count_in_list = 4;

        // ...

?>
<form action='options.php' method='post'>
    <h2>compare_filter_search</h2>
<?php
        settings_fields( 'pluginPage' );
        do_settings_sections( 'pluginPage' );
        submit_button();
?>
</form>
<?php

        // Render template
        //ob_start( function() {} );
        //include_once ODWPCFS_PATH . '/templates/settings.phtml';
        //$html = ob_get_flush();
        //echo apply_filters( 'odwpcfs-settings', $html );
    }
endif;


if ( !function_exists( 'odwpcfs_settings_page_save' ) ) :
    /**
     * @internal
     * @global wpdb $wpdb
     * @link https://codex.wordpress.org/Class_Reference/wpdb#INSERT_row
     * @param integer $filter_id ID of updated filter or NULL if new one should be created.
     * @return boolean
     */
    function odwpcfs_settings_page_save( $filter_id = null ) {
        global $wpdb;
        $table_name = $wpdb->prefix . ODWPCFS_DBTABLE;

        $data = array( 
            'name' => filter_input( INPUT_POST, 'filter-name' ),
            'type' => filter_input( INPUT_POST, 'filter-type' ),
            'description' => filter_input( INPUT_POST, 'filter-description' ),
            'extra' => filter_input( INPUT_POST, 'filter-type2' )
        );

        $res = false;
        $format = array( '%s', '%s', '%s', '%s' );

        if ( is_null( $filter_id ) ) {
            $res = $wpdb->insert( $table_name, $data, $format );
        } else {
            $res = $wpdb->update( $table_name, $data, array( 'id' => $filter_id ), $format, array( '%d' ) );
        }

        return ( $res !== false && $res > 0);
    }
endif;


if ( !function_exists( 'odwpcfs_settings_page_delete' ) ) :
    /**
     * @internal
     * @global wpdb $wpdb
     * @link https://codex.wordpress.org/Class_Reference/wpdb#INSERT_row
     * @param array $filter_ids Array of IDs of filters to delete.
     * @return boolean
     */
    function odwpcfs_settings_page_delete( $filter_ids = array() ) {
        global $wpdb;
        $table_name = $wpdb->prefix . ODWPCFS_DBTABLE;
        $total_res = true;

        for ( $i = 0; $i < count( $filter_ids ); $i++ ) {
            $total_res = $total_res && ( $wpdb->delete( $table_name, array( 'id' => $filter_ids[$i] ), array( '%d' ) ) !== false );
        }

        return $total_res;
    }
endif;
