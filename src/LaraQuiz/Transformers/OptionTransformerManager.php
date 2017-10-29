<?php
declare(strict_types=1);

namespace LaraQuiz\Transformers;

use QuizRunner\Transformers\Transformer;

/**
 * Class OptionTransformerManager
 *
 * @package QuizRunner\Transformers
 */
class OptionTransformerManager extends Transformer
{
    /**
     * @var Transformer[]
     */
    private $transformers = [];


    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     * @param \QuizRunner\Contracts\Options\Option|\LaraQuiz\Models\QuestionOption $item
     * @return array
     */
    public function transform($item): array
    {
        // @TODO: remove if-else, make it more automatically
        if ($item->listOption !== null) {
            $name = 'ListOption';
            $option = $item->listOption;
        } else {
            $name = 'IntervalOption';
            $option = $item->intervalOption;
        }

        $transformer = $this->transformers[$name] ?? null;

        if ($transformer === null) {
            $class = '\QuizRunner\Transformers\\' . $name . 'Transformer';
            $transformer = new $class;
            $this->transformers[$name] = $transformer;
        }

        return $transformer->transform($option);
    }
}