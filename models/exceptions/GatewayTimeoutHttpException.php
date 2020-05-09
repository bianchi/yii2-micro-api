<?php

namespace api\models\exceptions;

use yii\web\HttpException;

/**
 * GatewayTimeout represents a "Gateway timeout" HTTP exception with status code 504.
 *
 * The 504 (Gateway timeout)) status code indicates that the server, 
 * while acting as a gateway or proxy, did not receive a timely response
 * from an upstream server it needed to access in order to complete the
 * request.
 *
 * @see https://tools.ietf.org/html/rfc7231#section-6.6.5
 */
class GatewayTimeoutHttpException extends HttpException
{
    /**
     * Constructor.
     * @param string $message error message
     * @param int $code error code
     * @param \Exception $previous The previous exception used for the exception chaining.
     */
    public function __construct($message = null, $code = 0, \Exception $previous = null)
    {
        parent::__construct(504, $message, $code, $previous);
    }
}