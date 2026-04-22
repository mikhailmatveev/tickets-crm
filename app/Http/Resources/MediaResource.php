<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MediaResource',
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
class MediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->file_name,
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'url' => $this->getUrl()
        ];
    }
}
