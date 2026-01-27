<?php

/**
 * Summary: php file which implements customizations related to permissions
 */


function gmuw_sl_roles_and_caps_setup() {

    // modify default administrator role
    get_role('administrator')->add_cap('create_shortlinks');

    // set up mason_user role. this role is intended for regular Mason users of this system

    // first, remove the role if it exists
    remove_role('mason_user');

    // then re-add the role
    add_role('mason_user','Mason User',array());

    // add the read and upload_files capability to the role
	get_role('mason_user')->add_cap('read');
	get_role('mason_user')->add_cap('create_shortlinks');

}

function gmuw_sl_roles_and_caps_cleanup() {

    // modify default administrator role
    get_role('administrator')->remove_cap('create_shortlinks');

    // mason_user
    remove_role('mason_user');

}
