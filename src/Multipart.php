<?php

namespace ImgBB;

use Exception;

class Multipart
{
    private string $boundary;

    public function __construct()
    {
        $this->boundary =
            '----ImgBB' . bin2hex(random_bytes(16));
    }

    public function file(
        string $path,
        ?string $name = null,
        ?int $expiration = null
    ): string {
        $content = file_get_contents($path);

        if ($content === false) {
            throw new Exception(
                "Unable to read file: {$path}"
            );
        }

        $filename = basename($path);

        $mime = mime_content_type($path)
            ?: 'application/octet-stream';

        $body = '';

        $body .= "--{$this->boundary}\r\n";

        $body .=
            'Content-Disposition: form-data; ' .
            'name="image"; ' .
            'filename="' .
            addslashes($filename) .
            "\"\r\n";

        $body .=
            "Content-Type: {$mime}\r\n\r\n";

        $body .= $content;
        $body .= "\r\n";

        if ($name !== null) {
            $body .= "--{$this->boundary}\r\n";

            $body .=
                "Content-Disposition: form-data; " .
                "name=\"name\"\r\n\r\n";

            $body .= $name . "\r\n";
        }

        if ($expiration !== null) {
            $body .= "--{$this->boundary}\r\n";

            $body .=
                "Content-Disposition: form-data; " .
                "name=\"expiration\"\r\n\r\n";

            $body .= $expiration . "\r\n";
        }

        $body .=
            "--{$this->boundary}--\r\n";

        return $body;
    }

    public function contentType(): string
    {
        return
            "multipart/form-data; " .
            "boundary={$this->boundary}";
    }
}
