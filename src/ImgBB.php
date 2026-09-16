<?php

namespace ImgBB;

use Exception;
use KyPHP\KyPHP;

class ImgBB
{
    private string $apiKey;
    private string $endpoint = 'https://api.imgbb.com/1/upload';

    private int $retry = 3;
    private bool $debug = false;

    private $beforeHook = null;
    private $afterHook = null;

    public function __construct(string $apiKey)
    {
        if ($apiKey === '') {
            throw new Exception('ImgBB API key is required');
        }

        $this->apiKey = $apiKey;
    }

    /**
     * Set the number of retries for failed requests.
     *
     * retry(3) means:
     * 1 initial request + 3 retries = 4 attempts.
     */
    public function retry(int $attempts): self
    {
        $this->retry = max(0, $attempts);

        return $this;
    }

    /**
     * Enable or disable KyPHP debugging.
     */
    public function debug(bool $enabled = true): self
    {
        $this->debug = $enabled;

        return $this;
    }

    /**
     * Register a KyPHP before-request hook.
     *
     * The callback is executed before each request attempt.
     */
    public function beforeRequest(callable $fn): self
    {
        $this->beforeHook = $fn;

        return $this;
    }

    /**
     * Register a KyPHP after-response hook.
     *
     * The callback receives the KyPHP response array.
     */
    public function afterResponse(callable $fn): self
    {
        $this->afterHook = $fn;

        return $this;
    }

    /**
     * Upload a single image.
     */
    public function upload(
        string $file,
        ?string $name = null,
        ?int $expiration = null
    ): Response {
        if (!is_file($file)) {
            throw new Exception("File not found: {$file}");
        }

        $multipart = new Multipart();

        $body = $multipart->file(
            $file,
            $name,
            $expiration
        );

        $request = $this->createRequest(
            $multipart,
            $body
        );

        return new Response(
            $request->sendJson()
        );
    }

    /**
     * Upload multiple images concurrently.
     *
     * KyPHP handles the concurrent HTTP requests.
     */
    public function uploadBatch(
        array $files,
        ?string $name = null,
        ?int $expiration = null
    ): array {
        foreach ($files as $file) {
            if (!is_string($file) || !is_file($file)) {
                throw new Exception("File not found: {$file}");
            }
        }

        foreach ($files as $file) {
            $multipart = new Multipart();

            $body = $multipart->file(
                $file,
                $name,
                $expiration
            );

            $request = $this->createRequest(
                $multipart,
                $body
            );

            $request->addToBatch();
        }

        $responses = KyPHP::sendBatchJson();

        $results = [];

        foreach ($responses as $response) {
            $results[] = new Response($response);
        }

        return $results;
    }

    /**
     * Create and configure a KyPHP request.
     */
    private function createRequest(
        Multipart $multipart,
        string $body
    ): KyPHP {
        $request = new KyPHP();

        $request
            ->post($this->endpoint)
            ->query([
                'key' => $this->apiKey
            ])
            ->header(
                'Content-Type',
                $multipart->contentType()
            )
            ->header(
                'Accept',
                'application/json'
            )
            ->retry($this->retry)
            ->debug($this->debug);

        if (is_callable($this->beforeHook)) {
            $request->beforeRequest($this->beforeHook);
        }

        if (is_callable($this->afterHook)) {
            $request->afterResponse($this->afterHook);
        }

        $this->setBody($request, $body);

        return $request;
    }

    /**
     * Set the multipart request body.
     *
     * This currently uses KyPHP's internal body property because
     * the public KyPHP API does not yet expose a raw-body setter.
     */
    private function setBody(
        KyPHP $request,
        string $body
    ): void {
        $setter = \Closure::bind(
            static function (
                KyPHP $request,
                string $body
            ): void {
                $request->body = $body;
            },
            null,
            KyPHP::class
        );

        $setter($request, $body);
    }
}
