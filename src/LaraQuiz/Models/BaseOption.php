<?php
declare(strict_types=1);

namespace LaraQuiz\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use QuizRunner\Contracts\Options\Option;
use QuizRunner\Contracts\Questions\Question;

/**
 * Class BaseOption
 *
 * @property-read QuestionOption $questionOption
 */
abstract class BaseOption extends BaseModel implements Option
{
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
        return $this->questionOption->getId();
    }

    /**
     * @return string
     */
    public function getFeedback(): string
    {
        return $this->questionOption->getFeedback();
    }

    /**
     * @return int
     */
    public function getCorrectness(): int
    {
        return $this->questionOption->getCorrectness();
    }

    /**
     * @return int
     */
    public function getScore(): int
    {
        return $this->questionOption->getScore();
    }

    /**
     * @return Question
     */
    public function getQuestion(): Question
    {
        return $this->questionOption->getQuestion();
    }


    /*
    |--------------------------------------------------------------------------
    |  Relations
    |--------------------------------------------------------------------------
    */

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function questionOption(): BelongsTo
    {
        return $this->belongsTo(QuestionOption::class);
    }
}
