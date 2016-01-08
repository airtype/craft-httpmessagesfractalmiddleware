<?php

namespace HttpMessagesFractalMiddleware\Http;

use HttpMessages\Http\CraftResponse as Response;

class FractalResponse extends Response
{
    /**
     * Item
     *
     * @var array
     */
    protected $item;

    /**
     * Collection
     *
     * @var array
     */
    protected $collection;

    /**
     * Constructor
     *
     * @param Response $response Response
     */
    public function __construct(Response $response)
    {
        foreach (get_object_vars($response) as $property => $value) {
            $this->$property = $value;
        }
    }

    /**
     * Get Collection
     *
     * @return array Collection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * With Collection
     *
     * @param array $collection Collection
     *
     * @return RestResponse RestResponse
     */
    public function withCollection(array $collection)
    {
        $new = clone $this;

        $new->collection = $collection;

        return $new;
    }

    /**
     * Get Item
     *
     * @return Item Item
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * With Item
     *
     * @param mixed $item Item
     *
     * @return RestResponse RestResponse
     */
    public function withItem($item)
    {
        $new = clone $this;

        $new->item = $item;

        return $new;
    }

}
