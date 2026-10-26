<?php

namespace App\Enums;

enum SiteFramework: string
{
    case Laravel = 'laravel';
    case Django = 'django';
    case Html = 'html';
    case Other = 'other';
}
