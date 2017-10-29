<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create new Question</h3>
    </div>

    <form id="question-form" class="box box-solid" action="{{$createQuestionUrl}}" method="post">
        {{ method_field('post') }}
        {{ csrf_field() }}
        {{ referrer_field() }}

        <div class="box-header with-border">
            <div class="form-group">
                <label for="question">Question:</label>
                <input name="text" id="question" type="text" class="form-control"
                       value="{{old('text')}}" required>
            </div>

            <div class="form-group">
                <label for="type">Question type</label>
                <select name="type" class="form-control select2 select2-hidden-accessible" required>
                    <option value="">Select question type</option>
                    @foreach($questionTypes as $id => $questionType)
                        <option value="{{$id}}">
                            {{$questionType['name']}}
                        </option>
                    @endforeach
                </select>
            </div>

        </div>

        <div id="answers" class="box-body">
            <div data-answer-id="0" class="form-group answer">
                <label class="answer-label" for="answer-0">Answer 1</label>
                <div class="row answer-row">
                    <div class="col-sm-10 input-col">
                        <input name="options[0][title]" id="answer-0" type="text"
                               class="form-control answer-input" required>
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
        </div>

        <div class="box-footer">
            <div class="pull-right">
                <button type="submit" class="btn btn-success">Save</button>
            </div>
        </div>
    </form>
</div>

@section('custom-js')
    <script type="text/javascript" src="{{ URL::asset('js/core/edit-question3.js') }}"></script>
@endsection