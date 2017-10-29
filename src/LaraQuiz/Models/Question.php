<?php
declare(strict_types=1);

namespace LaraQuiz\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use LogicException;
use QuizRunner\Contracts\Questions\OptionsQuestion;
use QuizRunner\Contracts\Questions\QuestionType;

/**
 * @property int $id
 * @property int $type
 * @property string $text
 * @property string $hint
 * @property string $feedback
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|Survey[] $surveys
 * @property-read \Illuminate\Database\Eloquent\Collection|QuestionOption[] $questionOptions
 */
class Question extends BaseModel implements OptionsQuestion
{
    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * @var array
     */
    protected $casts = [
        'id' => 'int',
        'type' => 'int',
    ];

    /**
     * @var \QuizRunner\Models\QuestionType
     */
    private $questionType;


    /**
     * @return string
     */
    public static function tableName(): string
    {
        return config('laraQuiz.tableQuestions');
    }


    /*
    |--------------------------------------------------------------------------
    |  Question interface
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
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return QuestionType
     */
    public function getType(): QuestionType
    {
        if ($this->type === null) {
            throw new LogicException('Type has not been set yet.');
        }

        $this->questionType = \QuizRunner\Models\QuestionType::make($this->type);

        return $this->questionType;
    }

    /**
     * @return string
     */
    public function getHint(): string
    {
        return $this->hint;
    }

    /**
     * @return string
     */
    public function getFeedback(): string
    {
        return $this->feedback;
    }

    /**
     * @return iterable|\Illuminate\Database\Eloquent\Collection|\QuizRunner\Contracts\Surveys\Survey[]
     */
    public function getSurveys(): iterable
    {
        return $this->surveys;
    }

    /**
     * @return iterable|\Illuminate\Database\Eloquent\Collection|\QuizRunner\Contracts\Options\Option[]
     */
    public function getOptions(): iterable
    {
        return $this->questionOptions;
    }


    /*
    |--------------------------------------------------------------------------
    |  Relations
    |--------------------------------------------------------------------------
    */

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function surveys(): BelongsToMany
    {
        return $this->belongsToMany(Survey::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function questionOptions(): HasMany
    {
        return $this->hasMany(QuestionOption::class);
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
    public function getHintAttribute(?string $value): string
    {
        return $value ?? '';
    }

    /**
     * @param string|null $value
     * @return string
     */
    public function getFeedbackAttribute(?string $value): string
    {
        return $value ?? '';
    }
}
