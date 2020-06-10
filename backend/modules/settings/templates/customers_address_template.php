<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
use Bookly\Backend\Components\Settings\Inputs;

Inputs::renderTextArea( 'bookly_l10n_cst_address_template', __( 'Customer address', 'bookly' ), __( 'Configure how the customer\'s address will be displayed in notifications.', 'bookly' ), 3 );

$codes = array(
    array( 'code' => 'country',            'description' => strtolower( get_option( 'bookly_l10n_label_country' ) ) ),
    array( 'code' => 'state',              'description' => strtolower( get_option( 'bookly_l10n_label_state' ) ) ),
    array( 'code' => 'postcode',           'description' => strtolower( get_option( 'bookly_l10n_label_postcode' ) ) ),
    array( 'code' => 'city',               'description' => strtolower( get_option( 'bookly_l10n_label_city' ) ) ),
    array( 'code' => 'street',             'description' => strtolower( get_option( 'bookly_l10n_label_street' ) ) ),
    array( 'code' => 'street_number',      'description' => strtolower( get_option( 'bookly_l10n_label_street_number' ) ) ),
    array( 'code' => 'additional_address', 'description' => strtolower( get_option( 'bookly_l10n_label_additional_address' ) ) ),
);

echo Bookly\Lib\Utils\Common::codes( $codes );
?>
<br>