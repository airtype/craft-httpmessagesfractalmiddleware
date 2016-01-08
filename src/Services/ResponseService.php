<?php

namespace HttpMessagesFractalMiddleware\Services;

use HttpMessages\Http\CraftResponse as Response;
use HttpMessagesFractalMiddleware\Http\FractalResponse;

class ResponseService
{
    /**
     * Get Response
     *
     * @return object Response
     */
    public function getResponse(Response $response)
    {
        $response = new FractalResponse($response);

        return $response;
    }

}
