<?php

namespace App\DTO;

use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Data;

class CreateTicketData extends Data
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $phone,
        public readonly string $subject,
        public readonly string $text,
        /** @var UploadedFile[] */
        public readonly array $attachments = []
    ) {}
}
