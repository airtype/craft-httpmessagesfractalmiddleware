<?php

namespace HttpMessagesFractalMiddleware\Transformers;

class ArrayTransformer extends BaseTransformer
{
    public function transform(array $array)
    {
        return $array;
    }
}
