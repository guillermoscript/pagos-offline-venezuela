<?php
/**
 * Create the section beneath the products tab
 **/


function tasa_dolar_add_section( $sections ) {
	
	$sections['tasa_dolar'] = __( 'Tasa del dolar', 'text-domain' );
	return $sections;
	
}

/**
 * Add settings to the specific section we created before
 */

function tasa_dolar_all_settings( $settings, $current_section ) {
	/**
	 * Check the current section is what we want
	 **/
	if ( $current_section == 'tasa_dolar' ) {
		$settings_slider = array();
		// Add Title to the Settings
		$settings_dolar[] = array( 
			'name' => __( 'Tasa del dolar', 'text-domain' ),
			'type' => 'title', 
			'desc' => __( 'Aqui es donde vas a poner la tasa del dolar', 'text-domain' ), 
			'id' => 'tasa_dolar'
		);
		// Add first checkbox option
		$settings_dolar[] = array(
			'name'     => __( 'Activar la tasa', 'text-domain' ),
			'desc_tip' => __( 'Activa esto si quieres que automaticamente se busque el precio del dolar en la pagina del BCV.', 'text-domain' ),
			'id'       => 'tasa_dolar_auto_insert',
			'type'     => 'checkbox',
			'css'      => 'min-width:300px;',
			'desc'     => __( 'Permitir el insertado', 'text-domain' ),
		);
		// Add second text field option
		$settings_dolar[] = array(
			'name'     => __( 'Tasa del dia de hoy', 'text-domain' ),
			'desc_tip' => __( 'Dolar hoy', 'text-domain' ),
			'id'       => 'tasa_dolar_title',
			'type'     => 'text',
			'desc'     => __( 'Ejemplo: 430.000 Bs', 'text-domain' ),
		);
		
		$settings_dolar[] = array( 'type' => 'sectionend', 'id' => 'tasa_dolar' );
		return $settings_dolar;
	
	/**
	 * If not, return the standard settings
	 **/
	} else {
		return $settings;
	}
}