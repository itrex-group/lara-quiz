<?php
declare(strict_types=1);

namespace LaraQuiz\Http\Controllers\Front;

use Illuminate\Container\Container;
use Illuminate\Contracts\Auth\Guard;
use QuizRunner\Contracts\Users\Respondent;

/**
 * Class BaseFrontController
 *
 * @package LaraQuiz\Http\Controllers\Front
 */
class BaseFrontController extends ApiController
{
    /**
     * @return null|Respondent
     */
    protected function getCurrentRespondent(): ?Respondent
    {
        /** @var Guard $auth */
        $auth = Container::getInstance()->make(Guard::class);

        return $auth->user();
    }
}