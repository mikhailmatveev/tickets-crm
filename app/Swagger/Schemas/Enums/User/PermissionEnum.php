<?php

namespace App\Swagger\Schemas\Enums\User;

use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'PermissionEnum',
    type: 'string',
    enum: [
        'change_ticket_status',
        'change_user_password',
        'change_user_role',
        'create_user',
        'delete_user',
        'reply_ticket',
        'view_api_docs',
        'view_telescope',
        'view_ticket_details',
        'view_ticket_stats',
        'view_tickets'
    ]
)]
class PermissionEnum {}
