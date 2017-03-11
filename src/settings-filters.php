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


if ( !function_exists( 'odwpcfs_settings_filters_page_render' ) ) :
    /**
     * Render admin settings page.
     * @global wpdb $wpdb
     * @link https://codex.wordpress.org/Class_Reference/wpdb#SELECT_Generic_Results
     */
    function odwpcfs_settings_filters_page_render() {
        global $wpdb;
        $table_name = $wpdb->prefix . ODWPCFS_DBTABLE;
        $messages = array();

        // Action
        $action = filter_input( INPUT_GET, 'action' );
        if ( empty( $action ) ) {
            $action = filter_input( INPUT_POST, 'action' );
        }
        if ( empty( $action ) ) {
            $action = 'insert';
        }
        
        // Update action if cancel was pressed on the form.
        $cancel = filter_input( INPUT_POST, 'cancel' );
        if ( ! empty( $cancel ) ) {
            $action = 'insert';
        }

        // Is filter ID provided? Select that filter.
        $filter = null;
        $filter_id = (int) filter_input( $action == 'edit-filter' ? INPUT_GET : INPUT_POST, 'filter_id' );
        if ( $filter_id > 0 && in_array( $action, array( 'edit-filter', 'update-filter' ) ) ) {
            $filter = odwpcfs_get_filter_by_id( $filter_id );
        }

        // Form submit
        $submit = filter_input( INPUT_POST, 'submit' );
        // Insert new filter
        if ( ! empty( $submit ) && $action == 'insert' ) {
            if ( odwpcfs_settings_filters_page_save() === true ) {
                $messages[] = array( 'type' => 'success', 'msg' => __( 'Nový filtr byl úspěšně uložen.', ODWPCFS_SLUG ) );
            } else {
                $messages[] = array( 'type' => 'error', 'msg' => __( 'Uložení nového filtru se nezdařilo!', ODWPCFS_SLUG ) );
            }
        }
        // Update existing filter
        if ( ! empty( $submit ) && $action == 'update-filter' ) {
            if ( odwpcfs_settings_filters_page_save( $filter_id ) === true ) {
                $messages[] = array( 'type' => 'success', 'msg' => __( 'Filtr byl úspěšně aktualizován.', ODWPCFS_SLUG ) );
            } else {
                $messages[] = array( 'type' => 'error', 'msg' => __( 'Uložení filtru se nezdařilo!', ODWPCFS_SLUG ) );
            }
        }
        
        // Delete filter(s)
        if ( $action == 'delete' ) {
            $filter_ids = filter_input( INPUT_POST, 'id', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
            if ( odwpcfs_settings_filters_page_delete( $filter_ids ) === true ) {
                $messages[] = array( 'type' => 'success', 'msg' => __( 'Filtr byly úspěšně smazány.', ODWPCFS_SLUG ) );
            } else {
                $messages[] = array( 'type' => 'error', 'msg' => __( 'Odstranění filtrů se nezdařilo!', ODWPCFS_SLUG ) );
            }
        }

        // Select all available filters
        $filters = $wpdb->get_results( "SELECT * FROM $table_name WHERE 1 ORDER BY `name`, `type` " );
        // Prepare page's url
        $url = admin_url( 'edit.php?post_type=' . ODWPCFS_CPT . '&page=' . basename( ODWPCFS_FILE ) );

        // Render template
        ob_start( function() {} );
        include_once ODWPCFS_PATH . '/templates/settings-filters.phtml';
        $html = ob_get_flush();
        echo apply_filters( 'odwpcfs-settings-filters', $html );
    }
endif;


if ( !function_exists( 'odwpcfs_settings_filters_page_save' ) ) :
    /**
     * @internal
     * @global wpdb $wpdb
     * @link https://codex.wordpress.org/Class_Reference/wpdb#INSERT_row
     * @param integer $filter_id ID of updated filter or NULL if new one should be created.
     * @return boolean
     */
    function odwpcfs_settings_filters_page_save( $filter_id = null ) {
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


if ( !function_exists( 'odwpcfs_settings_filters_page_delete' ) ) :
    /**
     * @internal
     * @global wpdb $wpdb
     * @link https://codex.wordpress.org/Class_Reference/wpdb#INSERT_row
     * @param array $filter_ids Array of IDs of filters to delete.
     * @return boolean
     */
    function odwpcfs_settings_filters_page_delete( $filter_ids = array() ) {
        global $wpdb;
        $table_name = $wpdb->prefix . ODWPCFS_DBTABLE;
        $total_res = true;

        for ( $i = 0; $i < count( $filter_ids ); $i++ ) {
            $total_res = $total_res && ( $wpdb->delete( $table_name, array( 'id' => $filter_ids[$i] ), array( '%d' ) ) !== false );
        }

        return $total_res;
    }
endif;
