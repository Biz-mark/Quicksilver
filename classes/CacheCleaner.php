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

            /* Exclude variables from url */
            if (!empty($settings->blog_post_pattern_exclude_variables)) {
                foreach (array_column($settings->blog_post_pattern_exclude_variables, 'variable') as $variable) {
                    $postUrl = trim(str_replace(":$variable", '', $postUrl));
                }
            }

            /* Prepare post urls for each category */
            if (!empty($settings->blog_post_pattern_category_slug)) {
                $postCategoriesSlugs = $post->categories()->pluck('slug')->toArray();

                foreach ($postCategoriesSlugs as $postCategorySlug) {
                    $urlsToClear[] = trim(str_replace(":{$settings->blog_post_pattern_category_slug}", $postCategorySlug, $postUrl));
                }
            } else {
                $urlsToClear[] = $postUrl;
            }
        }

        /* Prepare extra urls */
        if (!empty($settings->blog_post_extra_urls)) {
            foreach (array_column($settings->blog_post_extra_urls, 'url') as $extraUrl) {
                if ($settings->blog_post_pattern_post_slug) {
                    $extraUrl = trim(str_replace(":{$settings->blog_post_pattern_post_slug}", $post->slug, $extraUrl));
                }

                $urlsToClear[] = $extraUrl;
            }
        }

        /* Prepare post categories pages and child pages recursively */
        if (
            !empty($settings->blog_category_pattern) &&
            !empty($settings->blog_category_pattern_slug)
        ) {
            /* Exclude variables from url */
            if (!empty($settings->blog_category_pattern_exclude_variables)) {
                foreach (array_column($settings->blog_category_pattern_exclude_variables, 'variable') as $variable) {
                    $settings->blog_category_pattern = trim(str_replace(":$variable", '', $settings->blog_category_pattern));
                }
            }

            $settings->blog_category_pattern = trim(rtrim($settings->blog_category_pattern, '/'));

            $postCategoriesSlugs = $post->categories()->pluck('slug')->toArray();

            foreach ($postCategoriesSlugs as $postCategorySlug) {
                $categoryUrl = str_replace(":{$settings->blog_category_pattern_slug}", $postCategorySlug, $settings->blog_category_pattern);

                $urlsToClear[] = $categoryUrl;
                $urlsToClear[] = $categoryUrl . '/*';
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
            /* Exclude variables from url */
            if (!empty($settings->blog_category_pattern_exclude_variables)) {
                foreach (array_column($settings->blog_category_pattern_exclude_variables, 'variable') as $variable) {
                    $settings->blog_category_pattern = str_replace(":$variable", '', $settings->blog_category_pattern);
                }
            }

            $settings->blog_category_pattern = trim(rtrim($settings->blog_category_pattern, '/'));


            $categoryUrl = str_replace(":{$settings->blog_category_pattern_slug}", $category->slug, $settings->blog_category_pattern);

            $urlsToClear[] = $categoryUrl;
            $urlsToClear[] = $categoryUrl . '/*';
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
            foreach (array_column($settings->blog_category_extra_urls, 'url') as $extraUrl) {
                if ($settings->blog_category_pattern_slug) {
                    $extraUrl = trim(str_replace(":{$settings->blog_post_pattern_post_slug}", $category->slug, $extraUrl));
                }

                $urlsToClear[] = $extraUrl;
            }
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
        $properties = [];
        if (preg_match('/\*$/', $url) === 1) {
            $properties['--recursive'] = true;
        }

        $properties['slug'] = preg_replace('/\*$/', '', $url);

        Artisan::call('page-cache:clear', $properties);
    }

    /**
     * @return void
     */
    public static function clear(): void
    {
        Artisan::call('page-cache:clear');
    }
}
