<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;

use App\Models\ContentBlock;

class ContentController extends Controller
{
    // Retrieve content for the hero section
    public function hero()
    {
        $content = ContentBlock::where('section', 'hero')->first();
        return response()->json($content);
    }

    // Retrieve content for the about section
    public function about()
    {
        $content = ContentBlock::where('section', 'about')->first();
        return response()->json($content);
    }

    // Retrieve content for the promotion section
    public function promotion()
    {
        $content = ContentBlock::where('section', 'promotion')->first();
        return response()->json($content);
    }
     public function logo()
    {
        $content = ContentBlock::where('section', 'logo')->first();
        return response()->json($content);
    }
}
