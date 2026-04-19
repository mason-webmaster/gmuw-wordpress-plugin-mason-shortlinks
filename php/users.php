<?php

/**
 * Summary: php file which implements the user-related customizations
 */


/**
 * function to get user name
 */
function gmuw_sl_get_username($user_id) {

    //do we have a user id?
    if ($user_id>0) {
        return get_user_by('id', $user_id)->user_login;
    } else {
        return '-';
    }

}

/**
 * manage user columns
 */
add_filter( 'manage_users_columns', function( $columns ) {

    // unset the default 'posts' column
    unset( $columns['posts'] );

    // unset the default 'name' column
    unset( $columns['name'] );

    //add columns for our custom usermeta

    //links to users redirects
    $columns['redirect_group_link'] = 'User Redirects';

    //group permissions
    $columns['gmuw_sl_user_groups'] = 'Group Permissions';

    //return updated columns
    return $columns;

});


/**
 * content for custom user columns
 */
add_filter( 'manage_users_custom_column', function( $output, $column_name, $user_id ) {

	//links to users redirects
    if ( $column_name === 'redirect_group_link' ) {
        $url = admin_url('tools.php?page=redirection.php&filterby%5Bgroup%5D='.gmuw_sl_redirection_get_user_redirection_group_id($user_id));
        return '<a href="' . esc_url( $url ) . '">View User Redirects</a>';
    }

    //groups
    if ( $column_name === 'gmuw_sl_user_groups' ) {
        return str_replace(',','<br />',get_the_author_meta( $column_name, $user_id ));
    }

	//return
    return $output;

}, 10, 3 );

//set up aray of custom user fields
function gmuw_sl_custom_fields_array() {

    //set up array
    $my_fields = array(
        array('heading','', 'Shorlink Management Information', ''),
        array('text','gmuw_sl_user_groups', 'Group Permissions', 'Please enter a comma-separated list of all groups which this user can manage.'),
    );

    //return value
    return $my_fields;

}

add_action( 'show_user_profile', 'gmuw_sl_extra_user_profile_fields' );
add_action( 'edit_user_profile', 'gmuw_sl_extra_user_profile_fields' );
add_action( 'user_new_form', 'gmuw_sl_extra_user_profile_fields' );

function gmuw_sl_extra_user_profile_fields($user) { 

    //only for admins
    if (!current_user_can('manage_options')) return;

    echo '<table class="form-table">';
    
    //set list of fields and data
    $my_fields = gmuw_sl_custom_fields_array();

    //output fields
    foreach ($my_fields as $my_field) {
        if ($my_field[0]=='heading') {
            echo '<tr><th colspan="2"><h3>'.$my_field[2].' '.$my_field[3].'</h3></th></tr>';
        }
        if (($my_field[0]=='text')||($my_field[0]=='email')) {
            echo gmuw_sl_user_profile_field_text($user,$my_field[1], $my_field[2], $my_field[3]);
        }
        if (($my_field[0]=='checkbox')) {
            echo gmuw_sl_user_profile_field_checkbox($user,$my_field[1], $my_field[2], $my_field[3]);
        }
    }

    echo '</table>';
    
}

// return user profile screen checkbox field
function gmuw_sl_user_profile_field_checkbox($user,$field_name, $field_title, $field_desc) {

    //initialize variables
    $return_value='';

    //pf_title
    $return_value.='<tr>';
    $return_value.='<th><label for="'.$field_name.'">'.$field_title.'</label></th>';
    $return_value.='<td>';

    $return_value.='<table class="pf-profile-layout">';
    $return_value.='<tr>';

    $return_value.='<td>';
    //user-entered field
    $return_value.='<input type="checkbox" name="'.$field_name.'" id="'.$field_name.'" value="1" '. (get_user_meta( $user->ID, $field_name, true )==1 ? ' checked' : '') .' /><br />';
    $return_value.='<p><span class="description">'.$field_desc.'</span></p>';
    $return_value.='</td>';

    $return_value.='</tr>';
    $return_value.='</table>';


    $return_value.='</td>';
    $return_value.='</tr>';

    //return value
    return $return_value;

}

// return user profile screen text field
function gmuw_sl_user_profile_field_text($user,$field_name, $field_title, $field_desc) {

    //initialize variables
    $return_value='';

    //pf_title
    $return_value.='<tr>';
    $return_value.='<th><label for="'.$field_name.'">'.$field_title.'</label></th>';
    $return_value.='<td>';

    $return_value.='<table class="pf-profile-layout">';
    $return_value.='<tr>';
    
    $return_value.='<td>';
    //user-entered field
    $return_value.='<input type="text" name="'.$field_name.'" id="'.$field_name.'" value="' . esc_attr( get_user_meta( $user->ID, $field_name, true ) ) . '" class="regular-text" /><br />';
    $return_value.='<p><span class="description">'.$field_desc.'</span></p>';
    $return_value.='</td>';

    $return_value.='</tr>';
    $return_value.='</table>';


    $return_value.='</td>';
    $return_value.='</tr>';

    //return value
    return $return_value;

}

add_action( 'personal_options_update', 'gmuw_sl_save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'gmuw_sl_save_extra_user_profile_fields' );
add_action( 'user_register', 'gmuw_sl_save_extra_user_profile_fields' );

function gmuw_sl_save_extra_user_profile_fields( $user_id ) {

    //check for capabilities
    if ( !current_user_can( 'edit_user', $user_id ) ) { 
        return false; 
    }

    //set list of my fields
    $my_fields = gmuw_sl_custom_fields_array();

    //save fields
    foreach ($my_fields as $my_field) {
        //If we have this field posted, then process it. (If the user is created programmatically, these fields will not be posted and we'll get warnings.)
        if (isset($_POST[$my_field[1]])) {
            if ($my_field[0]=='text' || $my_field[0]=='checkbox') {
                //save the field data
                gmuw_sl_save_extra_user_profile_field($user_id, $my_field[1]);
            }
            if ($my_field[0]=='email') {
                if (is_email($_POST[$my_field[1]]) || empty($_POST[$my_field[1]])) {
                    gmuw_sl_save_extra_user_profile_field($user_id, $my_field[1]);
                }
            }
        }

        //but if it's a checkbox and it's not set, clear it
        if ( $my_field[0]=='checkbox' && !isset($_POST[$my_field[1]]) ) {
            update_user_meta( $user_id, $my_field[1], '' );
        }

    }

}

function gmuw_sl_save_extra_user_profile_field( $user_id, $field_name ) {

    //prepare form data for saving depending on field name
    switch ($field_name) {
        case 'gmuw_sl_user_groups':

            //get submitted data
            $my_data=$_POST[$field_name];

            //lowercase
            $my_data=strtolower($my_data);

            //remove spaces
            $my_data=str_replace(' ','',$my_data);

            //confirm only valid characters
            if (!preg_match("/^[a-z0-9_\-,]*$/", $my_data)) {
                $field_value='';
            } else {
               $field_value=sanitize_text_field($my_data); 
            }

            break;

        default:
            $field_value=sanitize_text_field(strtolower($_POST[$field_name]));
            break;
    }

    //update user-entered field
    update_user_meta( $user_id, $field_name, $field_value );

}

//function to get array of user groups from user meta
function gmuw_sl_get_user_groups_array(){

    //get user meta for group permissions
    $my_group_data=get_user_meta(get_current_user_id(), 'gmuw_sl_user_groups', true);

    //turn into array
    $return_value=explode(',',$my_group_data);

    //return value
    return $return_value;

}
