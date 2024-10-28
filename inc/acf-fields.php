<?php

/**
 * Import ACF fields
 *
 * @return void
 * @since 0.1.0
 */
function ninety_init_acf_import() {

	if ( function_exists( 'acf_add_local_field_group' ) ):

		acf_add_local_field_group(
			[
				'key'                   => 'group_5d197b91b0564',
				'title'                 => '90/90 Location',
				'fields'                => [
					[
						'key'               => 'field_5d197b9c32cec',
						'label'             => 'Address',
						'name'              => 'ninety_location_address',
						'type'              => 'text',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => [
							'width' => '',
							'class' => '',
							'id'    => '',
						],
						'default_value'     => '',
						'placeholder'       => '',
						'prepend'           => '',
						'append'            => '',
						'maxlength'         => '',
					],
					[
						'key'               => 'field_5d197bc132ced',
						'label'             => 'Map Coordinates',
						'name'              => 'ninety_location_coords',
						'type'              => 'text',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => [
							'width' => '',
							'class' => '',
							'id'    => '',
						],
						'default_value'     => '',
						'placeholder'       => '',
						'prepend'           => '',
						'append'            => '',
						'maxlength'         => '',
						'disabled'          => '1',
					],
				],
				'location'              => [
					[
						[
							'param'    => 'taxonomy',
							'operator' => '==',
							'value'    => 'ninety_meeting_location',
						],
					],
				],
				'menu_order'            => 0,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen'        => '',
				'active'                => true,
				'description'           => '',
			]
		);

		acf_add_local_field_group(
			[
				'key'                   => 'group_5d1825320bff3',
				'title'                 => '90/90 Meeting Details',
				'fields'                => [
					[
						'key'                => 'field_5d18480b686a6',
						'label'              => 'Meeting Location',
						'name'               => 'ninety_meeting_location',
						'type'               => 'taxonomy',
						'instructions'       => '',
						'required'           => 0,
						'conditional_logic'  => 0,
						'wrapper'            => [
							'width' => '',
							'class' => '',
							'id'    => '',
						],
						'taxonomy'           => 'ninety_meeting_location',
						'field_type'         => 'select',
						'allow_null'         => 0,
						'add_term'           => 0,
						'save_terms'         => 1,
						'load_terms'         => 0,
						'return_format'      => 'object',
						'show_column'        => 0,
						'show_column_weight' => 1000,
						'allow_quickedit'    => 1,
						'allow_bulkedit'     => 1,
						'multiple'           => 0,
						'default_value'      => '185',
					],
					[
						'key'               => 'field_5d182c40c6e57',
						'label'             => 'Meeting Time',
						'name'              => 'ninety_meeting_time',
						'type'              => 'date_time_picker',
						'instructions'      => '',
						'required'          => 1,
						'conditional_logic' => 0,
						'wrapper'           => [
							'width' => '',
							'class' => '',
							'id'    => '',
						],
						'display_format'    => 'F j, Y g:i a',
						'return_format'     => 'F j, Y g:i a',
						'first_day'         => 0,
						'default_value'     => '14-09-2019 12:00:00',
					],
					[
						'key'               => 'field_5d18255d071c8',
						'label'             => 'Meeting Type',
						'name'              => 'ninety_meeting_type',
						'type'              => 'taxonomy',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => [
							'width' => '',
							'class' => '',
							'id'    => '',
						],
						'taxonomy'          => 'ninety_meeting_type',
						'field_type'        => 'select',
						'allow_null'        => 0,
						'add_term'          => 0,
						'save_terms'        => 1,
						'load_terms'        => 0,
						'return_format'     => 'object',
						'multiple'          => 0,
						'default_value'     => '179',
					],
					[
						'key'               => 'field_5d795146b6b29',
						'label'             => 'Secretary',
						'name'              => 'ninety_meeting_secretary',
						'type'              => 'text',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => [
							'width' => '',
							'class' => '',
							'id'    => '',
						],
						'default_value'     => '',
						'placeholder'       => '',
						'prepend'           => '',
						'append'            => '',
						'maxlength'         => '',
					],
					[
						'key'               => 'field_5d184939bb3dd',
						'label'             => 'Chair',
						'name'              => 'ninety_meeting_speaker',
						'type'              => 'text',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => [
							'width' => '',
							'class' => '',
							'id'    => '',
						],
						'default_value'     => '',
						'placeholder'       => '',
						'prepend'           => '',
						'append'            => '',
						'maxlength'         => '',
					],
					[
						'key'               => 'field_5d18494cbb3de',
						'label'             => 'Topic',
						'name'              => 'ninety_meeting_topic',
						'type'              => 'text',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => [
							[
								[
									'field'    => 'field_5d184939bb3dd',
									'operator' => '!=empty',
								],
							],
						],
						'wrapper'           => [
							'width' => '',
							'class' => '',
							'id'    => '',
						],
						'default_value'     => '',
						'placeholder'       => '',
						'prepend'           => '',
						'append'            => '',
						'maxlength'         => '',
					],
					[
						'key'               => 'field_5d7da04260841',
						'label'             => 'I Shared',
						'name'              => 'ninety_meeting_shared',
						'type'              => 'true_false',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => [
							'width' => '',
							'class' => '',
							'id'    => '',
						],
						'message'           => '',
						'default_value'     => 0,
						'ui'                => 0,
						'ui_on_text'        => '',
						'ui_off_text'       => '',
					],
					[
						'key'               => 'field_5d18490cbb3dc',
						'label'             => 'Notes',
						'name'              => 'ninety_meeting_notes',
						'type'              => 'wysiwyg',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => [
							'width' => '',
							'class' => '',
							'id'    => '',
						],
						'default_value'     => '',
						'tabs'              => 'all',
						'toolbar'           => 'full',
						'media_upload'      => 1,
						'delay'             => 0,
					],
					[
						'key'               => 'field_5d184af7fa43d',
						'label'             => 'Private Notes',
						'name'              => 'ninety_meeting_private_notes',
						'type'              => 'wysiwyg',
						'instructions'      => '',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => [
							'width' => '',
							'class' => '',
							'id'    => '',
						],
						'default_value'     => '',
						'tabs'              => 'all',
						'toolbar'           => 'full',
						'media_upload'      => 1,
						'delay'             => 0,
					],
					[
						'key'               => 'field_5d184b55fa43e',
						'label'             => 'Program',
						'name'              => 'ninety_meeting_program',
						'type'              => 'button_group',
						'instructions'      => '',
						'required'          => 1,
						'conditional_logic' => 0,
						'wrapper'           => [
							'width' => '',
							'class' => '',
							'id'    => '',
						],
						'choices'           => [
							'aa' => 'AA',
							'na' => 'NA',
						],
						'allow_null'        => 0,
						'default_value'     => 'aa',
						'layout'            => 'horizontal',
						'return_format'     => 'label',
					],
				],
				'location'              => [
					[
						[
							'param'    => 'post_type',
							'operator' => '==',
							'value'    => 'ninety_meeting',
						],
					],
				],
				'menu_order'            => 0,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen'        => '',
				'active'                => true,
				'description'           => '',
			]
		);

	endif;

}