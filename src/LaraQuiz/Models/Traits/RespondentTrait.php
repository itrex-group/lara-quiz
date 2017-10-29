<?php
declare(strict_types=1);

namespace LaraQuiz\Models\Traits;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use LaraQuiz\Models\Survey;

/**
 * Trait RespondentTrait
 *
 * @package LaraQuiz\Models\Traits
 * @property-read \Illuminate\Database\Eloquent\Collection|Survey[] $assignedSurveys
 */
trait RespondentTrait
{
    /*
    |--------------------------------------------------------------------------
    |  Relations
    |--------------------------------------------------------------------------
    */

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function assignedSurveys(): BelongsToMany
    {
        $config = Container::getInstance()->make(Repository::class);

        return $this->belongsToMany(Survey::class,
            $config->get('laraQuiz.tableSurveyUser'),
            $config->get('laraQuiz.userForeignKey'),
            'survey_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    |  Respondent interface
    |--------------------------------------------------------------------------
    */

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->getKey();
    }

    /**
     * @return iterable|\Illuminate\Database\Eloquent\Collection|\QuizRunner\Contracts\Surveys\Survey[]
     */
    public function getAssignedSurveys(): iterable
    {
        // TODO: don't rely on Eloquent model behaviour
        return $this->assignedSurveys;
    }

    /**
     * @param \QuizRunner\Contracts\Surveys\Survey|int $survey
     * @return bool
     */
    public function canPlaySurvey($survey): bool
    {
        // TODO: cache results
        $surveyId = $survey instanceof \QuizRunner\Contracts\Surveys\Survey ? $survey->getId() : $survey;

        // TODO: add "visibility" column to Survey, check if it's public.

        return $this->assignedSurveys()->where(Survey::tableName() . '.id', $surveyId)->exists();
    }


    /*
    |--------------------------------------------------------------------------
    |  \Illuminate\Database\Eloquent\Model methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get the value of the model's primary key.
     *
     * @return mixed|int
     */
    abstract public function getKey();

    /**
     * Define a many-to-many relationship.
     *
     * @param  string $related
     * @param  string|null $table
     * @param  string|null $foreignPivotKey
     * @param  string|null $relatedPivotKey
     * @param  string|null $parentKey
     * @param  string|null $relatedKey
     * @param  string|null $relation
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    abstract public function belongsToMany(
        string $related,
        ?string $table = null,
        ?string $foreignPivotKey = null,
        ?string $relatedPivotKey = null,
        ?string $parentKey = null,
        ?string $relatedKey = null,
        ?string $relation = null
    );
}