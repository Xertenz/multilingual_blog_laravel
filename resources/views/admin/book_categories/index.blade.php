@extends('layouts.admin.master')
@section('title', 'Book Categories')

@section('content')
    <livewire:admin.category-manager
        :model-type="\App\Models\BookCategory::class"
        :translation-type="\App\Models\BookCategoryTranslation::class"
        foreign-key="book_category_id"
        count-relation="books"
        page-title="Book Categories" />
@endsection