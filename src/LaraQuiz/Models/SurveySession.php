<?php
declare(strict_types=1);

namespace LaraQuiz\Models;

use DateTime;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use QuizRunner\Contracts\Sessions\Session;
use QuizRunner\Contracts\Surveys\Survey;
use QuizRunner\Contracts\Users\Respondent;

/**
 * @property int $id
 * @property int $survey_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $ended_at
 *
 * @property-read Survey $survey
 * @property-read Respondent $user
 * @property-read \Illuminate\Database\Eloquent\Collection|SurveySessionAnswer[] $answers
 */
class SurveySession extends BaseModel implements Session
{
    const UPDATED_AT = null;


    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at'];

    /**
     * @var array
     */
    protected $casts = [
        'id' => 'int',
        'survey_id' => 'int',
    ];

    /**
     * @var array
     */
    protected $dates = ['created_at', 'ended_at'];


    /**
     * @return string
     */
    public static function tableName(): string
    {
        return Container::getInstance()->make(Repository::class)->get('laraQuiz.tableSurveySessions');
    }


    /*
    |--------------------------------------------------------------------------
    |  Session interface
    |--------------------------------------------------------------------------
    */

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return DateTime|\Carbon\Carbon
     */
    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }

    /**
     * @return DateTime|\Carbon\Carbon
     */
    public function getEndedAt(): DateTime
    {
        return $this->ended_at;
    }

    /**
     * @return Respondent
     */
    public function getRespondent(): Respondent
    {
        return $this->user;
    }

    /**
     * @return Survey
     */
    public function getSurvey(): Survey
    {
        return $this->survey;
    }

    /**
     * @return iterable
     */
    public function getAnswers(): iterable
    {
        return $this->answers;
    }


    /*
    |--------------------------------------------------------------------------
    |  Relations
    |--------------------------------------------------------------------------
    */

    /**
     * @return BelongsTo
     */
    public function survey(): BelongsTo
    {
        return $this->belongsTo(\LaraQuiz\Models\Survey::class, 'survey_id');
    }

    /**
     * @return BelongsTo
     */
    public function user(): belongsTo
    {
        $config = Container::getInstance()->make(Repository::class);

        return $this->belongsTo($config->get('laraQuiz.modelUser'), $config->get('laraQuiz.userForeignKey'));
    }

    /**
     * @return HasMany
     */
    public function answers(): HasMany
    {
        return $this->hasMany(SurveySessionAnswer::class, 'survey_session_id');
    }
}
