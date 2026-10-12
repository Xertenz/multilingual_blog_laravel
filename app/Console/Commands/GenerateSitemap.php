<?php

namespace App\Console\Commands;

use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{
    protected $signature = 'sitemap:generate';

    protected $description = 'Generate the sitemap for the website.';

    // * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
    public function handle()
    {
        $this->info('Generating sitemap...');

        $sitemap = Sitemap::create();

        // 1. إضافة الصفحة الرئيسية
        $sitemap->add(
            Url::create('/')
                ->setPriority(1.0)
                ->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY)
        );

        // 2. إضافة المقالات الديناميكية
        Article::with('translations')->chunk(100, function ($articles) use ($sitemap) {
            foreach ($articles as $article) {
                foreach ($article->translations as $article_trans) {
                    if ($article_trans) {
                        $sitemap->add(
                            Url::create(route('articles.show', ['id' => $article_trans->id, 'slug' => $article_trans->slug]))
                                ->setLastModificationDate($article->updated_at) // or $article_trans->updated_at
                                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                                ->setPriority(0.8)
                        );
                    }
                }
            }
        });

        // 3. إضافة جميع ترجمات التصنيفات الديناميكية
        Category::with('translations')->chunk(100, function ($categories) use ($sitemap) {
            foreach ($categories as $category) {
                foreach ($category->translations as $category_trans) {
                    if ($category_trans) {
                        $sitemap->add(
                            Url::create(route('categories.show', ['id' => $category_trans->id, 'slug' => $category_trans->slug]))
                                ->setLastModificationDate($category->updated_at) // or $category_trans->updated_at
                                ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                                ->setPriority(0.6)
                        );
                    }
                }
            }
        });

        // 4. إضافة جميع ترجمات الوسوم (Tags) الديناميكية
        if (class_exists(Tag::class)) {
            Tag::with('translations')->chunk(100, function ($tags) use ($sitemap) {
                foreach ($tags as $tag) {
                    foreach ($tag->translations as $tag_trans) {
                        // تأكد من مطابقة اسم الراوت الخاص بالوسوم لديك، مثلاً tags.show أو ما شابه
                        if (Route::has('tags.show')) {
                            $sitemap->add(
                                Url::create(route('tags.show', ['id' => $tag_trans->id, 'slug' => $tag_trans->slug]))
                                    ->setLastModificationDate($tag->updated_at)
                                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                                    ->setPriority(0.5)
                            );
                        }
                    }
                }
            });
        }

        // 5. حفظ الملف في مجلد public
        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('Sitemap generated successfully!');
    }
    // * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
}
