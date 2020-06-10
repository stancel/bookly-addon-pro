<?php
namespace BooklyPro\Backend\Components\Appearance\ProxyProviders;

use Bookly\Backend\Components\Appearance\Proxy;

/**
 * Class Shared
 * @package BooklyPro\Backend\Modules\Appearance\ProxyProviders
 */
class Shared extends Proxy\Shared
{
    /**
     * @inheritdoc
     */
    public static function prepareCodes( array $codes )
    {
        return array_merge( $codes, array(
            array( 'code' => 'online_meeting_url', 'description' => __( 'online meeting URL', 'bookly' ), 'flags' => array( 'step' => 8, 'extra_codes' => true ) ),
        ) );
    }

}