<?php
namespace BooklyPro\Backend\Modules\Notifications\ProxyProviders;

use Bookly\Backend\Modules\Notifications\Proxy;

/**
 * Class Shared
 * @package BooklyPro\Backend\Modules\Notifications\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritdoc
     */
    public static function prepareNotificationCodes( array $codes, $type )
    {
        $codes['appointment']['online_meeting_url'] = __( 'online meeting URL', 'bookly' );

        return $codes;
    }
}