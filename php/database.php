<?php

/**
 * Summary: php file which implements customizations related to the database
 */


//function to create a custom redirect meta table
function gmuw_sl_create_redirect_meta_table() {
    global $wpdb;

    //set table name with the correct prefix
    $table_name = $wpdb->prefix . 'gmuw_sl_redirectmeta';
    
    //get the character set and collation (usually utf8mb4)
    $charset_collate = $wpdb->get_charset_collate();

    //define the SQL structure
    //note: Use two spaces after PRIMARY KEY and specify column lengths for indexing.
    $sql = "CREATE TABLE $table_name (
        meta_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        redirect_id bigint(20) UNSIGNED NOT NULL DEFAULT '0',
        meta_key varchar(255) DEFAULT NULL,
        meta_value longtext DEFAULT NULL,
        PRIMARY KEY  (meta_id),
        KEY redirect_id (redirect_id),
        KEY meta_key (meta_key(191))
    ) $charset_collate;";

    //include the upgrade.php file to access dbDelta()
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    
    //execute the query
    dbDelta( $sql );
}

//function to get metadata from the custom redirect meta table
/**
 * Update metadata for a redirect.
 *
 * @param int    $redirect_id Redirect ID.
 * @param string $meta_key    Metadata key.
 * @param mixed  $meta_value  Metadata value. Must be serializable if not a string.
 * @return int|bool Meta ID if a new row is created, true on successful update, 
 * or false on failure/no change.
 */
function update_redirect_meta( $redirect_id, $meta_key, $meta_value ) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'gmuw_sl_redirectmeta';

    // 1. Sanitize the ID and Key
    $redirect_id = absint( $redirect_id );
    if ( ! $redirect_id ) {
        return false;
    }

    // 2. Handle serialization (just like WordPress does)
    // This allows you to pass arrays or objects as the meta_value.
    $maybe_fixed_value = maybe_serialize( $meta_value );

    // 3. Check if the record already exists
    $existing_id = $wpdb->get_var( $wpdb->prepare(
        "SELECT meta_id FROM $table_name WHERE redirect_id = %d AND meta_key = %s",
        $redirect_id,
        $meta_key
    ) );

    if ( $existing_id ) {
        // 4. Update the existing record
        // $wpdb->update returns the number of rows affected (1) or 0 if the value is the same.
        $result = $wpdb->update(
            $table_name,
            array( 'meta_value' => $maybe_fixed_value ), // Data
            array( 'meta_id'    => $existing_id ),       // Where
            array( '%s' ),                               // Data format
            array( '%d' )                                // Where format
        );

        // Return true if updated, or true if the values were identical (matching core behavior)
        return ( $result !== false );
    } else {
        // 5. Insert a new record
        $result = $wpdb->insert(
            $table_name,
            array(
                'redirect_id' => $redirect_id,
                'meta_key'    => $meta_key,
                'meta_value'  => $maybe_fixed_value
            ),
            array( '%d', '%s', '%s' )
        );

        // Return the new meta_id if successful
        return $result ? (int) $wpdb->insert_id : false;
    }
}

//function to retrieve metadata from the custom redirect meta table
/**
 * Retrieve metadata for a redirect.
 *
 * @param int    $redirect_id Redirect ID.
 * @param string $meta_key    Optional. The meta key to retrieve. If empty, retrieves all data for the ID.
 * @param bool   $single      Optional. If true, returns only the first value found. Default true.
 * @return mixed Will be an array if $single is false. Will be value of meta_data of $single is true.
 */
function get_redirect_meta( $redirect_id, $meta_key = '', $single = true ) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'gmuw_sl_redirectmeta';
    $redirect_id = absint( $redirect_id );

    if ( ! $redirect_id ) {
        return false;
    }

    // 1. Build the query based on whether a specific key was requested
    if ( ! empty( $meta_key ) ) {
        $query = $wpdb->prepare(
            "SELECT meta_value FROM $table_name WHERE redirect_id = %d AND meta_key = %s",
            $redirect_id,
            $meta_key
        );
        
        if ( $single ) {
            $result = $wpdb->get_var( $query );
            return maybe_unserialize( $result );
        } else {
            $results = $wpdb->get_col( $query );
            return array_map( 'maybe_unserialize', $results );
        }
    } else {
        // 2. If no key is provided, return all meta for this redirect (as an associative array)
        $results = $wpdb->get_results( $wpdb->prepare(
            "SELECT meta_key, meta_value FROM $table_name WHERE redirect_id = %d",
            $redirect_id
        ) );

        $meta_list = array();
        foreach ( $results as $row ) {
            $meta_list[ $row->meta_key ][] = maybe_unserialize( $row->meta_value );
        }
        
        return $meta_list;
    }
}

//function to delete metadata from the custom redirect meta table
function delete_redirect_meta( $redirect_id, $meta_key, $meta_value = '' ) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'gmuw_sl_redirectmeta';
    
    $where = array( 'redirect_id' => $redirect_id, 'meta_key' => $meta_key );
    if ( ! empty( $meta_value ) ) {
        $where['meta_value'] = maybe_serialize( $meta_value );
    }

    return $wpdb->delete( $table_name, $where );
}
