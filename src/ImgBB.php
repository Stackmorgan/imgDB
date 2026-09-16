<?php

namespace ImgBB;

use Exception;
use KyPHP\KyPHP;

class ImgBB
{
    private string $apiKey;
    private string $endpoint = 'https://api.imgbb.com/1/upload';

    public function __construct(string $apiKey)
    {
        if ($apiKey === '') {
            throw new Exception('ImgBB API key is required');
        }

        $this->apiKey = $apiKey;
    }

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
            );

        $this->setBody($request, $body);

        return new Response(
            $request->sendJson()
        );
    }

    public function uploadBatch(
        array $files,
        ?string $name = null,
        ?int $expiration = null
    ): array {
        $requests = [];

        foreach ($files as $file) {
            if (!is_string($file) || !is_file($file)) {
                throw new Exception("File not found: {$file}");
            }

            $multipart = new Multipart();

            $body = $multipart->file(
                $file,
                $name,
                $expiration
            );

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
                );

            $this->setBody($request, $body);

            $request->addToBatch();

            $requests[] = $request;
        }

        $responses = KyPHP::sendBatchJson();

        $results = [];

        foreach ($responses as $response) {
            $results[] = new Response($response);
        }

        return $results;
    }

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
