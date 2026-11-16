<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class TagManager extends Component
{
    use WithPagination;

    // ✅ خصائص المكون (تُمرَّر من Blade)
    public string $modelType;          // e.g. \App\Models\ArticleTag::class
    public string $translationType;    // e.g. \App\Models\ArticleTagTranslation::class
    public string $pageTitle;          // e.g. 'Article Tags'

    // ✅ حالة النموذج (Modal)
    public bool $showModal = false;
    public ?int $editingId = null;
    public string $label = '';
    public bool $is_active = true;
    public array $translations = [];

    // ✅ التهيئة
    public function mount(
        string $modelType,
        string $translationType,
        string $pageTitle = 'Tags'
    ) {
        $this->modelType = $modelType;
        $this->translationType = $translationType;
        $this->pageTitle = $pageTitle;

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
        $this->showModal = true;
    }

    // ✅ فتح Modal التعديل
    public function openEditModal(int $id)
    {
        $this->resetValidation();

        $model = $this->modelType::with('translations')->findOrFail($id);

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

        $this->showModal = true;
    }

    // ✅ حفظ (إنشاء أو تعديل)
    public function save()
    {
        // 1. التحقق الأساسي
        $rules = [
            'label' => [
                'required',
                'string',
                'max:50',
                Rule::unique((new $this->modelType)->getTable(), 'label')
                    ->ignore($this->editingId),
            ],
            'is_active' => 'boolean',
            'translations.*.name'             => 'nullable|string|max:100',
            'translations.*.slug'             => 'nullable|string|max:100',
            'translations.*.description'      => 'nullable|string',
            'translations.*.meta_title'       => 'nullable|string|max:150',
            'translations.*.meta_description' => 'nullable|string|max:255',
        ];

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

        // 3. مزامنة الترجمات
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

        // 4. إغلاق Modal + رسالة نجاح
        $this->showModal = false;
        $this->reset(['label', 'is_active', 'translations', 'editingId']);
        $this->translations = $this->emptyTranslations();

        $this->dispatch('toast', type: 'success', message: __('Saved successfully'));
    }

    // ✅ حذف
    public function delete(int $id)
    {
        $model = $this->modelType::findOrFail($id);
        $model->translations()->delete();
        $model->delete();

        $this->dispatch('toast', type: 'success', message: __('Deleted successfully'));
    }

    // ✅ تبديل حالة التفعيل بسرعة من الجدول
    public function toggleActive(int $id)
    {
        $model = $this->modelType::findOrFail($id);
        $model->update(['is_active' => !$model->is_active]);
    }

    // ✅ العرض
    public function render()
    {
        $items = $this->modelType::with('translations')
            ->latest()
            ->paginate(10);

        $supportedLocales = LaravelLocalization::getSupportedLocales();

        return view('livewire.tag-manager', compact('items', 'supportedLocales'));
    }
}
