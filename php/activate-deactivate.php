<?php

/**
 * Summary: php file which implements the theme initialization tasks
 */


//plugin activation function
function gmuw_sl_plugin_activate(){

    //activation tasks

	//setup permissions
    gmuw_sl_roles_and_caps_setup();
	
}

function gmuw_sl_plugin_deactivate() {

    //deactivation tasks

    //cleanup permissions
    gmuw_sl_roles_and_caps_cleanup();

}
