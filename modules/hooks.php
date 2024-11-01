<?php 
function wwb_add_order_number_start_setting( $settings ) {

	$updated_settings = array();
  
	foreach ( $settings as $section ) {
  
	  // at the bottom of the General Options section
	  if ( isset( $section['id'] ) && 'general_options' == $section['id'] &&
		 isset( $section['type'] ) && 'sectionend' == $section['type'] ) {
  
			$updated_settings[] = array(
				'name'     => __( 'Use Backbox API', 'wwb' ),
				'desc_tip' => '',
				'id'       => 'woocommerce_use_blackbox_api',
				'type'     => 'checkbox',
				'css'      => '',
				'std'      => '',  // WC < 2.0
				'default'  => '',  // WC >= 2.0
				'desc'     =>  __( 'Turn on if you want to use Blackbox API', 'wwb' )  ,
			  );

			$updated_settings[] = array(
		  'name'     => __( 'Backbox API key', 'wwb' ),
		  'desc_tip' => __( 'Please, enter your API key.', 'wwb' ),
		  'id'       => 'woocommerce_blackbox_api_key',
		  'type'     => 'text',
		  'css'      => 'min-width:300px;',
		  'std'      => '',  // WC < 2.0
		  'default'  => '',  // WC >= 2.0
		  'desc'     => sprintf( __( 'You can find API key <a target="_blank" href="%s">here</a>', 'wwb' ), 'https://blackbox.net.ua/store/#api-panel'),
		);
	  }
  
	  $updated_settings[] = $section;
	}
  
	return $updated_settings;
  }
add_filter( 'woocommerce_general_settings', 'wwb_add_order_number_start_setting' );


function wwb_make_api_call( $phone_number ){
	$key = get_option( 'woocommerce_blackbox_api_key', 1 );
	$usage = get_option( 'woocommerce_use_blackbox_api', 1 );
 
 
	if( $usage == 'yes' ){


	$requestParams = [
		"id" => 101100,
		"params" => [
			"phonenumber" => $phone_number,
	 
			"api_key"     => $key,
		]
	];
	
	 
	$response = wp_remote_get( "http://blackbox.net.ua/api/?data=" . json_encode($requestParams) );
	
	$result = json_decode( $response['body'] );


	 
	if( $result->success ){
		$out_string = '';
		if( $result->data ){
			foreach( $result->data as $key => $info ){
				$user_name = $info->fios[0];
				$user_phone = $info->phone;
				$out_string .= '<div style="background-color: #f14668;
				color: #fff;border-radius: 4px;
				padding: 1.25rem 2.5rem 1.25rem 1.5rem; margin-bottom:10px;">'.sprintf( __('User: %s %s is in blacklist', 'wwb'),$user_name, $user_phone ).'</div>';
			}
		}else{
			$out_string .= '<div style="background-color: #48c774;
			color: #fff;border-radius: 4px;
			padding: 1.25rem 2.5rem 1.25rem 1.5rem; margin-bottom:10px;">'.__('User not in black list', 'wwb').'</div>';
		}
		
	}else{
		if( $result->error ){
			$out_string .= '<div style="background-color: #ffdd57;
			color: rgba(0,0,0,.7); border-radius: 4px;
			padding: 1.25rem 2.5rem 1.25rem 1.5rem; margin-bottom:10px;">'.$result->message.'</div>';
		}
	}

	return $out_string;
	 
	}
}


//var_dump( make_api_call() );



add_action( 'woocommerce_email_order_details', 'wwb_action_email_order_details', 10, 4 );
function wwb_action_email_order_details( $order, $sent_to_admin, $plain_text, $email ) {
     if( $sent_to_admin ): // For admin emails notification

	//prepare phone
	$phone = $order->billing_phone;

	// patch for entries
	$phone = str_replace(' ', '', $phone);
	$phone = str_replace(')', '', $phone);
	$phone = str_replace('(', '', $phone);

	$result = substr($phone, 0, 3);
	if( $result == '+38' ){
		$phone = substr($phone, 3); 
	}
	$result = substr($phone, 0, 2);
	if( $result == '38' ){
		$phone = substr($phone, 2); 
	}
	$result = substr($phone, 0, 1);
	if( $result == '8' ){
		$phone = substr($phone, 1); 
	}

	
 
	echo  wwb_make_api_call( $phone );
 

    // Your code goes HERE

     endif;
}

 
?>