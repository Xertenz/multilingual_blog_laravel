@extends('layouts.admin.master')

@section('content')
<livewire:admin.tag-manager
    :model-type="\App\Models\BookTag::class"
    :translation-type="\App\Models\BookTagTranslation::class"
    page-title="Book Tags" />
@endsection