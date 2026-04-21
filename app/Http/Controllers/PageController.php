<?php

namespace App\Http\Controllers;

use App\Core\PageBuilder\PageRenderer;
use App\Models\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Renders public CMS pages built with the page builder.
 *
 * Falls through to the bundled `home` view for `/` when no Page is marked
 * as the homepage; falls through with a 404 for any other slug that does
 * not resolve to a published Page.
 */
class PageController extends Controller
{
    public function home(PageRenderer $renderer): Response|View
    {
        $page = $renderer->homepage();

        if ($page) {
            return response()->view('page', ['page' => $page]);
        }

        return response()->view('home');
    }

    public function show(Request $request, string $slug, PageRenderer $renderer): Response
    {
        $page = $renderer->findPublished($slug);

        abort_if($page === null, 404);

        return response()->view('page', ['page' => $page]);
    }
}
