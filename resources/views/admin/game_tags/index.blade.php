@extends('layouts.admin.master')

@section('title', 'Game Tags - Shadow Files')

@section('content')
    <livewire:admin.tag-manager
        :model-type="\App\Models\GameTag::class"
        :translation-type="\App\Models\GameTagTranslation::class"
        page-title="Game Tags" />

@endsection