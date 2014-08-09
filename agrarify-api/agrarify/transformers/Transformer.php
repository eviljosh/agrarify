<?php

namespace Agrarify\Transformers;

use Illuminate\Database\Eloquent\Collection;

/**
 * This is the abstract transformer class.  Transformers take an array representing an Agrarify model (or set of models)
 * and transform them into an array that is safe for public consumption.  Transformers also transform (but do not
 * validate) input intended to create or update an Agrarify model.
 */
abstract class AgrarifyTransformer
{
    /**
     * The name used for a single model record.
     *
     * @var string
     */
    var $singular_name = 'item';

    /**
     * The name used for a collection of model records. Child classes must override.
     *
     * @var string
     */
    var $plural_name = '';

    /**
     * Transforms a collection of model records.
     *
     * @param array $items
     * @return array
     */
    public function transformCollection($items)
    {
        return array_map([$this, 'transform'], $items);
    }

    /**
     * Transforms a single model record.
     *
     * @param array $item
     * @return array
     */
    public abstract function transform($item);

    /**
     * Transforms input intended to create or modify a model record.
     *
     * @param array $input
     * @return array
     */
    public function transformInput($input)
    {
        // default to passing all input through unchanged
        return $input;
    }

    /**
     * Returns the name used for a single model record of this type.
     *
     * @return string
     */
    public function getSingularName()
    {
        return $this->singular_name;
    }

    /**
     * Returns the name used for a collection of model records of this type.
     *
     * @return string
     */
    public function getPluralName()
    {
        return $this->plural_name;
    }

    /**
     * Helper method to get an array value for a specified key, or return a default value if that key isn't present.
     *
     * @param array $array
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function getArrayValueOrDefault($array, $key, $default = '')
    {
        if (isset($array[$key]))
        {
            return $array[$key];
        }
        return $default;
    }
}
