<?php
declare(strict_types=1);

namespace LaraQuiz\Models;

/**
 * @property int $id
 * @property int $question_option_id
 * @property int $order
 * @property bool $is_input
 * @property string $title
 *
 * @property-read QuestionOption $questionOption
 */
class ListOption extends BaseOption implements \QuizRunner\Contracts\Options\ListOption
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
        'order' => 'int',
        'is_input' => 'bool',
    ];


    /**
     * @return string
     */
    public static function tableName(): string
    {
        return config('laraQuiz.tableListOptions');
    }


    /*
    |--------------------------------------------------------------------------
    |  ListOption interface
    |--------------------------------------------------------------------------
    */

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @return bool
     */
    public function isInput(): bool
    {
        return $this->is_input;
    }
}
