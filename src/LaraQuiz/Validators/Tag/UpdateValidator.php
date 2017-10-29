<?php
declare(strict_types=1);

namespace LaraQuiz\Validators\Tag;

use Illuminate\Validation\Rule;
use LaraQuiz\Models\Tag;
use LaraQuiz\Validators\BaseValidator;

/**
 * Class UpdateValidator
 *
 * @package LaraQuiz\Validators\Tag
 */
class UpdateValidator extends BaseValidator
{
    /**
     * @var Tag
     */
    private $model;


    /**
     * UpdateValidator constructor.
     *
     * @param Tag $model
     */
    public function __construct(Tag $model)
    {
        $this->model = $model;
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:64',
                Rule::unique(Tag::tableName(), 'name')->ignore($this->model->id)
            ],
            'description' => ['string', 'max:191'],
        ];
    }
}