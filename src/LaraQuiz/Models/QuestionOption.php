<?php
declare(strict_types=1);

namespace LaraQuiz\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use QuizRunner\Contracts\Options\Option;

/**
 * @property int $id
 * @property int $question_id
 * @property int $correctness
 * @property int $score
 * @property string $feedback
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read \QuizRunner\Contracts\Questions\Question|Question $question
 * @property-read null|ListOption $listOption
 * @property-read null|IntervalOption $intervalOption
 */
final class QuestionOption extends BaseModel implements Option
{
    const CORRECTNESS_NEUTRAL = 0;
    const CORRECTNESS_CORRECT = 1;
    const CORRECTNESS_INCORRECT = 2;


    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * @var array
     */
    protected $casts = [
        'id' => 'int',
        'question_id' => 'int',
        'correctness' => 'int',
        'score' => 'int',
    ];


    /**
     * @return string
     */
    public static function tableName(): string
    {
        return config('laraQuiz.tableQuestionOptions');
    }


    /*
    |--------------------------------------------------------------------------
    |  Option interface
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
     * @return string
     */
    public function getFeedback(): string
    {
        return $this->feedback;
    }

    /**
     * @return int
     */
    public function getCorrectness(): int
    {
        return $this->correctness;
    }

    /**
     * @return int
     */
    public function getScore(): int
    {
        return $this->score;
    }

    /**
     * @return \QuizRunner\Contracts\Questions\Question|Question
     */
    public function getQuestion(): \QuizRunner\Contracts\Questions\Question
    {
        return $this->question;
    }


    /*
    |--------------------------------------------------------------------------
    |  Relations
    |--------------------------------------------------------------------------
    */

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * @return HasOne
     */
    public function listOption(): HasOne
    {
        return $this->hasOne(ListOption::class);
    }

    /**
     * @return HasOne
     */
    public function intervalOption(): HasOne
    {
        return $this->hasOne(IntervalOption::class);
    }


    /*
    |--------------------------------------------------------------------------
    |  Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * @param string|null $value
     * @return string
     */
    public function getFeedbackAttribute(?string $value): string
    {
        return $value ?? '';
    }

    /**
     * @param int|null $value
     * @return int
     */
    public function getCorrectnessAttribute(?int $value): int
    {
        return $value ?? QuestionOption::CORRECTNESS_NEUTRAL;
    }

    /**
     * @param int|null $value
     * @return int
     */
    public function getScoreAttribute(?int $value): int
    {
        return $value ?? 0;
    }
}
