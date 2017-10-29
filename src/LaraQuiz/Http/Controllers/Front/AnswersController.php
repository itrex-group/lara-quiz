<?php
declare(strict_types=1);

namespace LaraQuiz\Http\Controllers\Front;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use LaraQuiz\Exceptions\CustomValidationException;
use LaraQuiz\Helpers\Logger;
use LaraQuiz\Models\Question;
use LaraQuiz\Models\Survey;
use LaraQuiz\Models\SurveySession;
use LaraQuiz\Models\SurveySessionAnswer;
use LaraQuiz\Validators\Answer\OptionValidator;
use QuizRunner\Contracts\Users\Respondent;
use QuizRunner\Models\QuestionType;
use Throwable;

/**
 * Class AnswersController
 *
 * @package LaraQuiz\Http\Controllers\Front
 */
class AnswersController extends BaseFrontController
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \LaraQuiz\Models\SurveySession $session
     * @param \LaraQuiz\Models\Question $question
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @throws \LaraQuiz\Exceptions\CustomValidationException
     * @throws \Throwable
     */
    public function save(Request $request, SurveySession $session, Question $question)
    {
        $respondent = $this->getCurrentRespondent();

        if (!($respondent instanceof Respondent) || !$respondent->canPlaySurvey($session->getSurvey())) {
            return $this->respondForbidden('You are not allowed to play this Survey');
        }

        if ($session->ended_at !== null) {
            return $this->respondBadRequest('Session was already ended.');
        }

        if (!$question->surveys()->where(Survey::tableName() . '.id', $session->survey_id)->exists()) {
            return $this->respondBadRequest('Question does not belong to this survey.');
        }

        DB::beginTransaction();

        $session->answers()->where('question_id', $question->id)->delete(); // delete previous answer on this question

        try {
            if ($question->getType()->getGroup() === QuestionType::GROUP_OPTIONS) {
                (new OptionValidator($question))->setInput($request->input())->pass();

                $options = $request->input('options');

                $answer = new SurveySessionAnswer();
                $answer->session()->associate($session);
                $answer->question()->associate($question);
                $answer->save();

                $answer->questionOptions()->attach(array_column($options, 'id'));

                // TODO: save attached to option free text input, if option allows that. DB is already prepared for that.
            } else {
                // TODO: implement saving for other question types. DB is already prepared for that.
            }

            DB::commit();
        } catch (Throwable $e) {
            DB::rollBack();

            if (!$e instanceof CustomValidationException) {
                Logger::exception($e);

                return $this->respondServerError();
            }

            throw $e;
        }

        return $this->respondSuccess();
    }
}
