<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class CategoryManager extends Component
{
    use WithPagination;
    use WithFileUploads;

    // ✅ خصائص المكون (تُمرَّر من Blade)
    public string $modelType;          // e.g. \App\Models\ArticleCategory::class
    public string $translationType;    // e.g. \App\Models\ArticleCategoryTranslation::class
    public string $foreignKey;         // e.g. 'article_category_id'
    public string $pageTitle;          // e.g. 'Article Categories'
    public ?string $countRelation = null;  // e.g. 'articles' (اختياري)

    // ✅ حالة النموذج (Modal)
    public bool $showModal = false;
    public ?int $editingId = null;
    public string $label = '';
    public bool $is_active = true;
    public array $translations = [];

    // ✅ رفع الصور
    public $featured_img = null;         // الملف الجديد
    public ?string $currentImageUrl = null;  // الصورة الحالية
    public bool $removeCurrentImage = false; // حذف الصورة الحالية

    // ✅ التهيئة
    public function mount(
        string $modelType,
        string $translationType,
        string $foreignKey,
        string $pageTitle = 'Categories',
        ?string $countRelation = null
    ) {
        $this->modelType = $modelType;
        $this->translationType = $translationType;
        $this->foreignKey = $foreignKey;
        $this->pageTitle = $pageTitle;
        $this->countRelation = $countRelation;

        $this->translations = $this->emptyTranslations();
    }

    // ✅ تهيئة مصفوفة الترجمات لكل لغة مدعومة
    protected function emptyTranslations(): array
    {
        $out = [];
        foreach (LaravelLocalization::getSupportedLocales() as $code => $props) {
            $out[$code] = [
                'name'             => '',
                'slug'             => '',
                'description'      => '',
                'meta_title'       => '',
                'meta_description' => '',
            ];
        }
        return $out;
    }

    // ✅ فتح Modal الإضافة
    public function openCreateModal()
    {
        $this->resetValidation();
        $this->editingId = null;
        $this->label = '';
        $this->is_active = true;
        $this->translations = $this->emptyTranslations();
        $this->featured_img = null;
        $this->currentImageUrl = null;
        $this->removeCurrentImage = false;
        $this->showModal = true;
    }

    // ✅ فتح Modal التعديل
    public function openEditModal(int $id)
    {
        $this->resetValidation();

        $model = $this->modelType::with('translations', 'media')->findOrFail($id);

        $this->editingId = $id;
        $this->label = $model->label;
        $this->is_active = (bool) $model->is_active;
        $this->translations = $this->emptyTranslations();

        foreach ($model->translations as $trans) {
            $this->translations[$trans->locale] = [
                'name'             => $trans->name,
                'slug'             => $trans->slug,
                'description'      => $trans->description,
                'meta_title'       => $trans->meta_title,
                'meta_description' => $trans->meta_description,
            ];
        }

        // ✅ جلب الصورة الحالية
        $this->currentImageUrl = $model->getFirstMediaUrl('featured_imgs', 'thumb') ?: null;
        $this->featured_img = null;
        $this->removeCurrentImage = false;

        $this->showModal = true;
    }

    // ✅ حفظ (إنشاء أو تعديل)
    public function save()
    {
        // 1. التحقق
        $rules = [
            'label' => [
                'required',
                'string',
                'max:50',
                Rule::unique((new $this->modelType)->getTable(), 'label')
                    ->ignore($this->editingId),
            ],
            'is_active' => 'boolean',
            'featured_img' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'translations.*.name'             => 'nullable|string|max:100',
            'translations.*.slug'             => 'nullable|string|max:100',
            'translations.*.description'      => 'nullable|string',
            'translations.*.meta_title'       => 'nullable|string|max:150',
            'translations.*.meta_description' => 'nullable|string|max:255',
        ];

        // ✅ الصورة إلزامية عند الإنشاء فقط
        if (!$this->editingId && !$this->featured_img) {
            $rules['featured_img'] = 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120';
        }

        $this->validate($rules);

        // 2. إنشاء/تحديث السجل الأساسي
        $data = [
            'label'     => (string) Str::of($this->label)->slug('-'),
            'is_active' => $this->is_active,
        ];

        if ($this->editingId) {
            $model = $this->modelType::findOrFail($this->editingId);
            $model->update($data);
        } else {
            $data['user_id'] = auth()->id();
            $model = $this->modelType::create($data);
        }

        // 3. معالجة الصورة
        if ($this->featured_img) {
            // حذف الصورة القديمة (إن وجدت)
            if ($model->hasMedia('featured_imgs')) {
                $model->clearMediaCollection('featured_imgs');
            }

            // رفع الصورة الجديدة
            $fileName = uniqid() . '.' . strtolower($this->featured_img->getClientOriginalExtension());
            $model->addMedia($this->featured_img->getRealPath())
                ->usingFileName($fileName)
                ->withResponsiveImages()
                ->toMediaCollection('featured_imgs');
        } elseif ($this->removeCurrentImage && $model->hasMedia('featured_imgs')) {
            // ✅ حذف الصورة الحالية إذا طلب المستخدم
            $model->clearMediaCollection('featured_imgs');
        }

        // 4. مزامنة الترجمات
        foreach ($this->translations as $locale => $data) {
            // إذا كان الاسم فارغاً → احذف الترجمة إن وجدت
            if (empty($data['name'])) {
                $model->translations()->where('locale', $locale)->delete();
                continue;
            }

            $finalSlug = !empty($data['slug'])
                ? (string) Str::of($data['slug'])->slug('-')
                : (string) Str::of($data['name'])->slug('-');

            $model->translations()->updateOrCreate(
                ['locale' => $locale],
                [
                    'name'             => trim($data['name']),
                    'slug'             => trim($finalSlug),
                    'description'      => trim($data['description'] ?? ''),
                    'meta_title'       => trim($data['meta_title'] ?? $data['name']),
                    'meta_description' => trim($data['meta_description'] ?? Str::limit(strip_tags($data['description'] ?? ''), 150)),
                ]
            );
        }

        // 5. إغلاق Modal + رسالة نجاح
        $this->showModal = false;
        $this->reset(['label', 'is_active', 'translations', 'editingId', 'featured_img', 'currentImageUrl', 'removeCurrentImage']);
        $this->translations = $this->emptyTranslations();

        $this->dispatch('toast', type: 'success', message: __('Saved successfully'));
    }

    // ✅ حذف
    public function delete(int $id)
    {
        $model = $this->modelType::findOrFail($id);
        $model->clearMediaCollection('featured_imgs');  // ✅ حذف الصورة
        $model->translations()->delete();
        $model->delete();

        $this->dispatch('toast', type: 'success', message: __('Deleted successfully'));
    }

    // ✅ تبديل حالة التفعيل
    public function toggleActive(int $id)
    {
        $model = $this->modelType::findOrFail($id);
        $model->update(['is_active' => !$model->is_active]);
    }

    // ✅ العرض
    public function render()
    {
        $query = $this->modelType::with('translations', 'media');

        if ($this->countRelation) {
            $query->withCount($this->countRelation);
        }

        $items = $query->latest()->paginate(10);

        $supportedLocales = LaravelLocalization::getSupportedLocales();

        return view('livewire.admin.category-manager', compact('items', 'supportedLocales'));
    }
}
