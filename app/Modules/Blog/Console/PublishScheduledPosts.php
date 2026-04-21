<?php

namespace App\Modules\Blog\Console;

use App\Modules\Blog\Models\BlogPost;
use Illuminate\Console\Command;

/**
 * Promotes scheduled posts whose `published_at` is now in the past.
 *
 * Wired into the framework scheduler from routes/console.php and
 * intended to be invoked once per minute. Idempotent — never demotes
 * a post that's already published or older.
 */
class PublishScheduledPosts extends Command
{
    protected $signature = 'blog:publish-scheduled';

    protected $description = 'Promote due scheduled blog posts to published.';

    public function handle(): int
    {
        $count = 0;

        BlogPost::query()
            ->scheduledDue()
            ->chunkById(100, function ($posts) use (&$count): void {
                foreach ($posts as $post) {
                    $post->forceFill(['status' => BlogPost::STATUS_PUBLISHED])->save();
                    $count++;
                }
            });

        $this->components->info("Published {$count} scheduled post(s).");

        return self::SUCCESS;
    }
}
