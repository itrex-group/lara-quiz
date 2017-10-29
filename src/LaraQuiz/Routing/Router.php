<?php
declare(strict_types=1);

namespace LaraQuiz\Routing;

use Illuminate\Support\Facades\Route;

/**
 * Class Router
 *
 * @package LaraQuiz\Routing
 */
class Router
{
    const AS_PREFIX = 'qr::';

    const NAMESPACE = '\LaraQuiz\Http\Controllers\\';


    /**
     * Loads routes that manage Surveys
     *
     * @return void
     */
    public static function loadCms(): void
    {
        // @TODO: create/find issue on Github that we cannot start namespace from root in route group
        Route::group(['as' => self::AS_PREFIX], function () {

            static::loadCmsTags();

            Route::group(['prefix' => 'surveys', 'as' => 'surveys::'], function () {
                Route::get('', ['uses' => self::NAMESPACE . 'SurveysController@index', 'as' => 'index']);
                Route::get('get-rows', ['uses' => self::NAMESPACE . 'SurveysController@getRows', 'as' => 'getRows']);
                Route::get('create', ['uses' => self::NAMESPACE . 'SurveysController@create', 'as' => 'create']);
                Route::post('create',
                    ['uses' => self::NAMESPACE . 'SurveysController@createSubmit', 'as' => 'createSubmit']);
                Route::get('{model}/update',
                    ['uses' => self::NAMESPACE . 'SurveysController@update', 'as' => 'update']);
                Route::put('{model}/update',
                    ['uses' => self::NAMESPACE . 'SurveysController@updateSubmit', 'as' => 'updateSubmit']);
                Route::delete('{model}',
                    ['uses' => self::NAMESPACE . 'SurveysController@deleteSubmit', 'as' => 'deleteSubmit']);

                static::loadCmsQuestions();
            });
        });
    }

    /**
     * @return void
     */
    public static function loadCmsTags(): void
    {
        Route::group(['prefix' => 'tags', 'as' => 'tags::'], function () {
            Route::get('', ['uses' => self::NAMESPACE . 'TagsController@index', 'as' => 'index']);
            Route::get('get-rows', ['uses' => self::NAMESPACE . 'TagsController@getRows', 'as' => 'getRows']);
            Route::get('create', ['uses' => self::NAMESPACE . 'TagsController@create', 'as' => 'create']);
            Route::post('create', ['uses' => self::NAMESPACE . 'TagsController@createSubmit', 'as' => 'createSubmit']);
            Route::get('{model}/update', ['uses' => self::NAMESPACE . 'TagsController@update', 'as' => 'update']);
            Route::put('{model}/update',
                ['uses' => self::NAMESPACE . 'TagsController@updateSubmit', 'as' => 'updateSubmit']);
            Route::delete('{model}',
                ['uses' => self::NAMESPACE . 'TagsController@deleteSubmit', 'as' => 'deleteSubmit']);
        });
    }

    /**
     * @return void
     */
    public static function loadCmsQuestions(): void
    {
        Route::group(['as' => 'questions::'], function () {
            Route::post('{survey}/questions/create',
                ['uses' => self::NAMESPACE . 'QuestionsController@createSubmit', 'as' => 'createSubmit']);
            Route::get('{survey}/questions/{model}/update',
                ['uses' => self::NAMESPACE . 'QuestionsController@update', 'as' => 'update']);
            Route::put('{survey}/questions/{model}/update',
                ['uses' => self::NAMESPACE . 'QuestionsController@updateSubmit', 'as' => 'updateSubmit']);
            Route::delete('questions/{model}',
                ['uses' => self::NAMESPACE . 'QuestionsController@deleteSubmit', 'as' => 'deleteSubmit']);
        });
    }

    /**
     * Loads routes that show Surveys and save users' answers
     *
     * @return void
     */
    public static function loadFront(): void
    {
        Route::group(['prefix' => 'surveys'], function () {
            Route::get('', ['uses' => self::NAMESPACE . 'Front\SurveysController@index']);
            Route::get('{survey}', ['uses' => self::NAMESPACE . 'Front\SurveysController@read']);

            Route::group(['prefix' => 'sessions'], function () {
                Route::patch('{session}/end', ['uses' => self::NAMESPACE . 'Front\SessionsController@end']);
            });

            Route::group(['prefix' => 'answers'], function () {
                Route::post('sessions/{session}/questions/{question}',
                    ['uses' => self::NAMESPACE . 'Front\AnswersController@save']);
            });
        });
    }
}