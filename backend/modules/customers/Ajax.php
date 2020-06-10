<?php
namespace BooklyPro\Backend\Modules\Customers;

use Bookly\Backend\Modules\Customers\Proxy;
use Bookly\Lib as BooklyLib;
use Bookly\Lib\Utils\Common;
use BooklyPro\Lib;

/**
 * Class Ajax
 * @package BooklyPro\Backend\Modules\Customers
 */
class Ajax extends BooklyLib\Base\Ajax
{
    /**
     * @inheritdoc
     */
    protected static function permissions()
    {
        return array( '_default' => 'user' );
    }

    /**
     * Export Customers to CSV
     */
    public static function exportCustomers()
    {
        global $wpdb;
        $delimiter = self::parameter( 'export_customers_delimiter', ',' );

        header( 'Content-Type: text/csv; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename=Customers.csv' );

        $columns = BooklyLib\Utils\Tables::getColumns( BooklyLib\Utils\Tables::CUSTOMERS );

        $header = array();
        $column = array();

        foreach ( self::parameter( 'exp', array() ) as $key => $value ) {
            $header[] = $columns[ $key ];
            $column[] = $key;
        }

        $output = fopen( 'php://output', 'w' );
        fwrite( $output, pack( 'CCC', 0xef, 0xbb, 0xbf ) );
        fputcsv( $output, $header, $delimiter );

        $select = 'c.*, MAX(a.start_date) AS last_appointment,
                COUNT(a.id) AS total_appointments,
                COALESCE(SUM(p.total),0) AS payments,
                wpu.display_name AS wp_user';
        $select = Proxy\CustomerGroups::prepareCustomerSelect( $select );

        $query = BooklyLib\Entities\Customer::query( 'c' )
            ->select( $select )
            ->leftJoin( 'CustomerAppointment', 'ca', 'ca.customer_id = c.id' )
            ->leftJoin( 'Appointment', 'a', 'a.id = ca.appointment_id' )
            ->leftJoin( 'Payment', 'p', 'p.id = ca.payment_id' )
            ->tableJoin( $wpdb->users, 'wpu', 'wpu.ID = c.wp_user_id' )
            ->groupBy( 'c.id' );

        $query = Proxy\CustomerGroups::prepareCustomerQuery( $query );

        $rows = $query->fetchArray();

        foreach ( $rows as $row ) {
            $row_data = array_fill( 0, count( $column ), '' );
            foreach ( $row as $key => $value ) {
                if ( $key == 'info_fields' ) {
                    foreach ( json_decode( $value ) as $field ) {
                        $pos = array_search( 'info_fields_' . $field->id, $column );
                        if ( $pos !== false ) {
                            $row_data[ $pos ] = is_array( $field->value ) ? implode( ', ', $field->value ) : $field->value;
                        }
                    }
                } else {
                    $pos = array_search( $key, $column );
                    if ( $pos !== false ) {
                        $row_data[ $pos ] = $value;
                    }
                }
            }

            $pos = array_search( 'address', $column );
            if ( $pos !== false ) {
                $full_address = Lib\Utils\Common::getFullAddressByCustomerData( $row );
                $row_data[ $pos ] = $full_address;
            }

            fputcsv( $output, $row_data, $delimiter );
        }

        fclose( $output );

        exit;
    }

    /**
     * Check if the current user has access to the action.
     *
     * @param string $action
     * @return bool
     */
    protected static function hasAccess( $action )
    {
        if ( parent::hasAccess( $action ) ) {
            if ( ! Common::isCurrentUserSupervisor() ) {
                switch ( $action ) {
                    case 'exportCustomers':
                        return BooklyLib\Entities\Staff::query()
                            ->where( 'wp_user_id', get_current_user_id() )
                            ->count() > 0;
                }
            } else {
                return true;
            }
        }

        return false;
    }
}