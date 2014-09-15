<?php

namespace Agrarify\Models\Veggies;

use Agrarify\Lib\ImageStorageAdapter;
use Agrarify\Models\BaseModel;

class VeggieImage extends BaseModel {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'veggie_images';

    /**
     * Validation rules for the model
     *
     * @var array
     */
    public static $rules = [
        'veggie_id'  => 'required|numeric',
        'is_primary' => 'required|boolean',
        'guid'       => 'required',
    ];

    /**
     * Indicates which fields can be mass assigned
     *
     * @var array
     */
    protected $fillable = [
        'is_primary',
    ];

    /**
     * Create a new VeggieImage model instance.
     *
     * @param array $attributes
     */
    function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
        $this->guid = str_random(40);
    }

    /**
     * Defines the many-to-one relationship with veggies
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function veggie()
    {
        return $this->belongsTo('Agrarify\Models\Veggies\Veggie');
    }

    /**
     * @return int Id
     */
    public function getId()
    {
        return $this->getParamOrDefault('id');
    }

    /**
     * @return \Carbon\Carbon Created at date
     */
    public function getCreatedAt()
    {
        return $this->getParamOrDefault('created_at');
    }

    /**
     * @return \Agrarify\Models\Veggies\Veggie
     */
    public function getVeggie()
    {
        return $this->veggie;
    }

    /**
     * @param \Agrarify\Models\Veggies\Veggie $veggie
     */
    public function setVeggie($veggie)
    {
        $this->veggie_id = $veggie->getId();
    }

    /**
     * @return boolean
     */
    public function isPrimary()
    {
        return (boolean) $this->getParamOrDefault('is_primary', false);
    }

    /**
     * @return string
     */
    public function getGuid()
    {
        return $this->getParamOrDefault('guid');
    }

    /**
     * @return string
     */
    public function getDownloadUrl()
    {
        return ImageStorageAdapter::getUrlForVeggieImage($this->getGuid());
    }

    /**
     * Takes a file object just uploaded in an API call and stores it permanently in S3.
     *
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     */
    public function setImageFile($file)
    {
        ImageStorageAdapter::storeVeggieImage($this->getGuid(), $file);
    }

}