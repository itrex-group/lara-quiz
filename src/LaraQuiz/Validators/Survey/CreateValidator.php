<?php
declare(strict_types=1);

namespace LaraQuiz\Validators\Survey;

use LaraQuiz\Models\Survey;
use LaraQuiz\Models\Tag;
use LaraQuiz\Validators\BaseValidator;

/**
 * Class CreateValidator
 *
 * @package LaraQuiz\Validators\Survey
 */
class CreateValidator extends BaseValidator
{
    /**
     * @return array
     */
    public function getRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:191'],
            'description' => ['string', 'max:255'],
            'timeLimit' => ['integer', 'min:0'],
            'status' => ['in:' . implode(',', Survey::$statuses)],
            'tagsIds.*' => ['exists:' . Tag::tableName() . ',id'],
        ];
    }
}