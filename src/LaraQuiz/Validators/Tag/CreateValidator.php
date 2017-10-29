<?php
declare(strict_types=1);

namespace LaraQuiz\Validators\Tag;

use LaraQuiz\Models\Tag;
use LaraQuiz\Validators\BaseValidator;

/**
 * Class CreateValidator
 *
 * @package LaraQuiz\Validators\Tag
 */
class CreateValidator extends BaseValidator
{
    /**
     * @return array
     */
    public function getRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:64', 'unique:' . Tag::tableName() . ',name'],
            'description' => ['string', 'max:191'],
        ];
    }
}