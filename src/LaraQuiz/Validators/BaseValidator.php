<?php
declare(strict_types=1);

namespace LaraQuiz\Validators;

use Closure;
use Illuminate\Support\Facades\Validator;
use LaraQuiz\Exceptions\CustomValidationException;
use UnexpectedValueException;

/**
 * Class BaseValidator
 *
 * @package LaraQuiz\Validators
 */
abstract class BaseValidator
{
    /**
     * @var \Illuminate\Validation\Validator
     */
    private $validator;

    /**
     * @var array|Closure[]
     */
    protected $customChecks = [];

    /**
     * @var array
     */
    protected $inputData;


    /**
     * @param array $data
     * @return \LaraQuiz\Validators\BaseValidator
     */
    public function setInput(array $data): self
    {
        $this->inputData = $data;
        $this->validator = Validator::make($data, $this->getRules());

        return $this;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return array_keys($this->getRules());
    }

    /**
     * @return bool
     * @throws UnexpectedValueException
     */
    public function validate(): bool
    {
        if ($this->validator === null) {
            throw new UnexpectedValueException('Validator is not set');
        }

        $this->applyCustomChecks();

        return $this->validator->passes();
    }

    /**
     * Tries to pass the validation rules, throws an exception in case of failure
     *
     * @throws CustomValidationException
     * @throws UnexpectedValueException
     * @return void
     */
    public function pass(): void
    {
        if (!$this->validate()) {
            throw new CustomValidationException($this->getValidator());
        }
    }

    /**
     * @param Closure $closure
     * @return void
     */
    public function addCheck(Closure $closure): void
    {
        $this->customChecks[] = $closure;
    }

    /**
     * @return \Illuminate\Validation\Validator
     * @throws UnexpectedValueException
     */
    public function getValidator(): \Illuminate\Validation\Validator
    {
        if ($this->validator === null) {
            throw new UnexpectedValueException('Validator is not set');
        }

        return $this->validator;
    }

    /**
     * @return void
     * @throws UnexpectedValueException
     */
    protected function applyCustomChecks(): void
    {
        if ($this->validator === null) {
            throw new UnexpectedValueException('Validator is not set');
        }

        foreach ($this->customChecks as $closure) {
            $this->validator->after($closure);
        }

        $this->customChecks = [];
    }

    /**
     * @return array
     */
    abstract protected function getRules(): array;
}