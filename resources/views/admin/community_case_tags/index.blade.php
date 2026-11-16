@extends('layouts.admin.master')

@section('title', 'Community Case Tags')

@section('content')
    <livewire:admin.tag-manager
        :model-type="\App\Models\CommunityCaseTag::class"
        :translation-type="\App\Models\CommunityCaseTagTranslation::class"
        page-title="Community Case Tags" />
@endsection

