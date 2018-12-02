<?php


namespace Best;

use Best\Foundation\Http\Response as HttpResponse;

class Response extends HttpResponse
{
    /**
     * Response constructor.
     *
     * @param $body \Best\Foundation\Http\Stream|string
     * @param $headers array
     * @param $statusCode int
     */
    public function __construct($body, int $statusCode = 200, array $headers = ['Content-Type' => 'text/html;charset=utf-8'])
    {
        //Set http response status code
        $this->withStatus($statusCode);
        //Set http message headers
        foreach ($headers as $name => $header) {
            $this->withAddedHeader($name, $header);
        }
        //Set http message body
        $this->withBody($body);
    }

    /**
     * Send the http response message to client.
     *
     * Return True if success.
     *
     * @return bool
     */
    public function send()
    {
        if (!headers_sent()) {
            $this->sendStatusCode();
            $this->sendHeaders();
            $this->sendBody();
        }

        return true;
    }

    /**
     * Send http response status code to client
     */
    protected function sendStatusCode()
    {
        $statusCode = $this->getStatusCode();
        if (!is_null($statusCode)) {
            http_response_code($statusCode);
        }
    }

    /**
     * Send http message headers to client
     */
    protected function sendHeaders()
    {
        $headers = $this->getHeaders();
        if (!is_null($headers)) {
            foreach ($headers as $name => $header) {
                header($name . ':' . $this->getHeaderLine($name));
            }
        }
    }

    /**
     * Send http message body to client
     */
    protected function sendBody()
    {
        $body = $this->getBody();
        if (!is_null($body)) {
            echo $body;
        }
    }
}