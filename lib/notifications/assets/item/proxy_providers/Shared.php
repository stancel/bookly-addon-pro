<?php
namespace BooklyPro\Lib\Notifications\Assets\Item\ProxyProviders;

use Bookly\Lib as BooklyLib;
use Bookly\Lib\Entities\CustomerAppointment;
use Bookly\Lib\Notifications\Assets\Item\Codes;
use Bookly\Lib\Notifications\Assets\Item\Proxy;

/**
 * Class Shared
 * @package BooklyPro\Lib\Notifications\Assets\Item\ProxyProviders
 */
abstract class Shared extends Proxy\Shared
{
    /**
     * @inheritdoc
     */
    public static function prepareCodes( Codes $codes )
    {
        $item = $codes->getItem();

        $codes->appointment_online_meeting_url = BooklyLib\Proxy\Shared::buildOnlineMeetingUrl( '', $item->getAppointment() );
        $codes->status = $item->getCA()->getStatus();
    }

    /**
     * @inheritdoc
     */
    public static function prepareReplaceCodes( array $replace_codes, Codes $codes, $format )
    {
        $replace_codes['{online_meeting_url}'] = $codes->appointment_online_meeting_url;
        $replace_codes['{status}'] = CustomerAppointment::statusToString( $codes->status );

        return $replace_codes;
    }
}