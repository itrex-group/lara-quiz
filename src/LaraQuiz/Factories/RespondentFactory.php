<?php
declare(strict_types=1);

namespace LaraQuiz\Factories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use QuizRunner\Contracts\Users\Respondent;
use ReflectionClass;

/**
 * Class RespondentFactory
 *
 * @package LaraQuiz\Factories
 */
class RespondentFactory
{
    /**
     * @var string
     */
    protected $model;


    /**
     * RespondentFactory constructor.
     *
     * @param string $model
     * @throws \InvalidArgumentException
     */
    public function __construct(string $model)
    {
        $reflection = new ReflectionClass($model);

        if (!$reflection->isSubclassOf(Model::class)) {
            throw new InvalidArgumentException('Model must extend ' . Model::class . ' class.');
        }

        if (!$reflection->implementsInterface(Respondent::class)) {
            throw new InvalidArgumentException('Model must implement ' . Respondent::class . ' interface.');
        }

        $this->model = $model;
    }

    /**
     * @return Builder
     */
    public function query(): Builder
    {
        return $this->model::query();
    }
}