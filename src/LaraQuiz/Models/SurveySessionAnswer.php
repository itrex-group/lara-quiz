<?php
declare(strict_types=1);

namespace LaraQuiz\Models;

use DateTime;
use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use QuizRunner\Contracts\Answers\Answer;
use QuizRunner\Contracts\Answers\MultipleChoiceAnswer;
use QuizRunner\Contracts\Answers\SingleChoiceAnswer;
use QuizRunner\Contracts\Answers\TextAnswer;
use QuizRunner\Contracts\Options\Option;
use QuizRunner\Contracts\Questions\Question;
use QuizRunner\Contracts\Sessions\Session;

/**
 * @property int $id
 * @property int $survey_session_id
 * @property int $question_id
 * @property \Carbon\Carbon $created_at
 *
 * @property-read Session $session
 * @property-read Question $question
 * @property-read SessionAnswerValue|null $answerValue
 * @property-read \Illuminate\Database\Eloquent\Collection|QuestionOption[] $questionOptions
 */
class SurveySessionAnswer extends BaseModel implements Answer, TextAnswer, SingleChoiceAnswer, MultipleChoiceAnswer
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
        'survey_session_id' => 'int',
        'question_id' => 'int',
    ];


    /**
     * @return string
     */
    public static function tableName(): string
    {
        return Container::getInstance()->make(Repository::class)->get('laraQuiz.tableSurveySessionAnswers');
    }


    /*
    |--------------------------------------------------------------------------
    |  Answer interface
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
     * @return Question
     */
    public function getQuestion(): Question
    {
        return $this->question;
    }

    /**
     * @return Session
     */
    public function getSession(): Session
    {
        return $this->session;
    }

    /**
     * @return iterable|\QuizRunner\Contracts\Options\Option[]
     */
    public function getOptions(): iterable
    {
        return $this->questionOptions;
    }

    /**
     * @return Option
     */
    public function getOption(): Option
    {
        return $this->questionOptions->first();
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->answerValue->value;
    }

    /*
    |--------------------------------------------------------------------------
    |  Relations
    |--------------------------------------------------------------------------
    */

    /**
     * @return BelongsTo
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(\LaraQuiz\Models\Question::class, 'question_id');
    }

    /**
     * @return BelongsTo
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(\LaraQuiz\Models\SurveySession::class, 'survey_session_id');
    }

    /**
     * @return HasOne
     */
    public function answerValue(): HasOne
    {
        return $this->hasOne(SessionAnswerValue::class, 'survey_session_answer_id');
    }

    /**
     * @return BelongsToMany
     */
    public function questionOptions(): BelongsToMany
    {
        $config = Container::getInstance()->make(Repository::class);

        return $this->belongsToMany(
            QuestionOption::class,
            $config->get('laraQuiz.tableQuestionOptionSurveySessionAnswer'),
            'survey_session_answer_id',
            'question_option_id'
        )->withPivot('text');
    }
}
