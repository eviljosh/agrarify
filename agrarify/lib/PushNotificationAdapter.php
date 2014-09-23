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
            'CustomUserData' => Config::get('agrarify.app_name') . ' ' . $push_registration->getAccount()->getId() . ' ' . $push_registration->getAccount()->getEmailAddress(),
            'Enabled' => 'true',
            'Attributes' => [
                'Enabled' => 'true',
            ],
        ]);

        return $result['EndpointArn'];
    }

    /**
     * @param \Agrarify\Models\Accounts\PushRegistration $push_registration
     */
    public static function deleteDeviceEndpoint($push_registration)
    {
        $sns_client = self::getSnsClient();

        $result = $sns_client->deleteEndpoint([
            'EndpointArn' => $push_registration->getSnsArn()
        ]);
    }

    /**
     * @param \Agrarify\Models\Accounts\PushRegistration $push_registration
     */
    public static function enableDeviceEndpoint($push_registration)
    {
        $sns_client = self::getSnsClient();

        $result = $sns_client->setEndpointAttributes([
            'EndpointArn' => $push_registration->getSnsArn(),
            'Attributes' => [
                'Enabled' => 'true',
            ],
        ]);
    }

    /**
     * @param \Agrarify\Models\Accounts\PushRegistration $push_registration
     * @param string $message
     */
    public static function sendMessage($push_registration, $message, $attempt = 0)
    {
        $sns_client = self::getSnsClient();

        try {
            $sns_client->publish([
                'Message' => $message,
                'TargetArn' => $push_registration->getSnsArn(),
            ]);
        }
        catch (\Aws\Sns\Exception\EndpointDisabledException $e) {
            $push_registration->setEnabled(false);
            $push_registration->save();
        }
        catch (\Aws\Sns\Exception\InvalidParameterException $e) {
            if (($attempt < 3) and (strpos($e->getMessage(), 'No endpoint found for the target arn specified') !== false)) {
                $arn = self::registerDevice($push_registration);
                $push_registration->setEnabled(true);
                $push_registration->setSnsArn($arn);
                $push_registration->save();

                self::sendMessage($push_registration, $message, $attempt + 1);
            }
            else {
                throw $e;
            }
        }
    }

}