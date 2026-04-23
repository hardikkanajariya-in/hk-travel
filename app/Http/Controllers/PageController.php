<?php

namespace App\Http\Controllers;

use App\Core\Modules\ModuleManager;
use App\Core\PageBuilder\PageRenderer;
use App\Core\Permalink\PermalinkRouter;
use App\Models\Page;
use App\Modules\Activities\Livewire\Public\ActivityShow;
use App\Modules\Blog\Livewire\Public\BlogShow;
use App\Modules\Buses\Livewire\Public\BusShow;
use App\Modules\Cars\Livewire\Public\CarShow;
use App\Modules\Cruises\Livewire\Public\CruiseShow;
use App\Modules\Destinations\Livewire\Public\DestinationShow;
use App\Modules\Hotels\Livewire\Public\HotelShow;
use App\Modules\Taxi\Livewire\Public\TaxiShow;
use App\Modules\Tours\Livewire\Public\TourShow;
use App\Modules\Visa\Livewire\Public\VisaShow;
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
    /**
     * @return array<string, array{module:string, component:class-string}>
     */
    protected function publicPermalinkComponents(): array
    {
        return [
            'activity' => ['module' => 'activities', 'component' => ActivityShow::class],
            'blog_post' => ['module' => 'blog', 'component' => BlogShow::class],
            'bus' => ['module' => 'buses', 'component' => BusShow::class],
            'car' => ['module' => 'cars', 'component' => CarShow::class],
            'cruise' => ['module' => 'cruises', 'component' => CruiseShow::class],
            'destination' => ['module' => 'destinations', 'component' => DestinationShow::class],
            'hotel' => ['module' => 'hotels', 'component' => HotelShow::class],
            'taxi' => ['module' => 'taxi', 'component' => TaxiShow::class],
            'tour' => ['module' => 'tours', 'component' => TourShow::class],
            'visa' => ['module' => 'visa', 'component' => VisaShow::class],
        ];
    }

    public function home(PageRenderer $renderer): Response|View
    {
        $page = $renderer->homepage();

        if ($page) {
            return response()->view('page', ['page' => $page]);
        }

        return response()->view('home');
    }

    public function show(
        Request $request,
        string $slug,
        PageRenderer $renderer,
        PermalinkRouter $permalinks,
        ModuleManager $modules,
    ): Response {
        $match = $permalinks->match('/'.ltrim($slug, '/'));
        $componentConfig = $match ? $this->publicPermalinkComponents()[$match['entity_type']] ?? null : null;

        if ($componentConfig && $modules->enabled($componentConfig['module']) && isset($match['tokens']['slug'])) {
            return response()->view('public.permalink-entry', [
                'livewireComponent' => $componentConfig['component'],
                'slug' => $match['tokens']['slug'],
            ]);
        }

        $page = $renderer->findPublished($slug);

        abort_if($page === null, 404);

        return response()->view('page', ['page' => $page]);
    }
}
