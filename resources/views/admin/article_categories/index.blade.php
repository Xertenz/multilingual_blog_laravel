@extends('layouts.admin.master')
@section('title', 'Article Categories')

@section('content')
    <livewire:admin.category-manager
        :model-type="\App\Models\ArticleCategory::class"
        :translation-type="\App\Models\ArticleCategoryTranslation::class"
        foreign-key="article_category_id"
        count-relation="articles"
        page-title="Article Categories" />
@endsection