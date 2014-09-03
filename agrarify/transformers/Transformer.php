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
    protected $singular_name = 'item';

    /**
     * The name used for a collection of model records. Child classes must override.
     *
     * @var string
     */
    protected $plural_name = 'items';

    /**
     * Transforms a collection of model records.
     *
     * @param array|\Traversable $items
     * @param array $options
     * @return array
     */
    public function transformCollection($items, $options = [])
    {
        $transformed = [];
        foreach ($items as $item)
        {
            $transformed[] = $this->transform($item, $options);
        }

        return $transformed;
    }

    /**
     * Transforms a single model record.
     *
     * @param \Agrarify\Models\BaseModel $item
     * @param array $options
     * @return array
     */
    public abstract function transform($item, $options = []);

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
     * Returns the value of the option parameter if it is found within the options array.
     *
     * @param array $options
     * @param string $option_name
     * @return mixed
     */
    protected function getOption($options, $option_name)
    {
        if (isset($options[$option_name]))
        {
            return $options[$option_name];
        }
        return null;
    }
}
