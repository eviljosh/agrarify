<?php

namespace Agrarify\Models\Accounts;

use Agrarify\Lib\PushNotificationAdapter;
use Agrarify\Models\BaseModel;

class PushRegistration extends BaseModel {

    const TYPE_ANDROID_PHONE_APP = 'A';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'push_registrations';

    /**
     * Validation rules for the model
     *
     * @var array
     */
    public static $rules = [
        'token'       => 'required|max:255',
        'type'        => 'required|max:1',
        'device_name' => 'max:255',
    ];

    /**
     * Indicates which fields can be mass assigned
     *
     * @var array
     */
    protected $fillable = [
        'token',
        'type',
        'device_name',
    ];

    /**
     * Create a new PushRegistration model instance.
     *
     * @param array $attributes
     */
    function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
        $this->enabled = true;
    }

    /**
     * Overrides the base delete in order to delete the record on the AWS SNS side
     *
     * @return bool|null
     * @throws \Exception
     */
    public function delete()
    {
        PushNotificationAdapter::deleteDeviceEndpoint($this);
        return parent::delete();
    }

    /**
     * Defines the one-to-one relationship with accounts
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account()
    {
        return $this->belongsTo('Agrarify\Models\Accounts\Account');
    }

    /**
     * @return \Agrarify\Models\Accounts\Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param \Agrarify\Models\Accounts\Account $account
     */
    public function setAccount($account)
    {
        $this->account_id = $account->getId();
    }

    /**
     * @return mixed Database id for the record
     */
    public function getId()
    {
        return $this->getParamOrDefault('id');
    }

    /**
     * @return string The push token
     */
    public function getToken()
    {
        return $this->getParamOrDefault('token');
    }

    /**
     * @return int Type code
     */
    public function getType()
    {
        return $this->getParamOrDefault('type');
    }

    /**
     * @return string The device name
     */
    public function getDeviceName()
    {
        return $this->getParamOrDefault('device_name');
    }

    /**
     * @return string The AWS SNS ARN for this push registration
     */
    public function getSnsArn()
    {
        return $this->getParamOrDefault('sns_arn');
    }

    /**
     * @param string $arn The AWS SNS ARN
     */
    public function setSnsArn($arn)
    {
        $this->sns_arn = $arn;
    }

    /**
     * @return bool Indication of whether endpoint is enabled or not
     */
    public function isEnabled()
    {
        return (bool) $this->getParamOrDefault('enabled');
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * Send a push notification message to this registered device
     *
     * @param string $message
     */
    public function sendMessage($message)
    {
        if ($this->isEnabled())
        {
            PushNotificationAdapter::sendMessage($this, $message);
        }
    }

    /**
     * Send a complex push notification message to this registered device
     *
     * @param string $title
     * @param string $message
     */
    public function sendFormattedMessage($title, $message)
    {
        if ($this->isEnabled())
        {
            $message = [
                'GCM' => [
                    'data' => json_encode([
                        'title' => $title,
                        'message' => $message,
                    ])
                ]
            ];

            PushNotificationAdapter::sendMessage($this, $message);
        }
    }

}