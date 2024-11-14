@extends('filament::layouts.app')

@section('content')
    <div class="flex flex-wrap -mx-4">
        @foreach ($widgets as $widget)
            <div class="w-full md:w-1/3 px-4 mb-4">
                @livewire($widget)
            </div>
        @endforeach
    </div>
@endsection
