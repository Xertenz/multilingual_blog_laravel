@extends('layouts.admin.master')
@section('title', 'Game Categories')

@section('content')
    <livewire:admin.category-manager
        :model-type="\App\Models\GameCategory::class"
        :translation-type="\App\Models\GameCategoryTranslation::class"
        foreign-key="game_category_id"
        count-relation="games"
        page-title="Game Categories" />
@endsection