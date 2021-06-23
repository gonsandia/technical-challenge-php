<?php

namespace Gonsandia\CarPoolingChallenge\Infrastructure\Trace;

use Symfony\Component\HttpFoundation\Request;

class RequestTracer
{
    private static ?RequestTracer $instance = null;

    private Request $request;

    public static function instance(): RequestTracer
    {
        if (null === static::$instance) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    public function getSerializedRequest(): string
    {
        return json_encode([
            'method' => $this->request->getMethod(),
            'content_type' => $this->request->getContentType(),
            'url' => $this->request->getUri(),
            'params' => json_encode($this->request->request->all(), JSON_THROW_ON_ERROR),
            'body' => $this->request->getContent(),
        ], JSON_THROW_ON_ERROR);
    }

    public function hasRequest()
    {
        return isset($this->request);
    }
}
