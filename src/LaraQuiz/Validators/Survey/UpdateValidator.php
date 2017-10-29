<?php
declare(strict_types=1);

namespace LaraQuiz\Validators\Survey;

use LaraQuiz\Models\Survey;
use LaraQuiz\Validators\BaseValidator;

/**
 * Class UpdateValidator
 *
 * @package LaraQuiz\Validators\Survey
 */
class UpdateValidator extends BaseValidator
{
    /**
     * @return array
     */
    public function getRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:191'],
            'description' => ['string', 'max:255'],
            'timeLimit' => ['integer'],
            'status' => ['in:' . implode(',', Survey::$statuses)],
        ];
    }
}