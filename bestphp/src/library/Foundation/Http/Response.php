<?php


namespace Best\Foundation\Http;


class Response extends Message
{
    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var string
     */
    private $reasonPhrase;
    
    /**
     * Gets the response status code.
     *
     * The status code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     *
     * @return int Status code.
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Return an instance with the specified status code and, optionally, reason phrase.
     *
     * If no reason phrase is specified, implementations MAY choose to default
     * to the RFC 7231 or IANA recommended reason phrase for the response's
     * status code.
     * 
     * @param int $code  The 3-digit integer result code to set.
     * @param string $reasonPhrase  The reason phrase to use with the
     *     provided status code; if none is provided, implementations MAY
     *     use the defaults as suggested in the HTTP specification.
     * @return $this|Respond
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        $this->statusCode = $code;
        if ('' === trim($reasonPhrase)) {
            $reasonPhrase = HttpStatusCode::$reasonPhrase[$code] ?? '';
        }
        $this->reasonPhrase = $reasonPhrase;
        
        return $this;
    }

    /**
     * Gets the response reason phrase associated with the status code.
     *
     * @return string Reason phrase;
     */
    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }
}