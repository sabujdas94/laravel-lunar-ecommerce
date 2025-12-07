@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-3xl font-bold mb-4">{{ $page->title }}</h1>

    <div class="prose">
        {!! $page->content !!}
    </div>
</div>
@endsection
