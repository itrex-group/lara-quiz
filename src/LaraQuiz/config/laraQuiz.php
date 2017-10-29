<?php
declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Admin panel routes prefix
    |--------------------------------------------------------------------------
    |
    | Use this value if you need to load default package's routes inside your routes group. Eg cms::, admin::, or any
    | other prefix.
    */

    'routesPrefix' => '',

    /*
    |--------------------------------------------------------------------------
    | Tables names
    |--------------------------------------------------------------------------
    |
    | Change LaraQuiz tables names as you see fit.
    |
    */

    'tableQuestions' => 'questions',
    'tableQuestionOptions' => 'question_options',
    'tableListOptions' => 'list_options',
    'tableIntervalOptions' => 'interval_options',
    'tableSurveys' => 'surveys',
    'tableTags' => 'tags',
    'tableSurveyTag' => 'survey_tag',
    'tableQuestionSurvey' => 'question_survey',

    'tableSurveySessions' => 'survey_sessions',
    'tableSurveySessionAnswers' => 'survey_session_answers',
    'tableSessionAnswerValues' => 'session_answer_values',
    'tableQuestionOptionSurveySessionAnswer' => 'question_option_survey_session_answer',

    /*
    |--------------------------------------------------------------------------
    | User settings
    |--------------------------------------------------------------------------
    |
    | Here you can set up model, table and foreign key that represent User, who can play surveys, in your system.
    |
    */

    'tableSurveyUser' => 'survey_user',
    'userForeignKey' => 'user_id',
    'tableUser' => 'users',
    'modelUser' => '\App\User',

    /*
    |--------------------------------------------------------------------------
    | Default option manager
    |--------------------------------------------------------------------------
    |
    | In most cases you don't need to change this value. It's class that will transform questions' options objects to
    | appropriate API response.
    */

    'optionTransformerManager' => \LaraQuiz\Transformers\OptionTransformerManager::class,

    /*
    |--------------------------------------------------------------------------
    | Transformers Namespaces
    |--------------------------------------------------------------------------
    |
    | Here you can overwrite transformers that should be used by transformers managers. Order matters.
    */

    'transformersNamespaces' => []

];
