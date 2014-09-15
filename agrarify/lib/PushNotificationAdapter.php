<?php

namespace Agrarify\Lib;

use Aws\Sns\SnsClient;
use Illuminate\Support\Facades\Config;
use Agrarify\Models\Accounts\PushRegistration;

class PushNotificationAdapter {

    private static function getSnsClient()
    {
        return SnsClient::factory([
            'key'    => Config::get('agrarify.sns_key'),
            'secret' => Config::get('agrarify.sns_secret'),
            'region' => Config::get('agrarify.sns_region'),
        ]);
    }

    private static function getApplicationArnForType($type)
    {
        if ($type == PushRegistration::TYPE_ANDROID_PHONE_APP)
        {
            return Config::get('agrarify.sns_android_phone_arn');
        }
        else
        {
            return '';
        }
    }

    /**
     * @param \Agrarify\Models\Accounts\PushRegistration $push_registration
     *
     * @return string the AWS SNS endpoint ARN for this device
     */
    public static function registerDevice($push_registration)
    {
        $sns_client = self::getSnsClient();

        $result = $sns_client->createPlatformEndpoint([
            'PlatformApplicationArn' => self::getApplicationArnForType($push_registration->getType()),
            'Token' => $push_registration->getToken(),
            'CustomUserData' => Config::get('agrarify.app_name') . ' ' . $push_registration->getAccount()->getId(),
            'Enabled' => 'true',
            'Attributes' => [
                'Enabled' => 'true',
            ],
        ]);

        return $result['EndpointArn'];
    }

    /**
     * @param \Agrarify\Models\Accounts\PushRegistration $push_registration
     * @param string $message
     */
    public static function sendMessage($push_registration, $message)
    {
        $sns_client = self::getSnsClient();

        $sns_client->publish([
            'Message' => Config::get('agrarify.app_name') . ': ' . $message,
            'TargetArn' => $push_registration->getSnsArn(),
        ]);
    }

}