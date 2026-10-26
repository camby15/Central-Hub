<?php

namespace App\Enums;

enum TicketSource: string
{
    case Widget = 'widget';
    case Api = 'api';
    case Email = 'email';
    case Manual = 'manual';
}
