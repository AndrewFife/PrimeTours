<?php
/**
 * Prime Tours: ACF field group provisioning for the `experience` post type.
 *
 * One-time bootstrap. Run via WP-CLI:
 *   ddev wp eval-file scripts/seed-acf-fields.php
 *
 * This calls the same ACF save path the admin UI uses, so it triggers the
 * acf/settings/save_json filter in primetours-core.php and writes the field
 * group straight to wp-content/acf-json/, which is the tracked, committed
 * source of truth (see build.md §2, §5). Once that JSON file exists and is
 * committed, a fresh clone/environment picks the field group up automatically
 * via ACF's local JSON load path; nothing needs to re-run this script. Kept
 * here for documentation and disaster recovery only.
 *
 * Field list: build.md §5.
 *
 * @package PrimeTours
 */

if ( ! function_exists( 'acf_import_field_group' ) ) {
	WP_CLI::error( 'ACF is not active. Activate advanced-custom-fields first.' );
}

$months = array();
foreach ( array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' ) as $m ) {
	$months[ strtolower( substr( $m, 0, 3 ) ) ] = $m;
}

$field_group = array(
	'key'                   => 'group_pt_experience_details',
	'title'                 => 'Experience Details',
	'fields'                => array(
		array(
			'key'           => 'field_pt_price_from_zar',
			'label'         => 'Price From (ZAR)',
			'name'          => 'price_from_zar',
			'type'          => 'number',
			'instructions'  => 'Lowest genuinely available per-person price in South African Rand, before optional extras.',
			'required'      => 1,
			'min'           => 0,
			'step'          => 1,
			'prepend'       => 'R',
		),
		array(
			'key'           => 'field_pt_duration_hours',
			'label'         => 'Duration (hours)',
			'name'          => 'duration_hours',
			'type'          => 'number',
			'instructions'  => "Real door-to-door duration, e.g. 9.5, not the marketing \"full day\" claim.",
			'required'      => 1,
			'min'           => 0,
			'step'          => 0.5,
			'append'        => 'hrs',
		),
		array(
			'key'           => 'field_pt_departure_point',
			'label'         => 'Departure Point',
			'name'          => 'departure_point',
			'type'          => 'text',
			'instructions'  => "The actual pickup point, e.g. \"V&A Waterfront, Cape Town\", not just the city.",
			'required'      => 1,
		),
		array(
			'key'           => 'field_pt_best_months',
			'label'         => 'Best Months to Go',
			'name'          => 'best_months',
			'type'          => 'checkbox',
			'instructions'  => 'Months this is genuinely worth doing. Explain why in the verdict, not here.',
			'required'      => 0,
			'choices'       => $months,
			'layout'        => 'horizontal',
		),
		array(
			'key'           => 'field_pt_gyg_affiliate_link',
			'label'         => 'GetYourGuide Booking Link',
			'name'          => 'gyg_affiliate_link',
			'type'          => 'url',
			'instructions'  => 'Cloaked ThirstyAffiliates URL only (/go/...). Never paste a raw getyourguide.com link, see CLAUDE.md. Leave blank if Viator is the better listing for this experience: commission rate is not the deciding factor, see affiliates.md.',
			'required'      => 0,
		),
		array(
			'key'           => 'field_pt_viator_affiliate_link',
			'label'         => 'Viator Booking Link',
			'name'          => 'viator_affiliate_link',
			'type'          => 'url',
			'instructions'  => 'Cloaked ThirstyAffiliates URL only (/go/...). Leave blank if GetYourGuide is the better listing for this experience. At least one of the two link fields must be filled in, or the booking module renders nothing.',
			'required'      => 0,
		),
		array(
			'key'           => 'field_pt_cancellation_terms',
			'label'         => 'Cancellation Terms',
			'name'          => 'cancellation_terms',
			'type'          => 'text',
			'instructions'  => 'e.g. "Free cancellation up to 24 hours before." Shown in the booking module, identity.md §4c.',
			'required'      => 1,
		),
		array(
			'key'           => 'field_pt_includes',
			'label'         => 'Includes',
			'name'          => 'includes',
			'type'          => 'textarea',
			'instructions'  => 'One item per line.',
			'required'      => 0,
			'new_lines'     => 'br',
			'rows'          => 4,
		),
		array(
			'key'           => 'field_pt_excludes',
			'label'         => 'Excludes',
			'name'          => 'excludes',
			'type'          => 'textarea',
			'instructions'  => 'One item per line.',
			'required'      => 0,
			'new_lines'     => 'br',
			'rows'          => 4,
		),
		array(
			'key'           => 'field_pt_physical_demand',
			'label'         => 'Physical Demand',
			'name'          => 'physical_demand',
			'type'          => 'select',
			'instructions'  => 'Honest, not aspirational.',
			'required'      => 1,
			'choices'       => array(
				'easy'      => 'Easy',
				'moderate'  => 'Moderate',
				'strenuous' => 'Strenuous',
			),
			'ui'            => 1,
		),
		array(
			'key'           => 'field_pt_verdict_short',
			'label'         => 'Verdict (short)',
			'name'          => 'verdict_short',
			'type'          => 'textarea',
			'instructions'  => 'One or two sentences: worth it, not worth it, or depends, and why. Powers the verdict block.',
			'required'      => 1,
			'rows'          => 3,
		),
		array(
			'key'           => 'field_pt_worth_it',
			'label'         => 'Worth It?',
			'name'          => 'worth_it',
			'type'          => 'select',
			'instructions'  => 'The headline call shown in the verdict block.',
			'required'      => 1,
			'choices'       => array(
				'yes'     => 'Yes',
				'no'      => 'No',
				'depends' => 'Depends',
			),
			'ui'            => 1,
		),
		array(
			'key'           => 'field_pt_last_verified_date',
			'label'         => 'Last Verified Date',
			'name'          => 'last_verified_date',
			'type'          => 'date_picker',
			'instructions'  => 'Date prices, hours and cancellation terms were last checked. Drives the last-verified stamp, build.md §5.',
			'required'      => 1,
			'display_format' => 'j F Y',
			'return_format'  => 'Y-m-d',
			'first_day'      => 1,
		),
	),
	'location'              => array(
		array(
			array(
				'param'    => 'post_type',
				'operator' => '==',
				'value'    => 'experience',
			),
		),
	),
	'menu_order'            => 0,
	'position'              => 'normal',
	'style'                 => 'default',
	'label_placement'       => 'top',
	'instruction_placement' => 'label',
	'active'                => true,
);

acf_import_field_group( $field_group );

WP_CLI::success( 'Imported field group: ' . $field_group['title'] );
