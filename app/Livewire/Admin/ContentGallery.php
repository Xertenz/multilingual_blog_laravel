<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class ContentGallery extends Component
{
    use WithFileUploads;

    public string $modelType;
    public int $modelId;
    public string $collection = 'content_imgs';
    public bool $showModal = false;
    public $newImage;
    public array $images = [];

    public function mount(string $modelType, int $modelId, string $collection = 'content_imgs')
    {
        $this->modelType = $modelType;
        $this->modelId = $modelId;
        $this->collection = $collection;
        $this->loadImages();
    }

    protected function getModel(): ?HasMedia
    {
        if (!class_exists($this->modelType)) {
            return null;
        }

        $model = $this->modelType::find($this->modelId);

        return $model instanceof HasMedia ? $model : null;
    }

    public function loadImages()
    {
        $model = $this->getModel();
        if (!$model) {
            $this->images = [];
            return;
        }

        $this->images = $model->getMedia($this->collection)->map(fn($m) => [
            'id' => $m->id,
            'url' => $m->getUrl(),
            'thumb' => $m->getUrl('thumb') ?? $m->getUrl(),
            'name' => $m->file_name,
        ])->all();
    }

    public function openModal()
    {
        $this->loadImages();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->newImage = null;
    }

    // ✅ هذا هو المفتاح: يُستدعى تلقائياً بعد رفع newImage
    public function updatedNewImage()
    {
        $this->uploadImage();
    }

    public function uploadImage()
    {
        $this->validate([
            'newImage' => 'required|image|max:5120',
        ]);

        if (!auth()->user()->hasAnyRole(['super-admin', 'editor', 'writer'])) {
            abort(403);
        }

        $model = $this->getModel();
        if (!$model) {
            $this->addError('newImage', 'Model not found.');
            return;
        }

        try {
            $model->addMedia($this->newImage->getRealPath())
                ->usingFileName(uniqid() . '.' . $this->newImage->getClientOriginalExtension())
                ->withResponsiveImages()
                ->toMediaCollection($this->collection);

            $this->newImage = null;
            $this->loadImages();

            $this->dispatch('image-uploaded');
        } catch (\Throwable $e) {
            \Log::error('Gallery upload failed: ' . $e->getMessage());
            $this->addError('newImage', 'Upload failed: ' . $e->getMessage());
        }
    }

    public function deleteImage($mediaId)
    {
        if (!auth()->user()->hasAnyRole(['super-admin', 'editor', 'writer'])) {
            abort(403);
        }

        $media = Media::find($mediaId);
        if ($media && $media->model_id === $this->modelId && $media->model_type === $this->modelType) {
            $media->delete();
            $this->loadImages();
        }
    }

    public function render()
    {
        return view('livewire.admin.content-gallery');
    }
}
