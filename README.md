
# ImgDB

A lightweight PHP client for the ImgBB image hosting API.

## Features

- Single image uploads
- Concurrent batch uploads
- Optional image names
- Optional expiration
- Simple response wrapper
- Fast HTTP requests powered by KyPHP
- No additional HTTP dependencies

## Requirements

- PHP 8.1+
- cURL
- Fileinfo
- ImgBB API key

## Installation

Install using Composer:

```bash
composer require imgdb/imgdb

```

Composer will automatically install the required KyPHP dependency.

Basic Usage
```
<?php

require 'vendor/autoload.php';

use ImgDB\ImgBB;

$imgbb = new ImgBB('YOUR_IMGBB_API_KEY');

$image = $imgbb->upload('photo.jpg');

echo $image->url();

Upload With a Name

$image = $imgbb->upload(
    'photo.jpg',
    'my-photo'
);

echo $image->url();
```

Expiration

ImgDB does not send an expiration value by default.

To request an expiration time, provide the number of seconds:
```
$image = $imgbb->upload(
    'photo.jpg',
    null,
    3600
);
```
This requests a 1-hour expiration.

ImgBB accepts expiration values from 60 to 15552000 seconds.

Batch Uploads

Multiple images can be uploaded concurrently:
```
$images = $imgbb->uploadBatch([
    'one.jpg',
    'two.jpg',
    'three.png'
]);

foreach ($images as $image) {
    echo $image->url() . PHP_EOL;
}
```
Batch Uploads With Expiration
```
$images = $imgbb->uploadBatch(
    [
        'one.jpg',
        'two.jpg',
        'three.jpg'
    ],
    null,
    86400
);

foreach ($images as $image) {
    echo $image->url() . PHP_EOL;
}
```
This requests a 24-hour expiration for each image.

Response
````
Both "upload()" and "uploadBatch()" return "ImgDB\Response" objects.

Available methods:

$image->id();

$image->url();

$image->displayUrl();

$image->deleteUrl();

$image->thumbnail();

$image->medium();

$image->originalFilename();

$image->name();

$image->mime();

$image->extension();

$image->width();

$image->height();

$image->size();

$image->expiration();

$image->toArray();
````
Complete Example
```
<?php

require 'vendor/autoload.php';

use ImgDB\ImgBB;

$imgbb = new ImgBB('YOUR_IMGBB_API_KEY');

$image = $imgbb->upload(
    __DIR__ . '/images/photo.jpg',
    'my-photo'
);

echo 'URL: ' . $image->url() . PHP_EOL;
echo 'Thumbnail: ' . $image->thumbnail() . PHP_EOL;
echo 'Medium: ' . $image->medium() . PHP_EOL;
echo 'Delete URL: ' . $image->deleteUrl() . PHP_EOL;
```
Batch Example
```
<?php

require 'vendor/autoload.php';

use ImgDB\ImgBB;

$imgbb = new ImgBB('YOUR_IMGBB_API_KEY');

$images = $imgbb->uploadBatch([
    __DIR__ . '/images/one.jpg',
    __DIR__ . '/images/two.jpg',
    __DIR__ . '/images/three.jpg'
]);

foreach ($images as $image) {
    echo $image->url() . PHP_EOL;
}
```
Project Structure
```
imgdb/
├── src/
│   ├── ImgBB.php
│   ├── Multipart.php
│   └── Response.php
├── composer.json
└── README.md
```
How It Works

ImgDB provides a simple interface around the ImgBB upload API.

KyPHP is used internally as the HTTP client, including its concurrent request support for batch uploads.

You only need to interact with the "ImgBB" and "Response" classes.

License

MIT
