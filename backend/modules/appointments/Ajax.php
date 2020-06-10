<?php
namespace BooklyPro\Backend\Modules\Appointments;

use Bookly\Backend\Modules\Appointments\Proxy;
use Bookly\Lib as BooklyLib;
use Bookly\Lib\Utils\Common;
use BooklyPro\Lib;

/**
 * Class Ajax
 * @package BooklyPro\Backend\Modules\Appointments
 */
class Ajax extends \Bookly\Backend\Modules\Appointments\Ajax
{
    /**
     * @inheritdoc
     */
    protected static function permissions()
    {
        return array( '_default' => 'user' );
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
                    case 'exportAppointments':
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

    /**
     * Export Appointments to CSV
     */
    public static function exportAppointments()
    {
        $delimiter = self::parameter( 'delimiter', ',' );

        header( 'Content-Type: text/csv; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename=Appointments.csv' );

        $columns = BooklyLib\Utils\Tables::getColumns( BooklyLib\Utils\Tables::APPOINTMENTS );

        $header = array();
        $column = array();

        foreach ( self::parameter( 'exp', array() ) as $key => $value ) {
            $header[] = $columns[ $key ];
            $column[] = $key === 'payment' ? 'payment_raw_title' : $key;
        }

        $output = fopen( 'php://output', 'w' );
        fwrite( $output, pack( 'CCC', 0xef, 0xbb, 0xbf ) );
        fputcsv( $output, $header, $delimiter );
        $filter = json_decode( self::parameter( 'filter', array() ), true );
        $data   = self::getAppointmentsTableData( $filter );

        foreach ( $data['data'] as $row ) {
            $row_data = array_fill( 0, count( $column ), '' );
            foreach ( $row as $key => $value ) {
                if ( $key == 'custom_fields' ) {
                    foreach ( $value as $id => $field ) {
                        $pos = array_search( 'custom_fields_' . $id, $column );
                        if ( $pos !== false ) {
                            $row_data[ $pos ] = $field;
                        }
                    }
                } else {
                    $pos = array_search( $key, $column );
                    if ( $pos !== false ) {
                        $row_data[ $pos ] = $value;
                    } elseif ( is_array( $value ) ) {
                        foreach ( $value as $sub_key => $sub_value ) {
                            $pos = array_search( $key . '_' . $sub_key, $column );
                            if ( $pos !== false ) {
                                if ( $key . '_' . $sub_key === 'service_title' && count( $value['extras'] ) > 0 ) {
                                    $sub_value .= ' (' . implode( "\t", array_column( $value['extras'], 'title' ) ) . ')';
                                }
                                $row_data[ $pos ] = $sub_value;
                            }
                        }
                    }
                }
            }
            fputcsv( $output, $row_data, $delimiter );
        }

        fclose( $output );

        exit;
    }
}