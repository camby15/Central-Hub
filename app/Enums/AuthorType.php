<?php

namespace App\Enums;

enum AuthorType: string
{
    case Agent = 'agent';
    case Customer = 'customer';
    case System = 'system';
}
