<?php
declare(strict_types=1);

namespace LaraQuiz\Validators\Question;

use LaraQuiz\Helpers\MySql;
use LaraQuiz\Validators\BaseValidator;
use QuizRunner\Models\QuestionType;

/**
 * Class CreateValidator
 *
 * @package LaraQuiz\Validators\Question
 */
class CreateValidator extends BaseValidator
{
    /**
     * @return array
     */
    public function getRules(): array
    {
        return [
            //'surveyId' => ['bail', 'required', 'integer', 'min:1', 'exists:' . Survey::tableName() . ',id'],
            'type' => ['required', 'in:' . implode(',', array_keys(QuestionType::$types))],
            'text' => ['required', 'string', 'max:255'],
            'hint' => ['string', 'max:255'],
            'feedback' => ['string', 'max:255'],
            'options' => [
                'required_if:type,' . implode(',', array_keys(QuestionType::findByGroup(QuestionType::GROUP_OPTIONS))),
                'array'
            ],

            // @see \QuizRunner\Contracts\Options\ListOption
            'options.*.title' => [
                'required_if:type,' . implode(',', QuestionType::$listTypes),
                'string',
                'max:191'
            ],
            'options.*.order' => ['integer', 'min:' . MySql::MIN_SMALLINT, 'max:' . MySql::MAX_SMALLINT],

            // @see \QuizRunner\Contracts\Options\IntervalOption
            'options.*.minValue' => [
                'required_if:type,' . QuestionType::SLIDER,
                'integer',
                'min:' . MySql::MIN_SMALLINT,
                'max:' . MySql::MAX_SMALLINT
            ],
            'options.*.maxValue' => [
                'required_if:type,' . QuestionType::SLIDER,
                'integer',
                'min:' . MySql::MIN_SMALLINT,
                'max:' . MySql::MAX_SMALLINT
            ],
        ];
    }
}