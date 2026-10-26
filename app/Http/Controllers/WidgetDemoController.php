<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class WidgetDemoController extends Controller
{
    public function __invoke(): View
    {
        return view('widget.demo');
    }
}
