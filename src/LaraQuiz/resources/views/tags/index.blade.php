@extends('layouts.app')

@section('htmlheader_title')
    {!! $headerText = 'Tags'!!}
@endsection

@section('main-content')

    @include('partials._messages')

    @include('partials.datatables.html')

@endsection

@section('custom-js')

    <script>
        endpointUrl = '{{$getRowsUrl}}';
    </script>
    @include('partials.datatables.js')

@endsection