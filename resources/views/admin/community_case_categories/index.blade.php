@extends('layouts.admin.master')
@section('title', 'Community Case Categories')

@section('content')
    <livewire:admin.category-manager
        :model-type="\App\Models\CommunityCaseCategory::class"
        :translation-type="\App\Models\CommunityCaseCategoryTranslation::class"
        foreign-key="community_case_category_id"
        count-relation="communityCases"
        page-title="Community Case Categories" />
@endsection