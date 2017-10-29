<?php
declare(strict_types=1);

namespace LaraQuiz\Validators\Answer;

use Closure;
use LaraQuiz\Helpers\MySql;
use LaraQuiz\Models\Question;
use LaraQuiz\Models\QuestionOption;
use LaraQuiz\Validators\BaseValidator;
use QuizRunner\Models\QuestionType;

/**
 * Class OptionValidator
 *
 * @package LaraQuiz\Validators\Answer
 */
class OptionValidator extends BaseValidator
{
    /**
     * @var Question
     */
    protected $question;


    /**
     * OptionValidator constructor.
     *
     * @param Question $question
     */
    public function __construct(Question $question)
    {
        $this->question = $question;
        $this->addCheck($this->getOptionsNumberCheck());
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return [
            'options' => ['required', 'array'],
            'options.*.id' => ['required', 'integer', 'min:1'],
            'options.*.text' => ['string', 'max:' . MySql::MAX_TEXT],
        ];
    }

    /**
     * @return \Closure
     */
    protected function getOptionsNumberCheck(): Closure
    {
        return function ($validator) {
            /** @var \Illuminate\Validation\Validator $validator */
            if (!empty($this->inputData['options'])) {
                $options = $this->inputData['options'];
                if ($this->question->getType()->getId() !== QuestionType::MULTIPLE_CHOICE && count($options) > 1) {
                    $validator->errors()->add('options', 'Only 1 option is allowed.');

                    return;
                }

                if ($this->question->questionOptions()
                        ->whereIn(QuestionOption::tableName() . '.id', array_column($options, 'id'))
                        ->count() !== count($options)) {
                    $validator->errors()->add('options', 'Chosen options were not found.');

                    return;
                }
            }
        };
    }
}