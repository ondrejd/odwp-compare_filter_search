<?php
add_action( 'admin_menu', 'odwp_add_admin_menu' );
add_action( 'admin_init', 'odwp_settings_init' );


function odwp_add_admin_menu(  ) { 

	add_menu_page( 'compare_filter_search', 'compare_filter_search', 'manage_options', 'compare_filter_search', 'odwp_options_page' );

}


function odwp_settings_init(  ) { 

	register_setting( 'pluginPage', 'odwp_settings' );

	add_settings_section(
		'odwp_pluginPage_section', 
		__( 'Your section description', 'odwpcfs_cpt' ), 
		'odwp_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'odwp_text_field_0', 
		__( 'Settings field description', 'odwpcfs_cpt' ), 
		'odwp_text_field_0_render', 
		'pluginPage', 
		'odwp_pluginPage_section' 
	);

	add_settings_field( 
		'odwp_checkbox_field_1', 
		__( 'Settings field description', 'odwpcfs_cpt' ), 
		'odwp_checkbox_field_1_render', 
		'pluginPage', 
		'odwp_pluginPage_section' 
	);


}


function odwp_text_field_0_render(  ) { 

	$options = get_option( 'odwp_settings' );
	?>
	<input type='text' name='odwp_settings[odwp_text_field_0]' value='<?php echo $options['odwp_text_field_0']; ?>'>
	<?php

}


function odwp_checkbox_field_1_render(  ) { 

	$options = get_option( 'odwp_settings' );
	?>
	<input type='checkbox' name='odwp_settings[odwp_checkbox_field_1]' <?php checked( $options['odwp_checkbox_field_1'], 1 ); ?> value='1'>
	<?php

}


function odwp_settings_section_callback(  ) { 

	echo __( 'This section description', 'odwpcfs_cpt' );

}


function odwp_options_page(  ) { 

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

}
