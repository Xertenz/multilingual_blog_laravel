@extends('layouts.admin.master')
@section('title', 'Media Categories')

@section('content')
    <livewire:admin.category-manager
        :model-type="\App\Models\MediaSummaryCategory::class"
        :translation-type="\App\Models\MediaSummaryCategoryTranslation::class"
        foreign-key="media_summary_category_id"
        count-relation="mediaSummaries"
        page-title="Media Categories" />
@endsection