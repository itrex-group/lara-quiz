<?php
declare(strict_types=1);

namespace LaraQuiz\Http\Controllers\Front;

use Illuminate\Http\JsonResponse;
use LaraQuiz\Models\Survey;
use LaraQuiz\Models\SurveySession;
use QuizRunner\Contracts\Sessions\Session;
use QuizRunner\Contracts\Users\Respondent;
use QuizRunner\Transformers\QuestionTransformerManager;
use QuizRunner\Transformers\SurveyTransformer;

/**
 * Class SurveysController
 *
 * @package LaraQuiz\Http\Controllers\Front
 */
class SurveysController extends BaseFrontController
{
    /**
     * @param SurveyTransformer $transformer
     * @return JsonResponse
     */
    public function index(SurveyTransformer $transformer): JsonResponse
    {
        $q = Survey::query()->where('status', Survey::STATUS_ACTIVE);

        return $this->respondWithPaginatedItems($transformer->transformCollection($this->paginate($q)->get()));
    }

    /**
     * @param SurveyTransformer $transformer
     * @param QuestionTransformerManager $questionManager
     * @param Survey $survey
     * @return JsonResponse
     */
    public function read(
        SurveyTransformer $transformer,
        QuestionTransformerManager $questionManager,
        Survey $survey
    ): JsonResponse {
        $respondent = $this->getCurrentRespondent();

        if (!($respondent instanceof Respondent) || !$respondent->canPlaySurvey($survey)) {
            return $this->respondForbidden('You are not allowed to play this Survey');
        }

        $transformer->with(['questions'])->setQuestionTransformerManager($questionManager);

        // TODO: should we continue previous session? If yes, load answers that have been added so far.
        $session = $this->startSession($respondent, $survey);

        return $this->respondWithItem($transformer->transform($survey), [
            'sessionId' => $session->getId()
        ]);
    }

    /**
     * @param Respondent $respondent
     * @param Survey $survey
     * @return Session
     */
    protected function startSession(Respondent $respondent, Survey $survey): Session
    {
        $session = new SurveySession();
        $session->survey()->associate($survey);
        $session->user()->associate($respondent->getId());
        $session->save();

        return $session;
    }
}
