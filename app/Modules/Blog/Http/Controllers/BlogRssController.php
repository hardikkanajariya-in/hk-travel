<?php

namespace App\Modules\Blog\Http\Controllers;

use App\Core\Routing\PublicUrlGenerator;
use App\Modules\Blog\Models\BlogPost;
use Illuminate\Http\Response;

/**
 * RSS 2.0 feed of the latest published blog posts. Cached for 5 minutes
 * via HTTP headers so guests get the same payload without hitting the
 * database every request.
 */
class BlogRssController
{
    public function __invoke(): Response
    {
        $urls = app(PublicUrlGenerator::class);
        $posts = BlogPost::query()
            ->published()
            ->with('author')
            ->orderByDesc('published_at')
            ->limit(20)
            ->get();

        $site = config('app.name', 'HK Travel');
        $self = $urls->route('blog.rss');
        $home = $urls->route('blog.index');
        $now = now()->toRssString();

        $items = '';
        foreach ($posts as $post) {
            $url = $urls->entity('blog_post', ['slug' => $post->slug]);
            $items .= "    <item>\n"
                .'      <title>'.htmlspecialchars((string) $post->title, ENT_XML1).'</title>'."\n"
                .'      <link>'.htmlspecialchars($url, ENT_XML1).'</link>'."\n"
                .'      <guid isPermaLink="true">'.htmlspecialchars($url, ENT_XML1).'</guid>'."\n"
                .'      <pubDate>'.($post->published_at?->toRssString() ?? $now).'</pubDate>'."\n"
                .'      <description>'.htmlspecialchars(strip_tags((string) $post->excerpt), ENT_XML1).'</description>'."\n"
                .($post->author?->name ? '      <dc:creator>'.htmlspecialchars((string) $post->author->name, ENT_XML1).'</dc:creator>'."\n" : '')
                ."    </item>\n";
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:dc="http://purl.org/dc/elements/1.1/">'."\n"
            ."  <channel>\n"
            .'    <title>'.htmlspecialchars($site, ENT_XML1).' — Blog</title>'."\n"
            .'    <link>'.htmlspecialchars($home, ENT_XML1).'</link>'."\n"
            .'    <atom:link href="'.htmlspecialchars($self, ENT_XML1).'" rel="self" type="application/rss+xml" />'."\n"
            .'    <description>Latest stories from '.htmlspecialchars($site, ENT_XML1).'.</description>'."\n"
            .'    <language>'.app()->getLocale().'</language>'."\n"
            .'    <lastBuildDate>'.$now.'</lastBuildDate>'."\n"
            .$items
            ."  </channel>\n"
            .'</rss>';

        return response($xml, 200, [
            'Content-Type' => 'application/rss+xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=300',
        ]);
    }
}
