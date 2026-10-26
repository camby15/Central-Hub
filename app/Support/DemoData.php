<?php

namespace App\Support;

class DemoData
{
    /**
     * @return array<string, mixed>
     */
    public static function stats(): array
    {
        return [
            'open_tickets' => 18,
            'overdue_sla' => 3,
            'new_today' => 7,
            'avg_first_response' => '42m',
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function sites(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Fragrance Store',
                'slug' => 'FRG',
                'url' => 'https://shop.fragrance.example',
                'framework' => 'laravel',
                'is_active' => true,
                'last_seen_at' => '2 minutes ago',
                'api_key_prefix' => 'frg_live',
                'allowed_origins' => ['https://shop.fragrance.example', 'https://www.fragrance.example'],
                'primary_color' => '#00B4FF',
                'logo_label' => 'Fragrance',
            ],
            [
                'id' => 2,
                'name' => 'Partner Portal',
                'slug' => 'PRT',
                'url' => 'https://partners.example.com',
                'framework' => 'django',
                'is_active' => true,
                'last_seen_at' => '18 minutes ago',
                'api_key_prefix' => 'prt_live',
                'allowed_origins' => ['https://partners.example.com'],
                'primary_color' => '#0F766E',
                'logo_label' => 'Partners',
            ],
            [
                'id' => 3,
                'name' => 'Marketing Site',
                'slug' => 'MKT',
                'url' => 'https://www.example.com',
                'framework' => 'html',
                'is_active' => false,
                'last_seen_at' => '3 days ago',
                'api_key_prefix' => 'mkt_live',
                'allowed_origins' => ['https://www.example.com'],
                'primary_color' => '#B45309',
                'logo_label' => 'Marketing',
            ],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function site(int $id): ?array
    {
        foreach (self::sites() as $site) {
            if ($site['id'] === $id) {
                return $site;
            }
        }

        return null;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function tickets(): array
    {
        return [
            [
                'id' => 1,
                'number' => 'FRG-00042',
                'subject' => 'Order #1043 not delivered',
                'site_id' => 1,
                'site' => 'Fragrance Store',
                'status' => 'open',
                'priority' => 'high',
                'assignee' => 'Ama Mensah',
                'customer_name' => 'Kwame Boateng',
                'customer_email' => 'kwame@example.com',
                'age' => '2h',
                'sla_percent' => 55,
                'sla_label' => '1h 12m left',
                'sla_state' => 'ok',
                'category' => 'Order issue',
                'created_at' => 'Today 08:14',
                'meta' => [
                    'page' => 'https://shop.fragrance.example/orders/1043',
                    'browser' => 'Chrome 131 · macOS',
                    'order_id' => '1043',
                ],
            ],
            [
                'id' => 2,
                'number' => 'FRG-00041',
                'subject' => 'Wrong scent in package',
                'site_id' => 1,
                'site' => 'Fragrance Store',
                'status' => 'in_progress',
                'priority' => 'normal',
                'assignee' => 'You',
                'customer_name' => 'Esi Owusu',
                'customer_email' => 'esi@example.com',
                'age' => '5h',
                'sla_percent' => 78,
                'sla_label' => '42m left',
                'sla_state' => 'warning',
                'category' => 'Order issue',
                'created_at' => 'Today 05:02',
                'meta' => [
                    'page' => 'https://shop.fragrance.example/account/orders',
                    'browser' => 'Safari 18 · iOS',
                    'order_id' => '991',
                ],
            ],
            [
                'id' => 3,
                'number' => 'PRT-00018',
                'subject' => 'Cannot reset partner password',
                'site_id' => 2,
                'site' => 'Partner Portal',
                'status' => 'waiting_customer',
                'priority' => 'high',
                'assignee' => 'Kofi Asante',
                'customer_name' => 'Nana Adjei',
                'customer_email' => 'nana@partner.example',
                'age' => '1d',
                'sla_percent' => 40,
                'sla_label' => 'Paused',
                'sla_state' => 'paused',
                'category' => 'Account',
                'created_at' => 'Yesterday 16:40',
                'meta' => [
                    'page' => 'https://partners.example.com/login',
                    'browser' => 'Firefox 134 · Windows',
                    'order_id' => null,
                ],
            ],
            [
                'id' => 4,
                'number' => 'FRG-00040',
                'subject' => 'Checkout fails on mobile',
                'site_id' => 1,
                'site' => 'Fragrance Store',
                'status' => 'new',
                'priority' => 'urgent',
                'assignee' => null,
                'customer_name' => 'Abena Darko',
                'customer_email' => 'abena@example.com',
                'age' => '28m',
                'sla_percent' => 92,
                'sla_label' => '4m left',
                'sla_state' => 'danger',
                'category' => 'Bug',
                'created_at' => 'Today 09:48',
                'meta' => [
                    'page' => 'https://shop.fragrance.example/checkout',
                    'browser' => 'Chrome 131 · Android',
                    'order_id' => null,
                ],
            ],
            [
                'id' => 5,
                'number' => 'MKT-00007',
                'subject' => 'Newsletter signup confirmation missing',
                'site_id' => 3,
                'site' => 'Marketing Site',
                'status' => 'resolved',
                'priority' => 'low',
                'assignee' => 'You',
                'customer_name' => 'Yaw Mensah',
                'customer_email' => 'yaw@example.com',
                'age' => '3d',
                'sla_percent' => 100,
                'sla_label' => 'Met',
                'sla_state' => 'ok',
                'category' => 'General',
                'created_at' => '3 days ago',
                'meta' => [
                    'page' => 'https://www.example.com/newsletter',
                    'browser' => 'Edge 131 · Windows',
                    'order_id' => null,
                ],
            ],
            [
                'id' => 6,
                'number' => 'PRT-00017',
                'subject' => 'API key rotation request',
                'site_id' => 2,
                'site' => 'Partner Portal',
                'status' => 'on_hold',
                'priority' => 'normal',
                'assignee' => 'Developer queue',
                'customer_name' => 'Tech Ops',
                'customer_email' => 'ops@partner.example',
                'age' => '2d',
                'sla_percent' => 35,
                'sla_label' => 'Paused',
                'sla_state' => 'paused',
                'category' => 'Technical',
                'created_at' => '2 days ago',
                'meta' => [
                    'page' => 'https://partners.example.com/settings/api',
                    'browser' => 'Chrome 131 · Linux',
                    'order_id' => null,
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function ticket(int $id): ?array
    {
        foreach (self::tickets() as $ticket) {
            if ($ticket['id'] === $id) {
                return $ticket;
            }
        }

        return self::tickets()[0] ?? null;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function replies(int $ticketId = 1): array
    {
        return [
            [
                'id' => 1,
                'author_type' => 'customer',
                'author' => 'Kwame Boateng',
                'body' => 'I paid on Monday but have not received my perfume. Order #1043 shows as shipped.',
                'is_internal' => false,
                'created_at' => 'Today 08:14',
            ],
            [
                'id' => 2,
                'author_type' => 'agent',
                'author' => 'Ama Mensah',
                'body' => 'Thanks for reaching out. I am checking the courier tracking for order #1043 now.',
                'is_internal' => false,
                'created_at' => 'Today 08:31',
            ],
            [
                'id' => 3,
                'author_type' => 'agent',
                'author' => 'Ama Mensah',
                'body' => 'Courier shows delayed in Accra sorting. Offering replacement or refund if not delivered by Friday.',
                'is_internal' => true,
                'created_at' => 'Today 08:33',
            ],
            [
                'id' => 4,
                'author_type' => 'customer',
                'author' => 'Kwame Boateng',
                'body' => 'A replacement would be great if it can arrive this week.',
                'is_internal' => false,
                'created_at' => 'Today 09:05',
            ],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function activity(int $ticketId = 1): array
    {
        return [
            ['event' => 'Ticket created', 'actor' => 'Widget', 'at' => 'Today 08:14'],
            ['event' => 'Priority set to High', 'actor' => 'System', 'at' => 'Today 08:14'],
            ['event' => 'Assigned to Ama Mensah', 'actor' => 'You', 'at' => 'Today 08:22'],
            ['event' => 'Status → In progress', 'actor' => 'Ama Mensah', 'at' => 'Today 08:31'],
            ['event' => 'Status → Open', 'actor' => 'Ama Mensah', 'at' => 'Today 09:06'],
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function ticketsBySite(): array
    {
        return [
            ['site' => 'Fragrance Store', 'open' => 11, 'overdue' => 2],
            ['site' => 'Partner Portal', 'open' => 5, 'overdue' => 1],
            ['site' => 'Marketing Site', 'open' => 2, 'overdue' => 0],
        ];
    }

    /**
     * @return list<array<string, string>>
     */
    public static function savedViews(): array
    {
        return [
            ['key' => 'all', 'label' => 'All tickets'],
            ['key' => 'mine', 'label' => 'Assigned to me'],
            ['key' => 'urgent', 'label' => 'Urgent & breached'],
            ['key' => 'unassigned', 'label' => 'Unassigned'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function statusLabels(): array
    {
        return [
            'new' => 'New',
            'open' => 'Open',
            'in_progress' => 'In progress',
            'waiting_customer' => 'Waiting on customer',
            'on_hold' => 'On hold',
            'resolved' => 'Resolved',
            'closed' => 'Closed',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function priorityLabels(): array
    {
        return [
            'low' => 'Low',
            'normal' => 'Normal',
            'high' => 'High',
            'urgent' => 'Urgent',
        ];
    }
}
