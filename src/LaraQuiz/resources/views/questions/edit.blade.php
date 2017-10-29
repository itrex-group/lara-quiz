@extends('layouts.app')

@section('htmlheader_title', $headerText)

@section('main-content')
    @include('partials._messages')

    <div class="container-fluid spark-screen">
        <div class="row">
            <div class="col-md-8">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">{{$headerText}}</h3>
                    </div>

                    <form id="question-form" class="box box-solid" action="{{$formUrl}}" method="post">
                        {{ method_field($httpMethod) }}
                        {{ csrf_field() }}
                        {{ referrer_field() }}

                        <div class="box-header with-border">
                            <div class="form-group">
                                <label for="question">Question:</label>
                                <input name="text" id="question" type="text" class="form-control"
                                       value="{{old('text', $model['text'])}}">
                            </div>

                            <div class="form-group">
                                <label for="type">Question type</label>
                                <select name="type" class="form-control select2 select2-hidden-accessible">
                                    <option value="">Select question type</option>
                                    @foreach($questionTypes as $id => $questionType)
                                        <option value="{{$id}}" @if($model['type']['id'] === $id){{'selected'}}@endif>
                                            {{$questionType['name']}}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div id="answers" class="box-body">
                            @if($options)
                                @foreach($options as $index => $option)
                                    <div data-answer-id="{{$index}}" class="form-group answer">
                                        <label class="answer-label" for="answer-{{$index}}">Answer {{$index + 1}}</label>
                                        <div class="row answer-row">
                                            <div class="col-sm-10 input-col">
                                                <input name="options[{{$index}}][title]" id="answer-{{$index}}" type="text"
                                                       class="form-control answer-input" value="{{$option['title']}}">
                                                <input name="options[{{$index}}][id]" type="hidden" value="{{$option['id']}}">
                                            </div>

                                            @if(!isset($options[$index+1]))
                                                <div class="col-sm-2 btn-col">
                                                    <a class="btn btn-default btn-sm remove-answer">
                                                        <i class="fa fa-times"></i>
                                                    </a>
                                                    <a class="btn btn-default btn-sm add-answer">
                                                        <i class="fa fa-plus-circle"></i>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div data-answer-id="0" class="form-group answer">
                                    <label class="answer-label" for="answer-0">Answer 1</label>
                                    <div class="row answer-row">
                                        <div class="col-sm-10 input-col">
                                            <input name="options[0][title]" id="answer-0" type="text"
                                                   class="form-control answer-input">
                                            <input name="options[0][id]" type="hidden">
                                        </div>
                                        <div class="col-sm-2 btn-col">
                                            <a class="btn btn-default btn-sm remove-answer">
                                                <i class="fa fa-times"></i>
                                            </a>
                                            <a class="btn btn-default btn-sm add-answer">
                                                <i class="fa fa-plus-circle"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="box-footer">
                            <a href="{{$backUrl}}" class="btn btn-default">Cancel</a>
                            <div class="pull-right">
                                <button type="submit" class="btn btn-success">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

@endsection

@section('custom-js')
    <script type="text/javascript" src="{{ URL::asset('js/core/edit-question3.js') }}"></script>
@endsection