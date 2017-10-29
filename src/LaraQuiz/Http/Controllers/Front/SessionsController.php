<?php
declare(strict_types=1);

namespace LaraQuiz\Http\Controllers\Front;

use Carbon\Carbon;
use LaraQuiz\Models\SurveySession;
use QuizRunner\Contracts\Users\Respondent;

/**
 * Class SessionsController
 *
 * @package LaraQuiz\Http\Controllers\Front
 */
class SessionsController extends BaseFrontController
{
    /**
     * @param SurveySession $session
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function end(SurveySession $session)
    {
        $respondent = $this->getCurrentRespondent();

        if (!($respondent instanceof Respondent) || !$respondent->canPlaySurvey($session->getSurvey())) {
            return $this->respondForbidden('You are not allowed to play this Survey');
        }

        if ($session->ended_at !== null) {
            return $this->respondBadRequest('Session was already ended.');
        }

        $session->ended_at = Carbon::now();
        $session->save();

        return $this->respondSuccess();
    }
}
