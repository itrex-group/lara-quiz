@extends('layouts.app')

@section('htmlheader_title', $headerText)

@section('main-content')
    @include('partials._messages')

    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            {{$headerText}}
                        </h3>
                    </div>
                    <form action="{{$formUrl}}" method="post" class="form-horizontal">
                        {{ method_field($httpMethod) }}
                        {{ csrf_field() }}
                        {{ referrer_field() }}
                        <div class="box-body">

                            <div class="form-group">
                                <label for="title" class="col-sm-2 control-label">Survey name</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="title" name="title"
                                           value="{{old('title', $model['title'])}}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="tagsIds" class="col-sm-2 control-label">Tag</label>
                                <div class="col-sm-9">
                                    <select class="form-control" name="tagsIds[]" id="tagsIds">
                                        <option value="">- Choose tag -</option>
                                        @foreach($tags as $tag)
                                            <option value="{{$tag['id']}}" @if(in_array($tag['id'], $surveyTagsIds)){{'selected'}}@endif>
                                                {{$tag['name']}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-1">
                                    <a class="btn btn-info btn-xs" href="{{$createTagUrl}}">
                                        <i class="fa fa-plus-square" aria-hidden="true"></i>
                                    </a>
                                </div>

                            </div>

                            <div class="form-group">
                                <label for="description" class="col-sm-2 control-label">Description</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="description" name="description"
                                           value="{{old('description', $model['description'])}}">
                                </div>
                            </div>
                        </div>

                        <div class="box-footer">
                            <a href="{{$backUrl}}" class="btn btn-default">
                                Back to Survey list
                            </a>

                            <button type="submit" class="btn btn-success pull-right">{{$submitText}}</button>
                        </div>
                    </form>
                </div>
            </div>

            @if(isset($createQuestionPartial))
                <div class="col-md-6">
                    @include($createQuestionPartial)
                </div>
            @endif
        </div>


        @if($questions)
            <div class="row">
                <div class="col-md-6">
                    @foreach($questions as $index => $question)
                        <div class="box box-solid">
                            <div class="box-header with-border">
                                <h4 class="box-title">{{$index + 1}}. {{$question['text']}}</h4>
                                <div class="pull-right">
                                    <a href="{{$question['updateQuestionUrl']}}" class="btn btn-default btn-sm">
                                        <i class="fa fa-pencil-square-o"></i>
                                    </a>
                                    <a class="btn btn-default btn-sm"
                                       onclick="event.preventDefault();$('#delete-form').attr('action', '{{$question['deleteQuestionUrl']}}').submit();">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="box-body">
                                <ol>
                                    @foreach($question['options'] as $option)
                                        @if($question['type']['key'] === 'SingleChoice')
                                            <div class="radio disabled">
                                                <label>
                                                    <input type="radio" disabled>
                                                    {{$option['title']}}
                                                </label>
                                            </div>
                                        @else
                                            <div class="checkbox disabled">
                                                <label>
                                                    <input type="checkbox" disabled>
                                                    {{$option['title']}}
                                                </label>
                                            </div>
                                        @endif
                                    @endforeach
                                </ol>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    @include('partials._delete_form')

@endsection