{{-- resources/views/admin/media_summary_tags/index.blade.php --}}
@extends('layouts.admin.master')

@section('title', 'Media Summary Tags')

@section('content')
    <livewire:tag-manager
        :model-type="\App\Models\MediaSummaryTag::class"
        :translation-type="\App\Models\MediaSummaryTagTranslation::class"
        page-title="Media Summary Tags" />
@endsection
