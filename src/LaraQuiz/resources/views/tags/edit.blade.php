@extends('layouts.app')

@section('htmlheader_title', $headerText)

@section('main-content')
    @include('partials._messages')

    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-5">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{$headerText}}</h3>
                    </div>
                    <form action="{{$formUrl}}" method="post">
                        {{ method_field($httpMethod) }}
                        {{ csrf_field() }}
                        {{ referrer_field() }}
                        <div class="box-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="name">Title</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                               value="{{old('name', $model['name'])}}" required>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="description">Description</label>
                                        <input type="text" class="form-control" id="description" name="description"
                                               value="{{old('description', $model['description'])}}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box-footer">
                            <a href="{{$backUrl}}" class="btn btn-default">
                                Back to Tags
                            </a>

                            <button type="submit" class="btn btn-primary pull-right">Save</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

@endsection