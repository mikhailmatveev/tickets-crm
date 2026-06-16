<?php

namespace App\Enums\User;

use OpenApi\Attributes as OA;

enum PermissionEnum: string
{
    case CHANGE_TICKET_STATUS = 'change_ticket_status';
    case CHANGE_USER_PASSWORD = 'change_user_password';
    case CHANGE_USER_ROLE = 'change_user_role';
    case CREATE_USER = 'create_user';
    case DELETE_USER = 'delete_user';
    case REPLY_ON_TICKET = 'reply_ticket';
    case VIEW_API_DOCS = 'view_api_docs';
    case VIEW_TELESCOPE = 'view_telescope';
    case VIEW_TICKET_DETAILS = 'view_ticket_details';
    case VIEW_TICKET_STATS = 'view_ticket_stats';
    case VIEW_TICKETS = 'view_tickets';

    /**
     * Helper-метод для передачи permissions в Gate
     * @return string
     */
    public function middleware(): string
    {
        return 'permission:' . $this->value;
    }

//    public static function values(self ...$permissions): array
//    {
//        return array_map(
//            static fn (self $permission) => $permission->value,
//            $permissions
//        );
//    }
}
