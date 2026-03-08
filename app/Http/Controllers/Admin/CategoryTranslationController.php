<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryTranslationController extends Controller
{

    public function create() {
        return view('admin.categories.create');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            "cat_sign" => 'required|string|unique:categories,sign',
            'cat_featured_img' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',

            'english_slug' => 'required|string|unique:category_translations,slug',
            'english_name' => 'required|string',
            'english_description' => 'required|string',
            'english_meta_title' => 'required|string',
            'english_meta_description' => 'required|string',
        ]);

        $english_slug = $request->english_slug;
        $english_name = $request->english_name;
        $english_description = $request->english_description;
        $english_meta_title = $request->english_meta_title;
        $english_meta_description = $request->english_meta_description;



        //inserting main article record (without translation)
        $cat = Category::create([
            'sign' => $request->cat_sign
        ]);


        $file = $request->file('cat_featured_img');
        $file_ext = $file->getClientOriginalExtension();
        $fileName = Str::random(8) . '_' . time() . '.' . $file_ext;


        $cat
            ->addMedia($file) // $article->addMediaFromRequest('content_img)
            //->usingFileName(Str::random(32).'.'.$file_ext)
            ->usingFileName($fileName)
            //->withResponsiveImages()
            ->toMediaCollection("featured_imgs");


        //inserting English article translation
        $cat->translations()->create([
            'language_code' => 'en',
            'slug' => $english_slug,
            'name' => $english_name,
            'description' => $english_description,
            'meta_title' => $english_meta_title,
            'meta_description' => $english_meta_description,
        ]);

        //inserting other optional category translations
        $this->store_cat_trans($cat, $request, 'arabic', 'ar');
        $this->store_cat_trans($cat, $request, 'german', 'gr');
        $this->store_cat_trans($cat, $request, 'spanish', 'sp');
        $this->store_cat_trans($cat, $request, 'french', 'fr');
        $this->store_cat_trans($cat, $request, 'russian', 'ru');
        $this->store_cat_trans($cat, $request, 'portuguese', 'pt');

        return to_route('admin.categories.index');
    }

    public function store_cat_trans(Category $cat, Request $request, string $lang, string $lang_code) {


        $lang_slug = $request->input("{$lang}_slug");
        $lang_name = $request->input("{$lang}_name");
        $lang_description = $request->input("{$lang}_description");
        $lang_meta_title = $request->input("{$lang}_meta_title");
        $lang_meta_description = $request->input("{$lang}_meta_description");
        if(
            $lang_slug != null &&
            $lang_name != null &&
            $lang_description != null &&
            $lang_meta_title != null &&
            $lang_meta_description != null
        ) {

            $cat->translations()->create([
                'language_code' => $lang_code,
                'slug' => $lang_slug,
                'name' => $lang_name,
                'description' => $lang_description,
                'meta_title' => $lang_meta_title,
                'meta_description' => $lang_meta_description,
            ]);
        }
    }

    public function edit($id) {
        $cat = Category::with('translations')->find($id);
        $translations = $cat->translations->keyBy('language_code');
        return view('admin.categories.edit', compact('cat', 'translations'));
    }

    public function update(Request $request, int $id) {
        $cat = Category::find($id);
    
        $validated = $request->validate([
            'cat_sign' => "required|string|unique:categories,sign,$id",
            'cat_featured_img' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',

            'english_slug' => "required|string|unique:category_translations,slug,{$cat->translations()->where('language_code', 'en')->value('id')}",
            'english_name' => 'required|string',
            'english_description' => 'required|string',
            'english_meta_title' => 'required|string',
            'english_meta_description' => 'required|string',

            'arabic_slug' => "unique:category_translations,slug,{$cat->translations()->where('language_code', 'ar')->value('id')}",
            'german_slug' => "unique:category_translations,slug,{$cat->translations()->where('language_code', 'gr')->value('id')}",
            'spanish_slug' => "unique:category_translations,slug,{$cat->translations()->where('language_code', 'sp')->value('id')}",
            'french_slug' => "unique:category_translations,slug,{$cat->translations()->where('language_code', 'fr')->value('id')}",
            'russian_slug' => "unique:category_translations,slug,{$cat->translations()->where('language_code', 'ru')->value('id')}",
            'portuguese_slug' => "unique:category_translations,slug,{$cat->translations()->where('language_code', 'pt')->value('id')}",
        ]);

        $english_slug = $request->english_slug;
        $english_name = $request->english_name;
        $english_description = $request->english_description;
        $english_meta_title = $request->english_meta_title;
        $english_meta_description = $request->english_meta_description;

        //Updating main category record (without translation)
        $cat->update([
            'sign' => $request->cat_sign
        ]);


        if($request->hasFile('cat_featured_img')) {
            if ($cat->hasMedia('featured_imgs')) {
                $cat->clearMediaCollection('featured_imgs');
            }
            $file = $request->file('cat_featured_img');
            $file_ext = $file->getClientOriginalExtension();
            $fileName = Str::random(8) . '_' . time() . '.' . $file_ext;

            $cat
                ->addMedia($file) // $article->addMediaFromRequest('content_img)
                //->usingFileName(Str::random(32).'.'.$file_ext)
                ->usingFileName($fileName)
                //->withResponsiveImages()
                ->toMediaCollection("featured_imgs");
        }

        //Updating English category translation
        $cat->translations()->where('language_code', 'en')->update([
            //'language_code' => 'en',
            'slug' => $english_slug,
            'name' => $english_name,
            'description' => $english_description,
            'meta_title' => $english_meta_title,
            'meta_description' => $english_meta_description,
        ]);

        $this->update_cat_trans($cat, $request, 'arabic', 'ar');
        $this->update_cat_trans($cat, $request, 'german', 'gr');
        $this->update_cat_trans($cat, $request, 'spanish', 'sp');
        $this->update_cat_trans($cat, $request, 'french', 'fr');
        $this->update_cat_trans($cat, $request, 'russian', 'ru');
        $this->update_cat_trans($cat, $request, 'portuguese', 'pt');

        return to_route('admin.categories.index');
        /*
        $current_trans = $cat->translations()
        ->where('language_code', $lang_code)
        ->first();

        $validated = $request->validate([
            "{$lang}_slug" => "unique:category_translations,slug,{$current_trans?->id}"
        ]);
        */

    }

    public function update_cat_trans(Category $cat, Request $request, string $lang, string $lang_code) {

        $lang_slug = $request->input("{$lang}_slug");
        $lang_name = $request->input("{$lang}_name");
        $lang_description = $request->input("{$lang}_description");
        $lang_meta_title = $request->input("{$lang}_meta_title");
        $lang_meta_description = $request->input("{$lang}_meta_description");
        if(
            $lang_slug != null &&
            $lang_name != null &&
            $lang_description != null &&
            $lang_meta_title != null &&
            $lang_meta_description != null
        ) {

            $cat_trans = $cat->translations()->where('language_code', $lang_code);
            if($cat_trans->exists()) {
                $cat_trans->update([
                    'language_code' => $lang_code,
                    'slug' => $lang_slug,
                    'name' => $lang_name,
                    'description' => $lang_description,
                    'meta_title' => $lang_meta_title,
                    'meta_description' => $lang_meta_description,
                ]);
            }else{
                $this->store_cat_trans($cat, $request, $lang, $lang_code);
            }

        }else{

        }
    }

    public function destroy($cat_id) {
        $cat = Category::findOrFail($cat_id);
        $cat->delete();
        return to_route('admin.categories.index');
    }
}
