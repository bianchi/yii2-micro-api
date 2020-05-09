<?php

namespace api\models\exceptions;

use yii\web\HttpException;

/**
 * BadRequestHttpException represents a "Bad Gateway" HTTP exception with status code 502.
 *
 * The 502 (Bad Gateway) status code indicates that the server, while
 * acting as a gateway or proxy, received an invalid response from an
 * inbound server it accessed while attempting to fulfill the request.
 *
 * @see https://tools.ietf.org/html/rfc7231#section-6.6.3
 */
class BadGatewayHttpException extends HttpException
{
    /**
     * Constructor.
     * @param string $message error message
     * @param int $code error code
     * @param \Exception $previous The previous exception used for the exception chaining.
     */
    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct(502, $message, $code, $previous);
    }
}