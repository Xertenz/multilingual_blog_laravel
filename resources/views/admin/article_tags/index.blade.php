{{-- resources/views/admin/tags/index.blade.php --}}
@extends('layouts.admin.master')

@section('title', 'Tags')

@section('content')
    <livewire:admin.tag-manager
        :model-type="\App\Models\ArticleTag::class"
        :translation-type="\App\Models\ArticleTagTranslation::class"
        page-title="Article Tags" />
@endsection