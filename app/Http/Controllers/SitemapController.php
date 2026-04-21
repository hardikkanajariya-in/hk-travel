<?php

namespace App\Http\Controllers;

use App\Core\Seo\SitemapGenerator;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class SitemapController extends Controller
{
    public function __construct(protected Filesystem $files) {}

    public function index(SitemapGenerator $generator): Response
    {
        $path = public_path('sitemaps/sitemap.xml');

        // Regenerate if missing OR older than 1 hour. Keeps the file fresh
        // without forcing an expensive rebuild on every public request.
        if (! $this->files->exists($path) || (time() - $this->files->lastModified($path)) > 3600) {
            $generator->generate();
        }

        return $this->serve($path);
    }

    public function child(string $name): Response
    {
        $name = basename($name);
        $path = public_path('sitemaps/'.$name);
        abort_unless($this->files->exists($path) && str_ends_with($name, '.xml'), 404);

        return $this->serve($path);
    }

    protected function serve(string $path): BinaryFileResponse
    {
        return response()->file($path, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'X-Robots-Tag' => 'noindex',
        ]);
    }
}
