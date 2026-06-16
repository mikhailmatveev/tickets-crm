<?php

namespace App\Swagger\Schemas;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MediaResourceSchema',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 101),
        new OA\Property(property: 'name', type: 'string', example: 'invoice.pdf'),
        new OA\Property(property: 'mime_type', type: 'string', example: 'application/pdf'),
        new OA\Property(property: 'size', description: 'Размер файла в байтах', type: 'integer', example: 245678),
        new OA\Property(
            property: 'url',
            type: 'string',
            format: 'uri',
            example: 'http://localhost/storage/101/invoice.pdf'
        ),
    ],
    type: 'object'
)]
class MediaResourceSchema {}
