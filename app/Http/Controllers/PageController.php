<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function terms()
    {
        $page = Page::where('slug', 'terms')->firstOrFail();

        return view('pages.show', compact('page'));
    }

    public function show($slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();

        return view('pages.show', compact('page'));
    }
}
