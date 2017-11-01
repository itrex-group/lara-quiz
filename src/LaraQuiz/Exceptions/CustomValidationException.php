<?php
declare(strict_types=1);

namespace LaraQuiz\Exceptions;

use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Validator;
use LaraQuiz\Helpers\Api;

/**
 * Class CustomValidationException
 *
 * @package LaraQuiz\Exceptions
 */
class CustomValidationException extends ValidationException
{
    /**
     * @param \Illuminate\Validation\Validator $validator
     * @param \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|null $response
     */
    public function __construct(Validator $validator, $response = null)
    {
        if ($response === null) {
            $response = Api::respondBadRequest($validator->errors()->all());
        }

        parent::__construct($validator, $response);
    }
}