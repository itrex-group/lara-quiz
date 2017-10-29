<?php
declare(strict_types=1);

namespace LaraQuiz\Models;

/**
 * @property int $id
 * @property int $question_option_id
 * @property int $min_value
 * @property int $max_value
 *
 * @property-read QuestionOption $questionOption
 */
class IntervalOption extends BaseOption implements \QuizRunner\Contracts\Options\IntervalOption
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
        'question_option_id' => 'int',
        'min_value' => 'int',
        'max_value' => 'int',
    ];


    /**
     * @return string
     */
    public static function tableName(): string
    {
        return config('laraQuiz.tableIntervalOptions');
    }


    /*
    |--------------------------------------------------------------------------
    |  IntervalOption interface
    |--------------------------------------------------------------------------
    */

    /**
     * @return int
     */
    public function getMinValue(): int
    {
        return $this->min_value;
    }

    /**
     * @return int
     */
    public function getMaxValue(): int
    {
        return $this->max_value;
    }
}
