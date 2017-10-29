<?php
declare(strict_types=1);

namespace LaraQuiz\Models;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $survey_session_answer_id
 * @property string value
 *
 * @property-read SurveySessionAnswer $answer
 */
class SessionAnswerValue extends BaseModel
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @var array
     */
    protected $casts = [
        'id' => 'int',
        'survey_session_answer_id' => 'int',
    ];


    /**
     * @return string
     */
    public static function tableName(): string
    {
        return Container::getInstance()->make(Repository::class)->get('laraQuiz.tableSessionAnswerValues');
    }


    /*
    |--------------------------------------------------------------------------
    |  Relations
    |--------------------------------------------------------------------------
    */

    /**
     * @return BelongsTo
     */
    public function answer(): BelongsTo
    {
        return $this->belongsTo(SurveySessionAnswer::class, 'survey_session_answer_id');
    }
}
