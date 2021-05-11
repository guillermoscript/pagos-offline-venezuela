<?php
/**
 * Create the section beneath the products tab
 **/


function rate_of_dolar_add_section( $sections ) {
	
	$sections['rate_of_dolar'] = __( 'Tasa del dolar', 'text-domain' );
	return $sections;
	
}

/**
 * Add settings to the specific section we created before
 */

function rate_of_dolar_all_settings( $settings, $current_section ) {
	/**
	 * Check the current section is what we want
	 **/
	if ( $current_section === 'rate_of_dolar' ) {
		$settings_slider = array();
		// Add Title to the Settings
		$settings_dolar[] = array( 
			'name' => __( 'Tasa del dolar', 'text-domain' ),
			'type' => 'title', 
			'desc' => __( 'Aqui es donde vas a poner la tasa del dolar', 'text-domain' ), 
			'id' => 'rate_of_dolar'
		);
		// Add first checkbox option
		$settings_dolar[] = array(
			'name'     => __( 'Activar la tasa', 'text-domain' ),
			'desc_tip' => __( 'Activa esto si quieres que automaticamente se busque el precio del dolar en la pagina del BCV.', 'text-domain' ),
			'id'       => 'rate_of_dolar_auto_insert',
			'type'     => 'checkbox',
			'css'      => 'min-width:300px;',
			'desc'     => __( 'Permitir el insertado', 'text-domain' ),
		);
		// Add second text field option
		$settings_dolar[] = array(
			'name'     => __( 'Tasa del dia de hoy', 'text-domain' ),
			'desc_tip' => __( 'Dolar hoy', 'text-domain' ),
			'id'       => 'rate_of_dolar_title',
			'type'     => 'text',
			'desc'     => __( 'Ejemplo: 430.000 Bs', 'text-domain' ),
		);
		
		$settings_dolar[] = array( 'type' => 'sectionend', 'id' => 'rate_of_dolar' );
		return $settings_dolar;
	
	/**
	 * If not, return the standard settings
	 **/
	} else {
		return $settings;
	}
}