<?php namespace BizMark\Quicksilver\Classes;

use BizMark\Quicksilver\Models\Settings;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;

class CacheCleaner
{
    /**
     * Rainlab post's cache clearing
     *
     * @param object $post
     * @return void
     */
    public static function clearPost(object $post): void
    {
        $settings = Settings::instance();

        $urlsToClear = [];

        if (
            !empty($settings->blog_post_pattern) &&
            !empty($settings->blog_post_pattern_post_slug)
        ) {
            $postUrl = trim(str_replace(":{$settings->blog_post_pattern_post_slug}", $post->slug, $settings->blog_post_pattern));

            /* Prepare post urls for each category */
            if (!empty($settings->blog_post_pattern_category_slug)) {
                $postCategoriesSlugs = $post->categories()->pluck('slug')->toArray();

                foreach ($postCategoriesSlugs as $postCategorySlug) {
                    $urlsToClear[] = trim(str_replace(":{$settings->blog_post_pattern_category_slug}", $postCategorySlug, $postUrl));

                    /* Prepare urls of each category and child pages recursively */
                    if (
                        !empty($settings->blog_category_pattern) &&
                        !empty($settings->blog_category_pattern_slug)
                    ) {
                        $urlsToClear[] = trim(str_replace(":{$settings->blog_category_pattern_slug}", $postCategorySlug, $settings->blog_category_pattern)) . '*';
                    }
                }
            } else {
                $urlsToClear[] = $postUrl;
            }
        }

        /* Prepare extra urls */
        if (!empty($settings->blog_post_extra_urls)) {
            $urlsToClear = [...$urlsToClear,
                            ...array_column($settings->blog_post_extra_urls, 'url')];
        }

        /* Prepare post categories pages and child pages recursively if they have not been prepared before */
        if (
            empty($postCategoriesSlugs) &&
            !empty($settings->blog_category_pattern) &&
            !empty($settings->blog_category_pattern_slug)
        ) {
            $postCategoriesSlugs = $post->categories()->pluck('slug')->toArray();

            foreach ($postCategoriesSlugs as $postCategorySlug) {
                $urlsToClear[] = trim(str_replace(":{$settings->blog_category_pattern_slug}", $postCategorySlug, $settings->blog_category_pattern)) . '*';
            }
        }

        self::clearUrls($urlsToClear);
    }

    /**
     * Rainlab post's cache clearing or scheduling
     *
     * @param object $post
     * @return void
     */
    public static function scheduleOrClearPost(object $post): void
    {
        $settings = Settings::instance();

        $schedule = $settings->schedule;

        if ($post->published_at <= Carbon::now()) {
            if (!empty($schedule[$post->id])) {
                unset($schedule[$post->id]);
            }

            self::clearPost($post);
        } else {
            $schedule[$post->id] = $post->published_at->format('Y-m-d H:i:s');
        }

        $settings->schedule = $schedule;

        $settings->save();
    }

    /**
     * Clean cache for scheduled posts
     * */
    public static function checkScheduledPosts(): void
    {
        $settings = Settings::instance();
        $schedule = $settings->get('schedule', []);

        $scheduleSize = count($schedule);

        if (!$scheduleSize) return;

        $dateNow = Carbon::now();

        foreach ($schedule as $postId => $datetime) {

            if (Carbon::parse($datetime) <= $dateNow) {
                unset($schedule[$postId]);

                self::clearPost(\RainLab\Blog\Models\Post::find($postId));
            }
        }

        if ($scheduleSize !== count($schedule)) {
            $settings->schedule = $schedule;

            $settings->save();
        }
    }

    /**
     * @param object $category
     * @return void
     */
    public static function clearCategory(object $category): void
    {
        $settings = Settings::instance();

        $urlsToClear = [];

        /* Clear category page and child pages recursively */
        if (
            !empty($settings->blog_category_pattern) &&
            !empty($settings->blog_category_pattern_slug)

        ) {
            $urlsToClear[] = trim(str_replace(":{$settings->blog_category_pattern_slug}", $category->slug, $settings->blog_category_pattern)) . '*';
        }

        /* Clear category posts */
        if (
            !empty($settings->blog_post_pattern) &&
            !empty($settings->blog_post_pattern_post_slug)
        ) {
            $categoryPostsSlugs = $category->posts()->pluck('slug')->toArray();

            foreach ($categoryPostsSlugs as $categoryPostSlug) {
                $postUrl = trim(str_replace(":{$settings->blog_post_pattern_post_slug}", $categoryPostSlug, $settings->blog_post_pattern));

                if (!empty($settings->blog_post_pattern_category_slug)) {
                    $urlsToClear[] = trim(str_replace(":{$settings->blog_category_pattern_slug}", $category->slug, $postUrl));
                } else {
                    $urlsToClear[] = $postUrl;
                }
            }
        }

        /* Clear extra urls */
        if (!empty($settings->blog_category_extra_urls)) {
            $urlsToClear = [...$urlsToClear,
                            ...array_column($settings->blog_category_extra_urls, 'url')];
        }

        self::clearUrls($urlsToClear);
    }

    /**
     * @param array $urls
     * @return void
     */
    public static function clearUrls(array $urls): void
    {
        foreach ($urls as $url) {
            self::clearUrl($url);
        }
    }

    /**
     * @param string $url
     * @return void
     */
    public static function clearUrl(string $url): void
    {
        $url = preg_replace('/\*$/', ' --recursive', $url);

        Artisan::call('page-cache:clear ' . $url);
    }

    /**
     * @return void
     */
    public static function clear(): void
    {
        Artisan::call('page-cache:clear');
    }
}
