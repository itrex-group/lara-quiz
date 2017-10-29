<?php
declare(strict_types=1);

namespace LaraQuiz\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use LaraQuiz\Models\IntervalOption;
use LaraQuiz\Models\ListOption;
use LaraQuiz\Models\Question;
use LaraQuiz\Models\QuestionOption;
use LaraQuiz\Models\Survey;
use LaraQuiz\Routing\Router;
use LaraQuiz\Validators\Question\CreateValidator;
use QuizRunner\Models\QuestionType;
use QuizRunner\Transformers\QuestionTransformerManager;

/**
 * Class QuestionsController
 *
 * @package LaraQuiz\Http\Controllers
 */
class QuestionsController extends BaseController
{
    const PREFIX = 'questions';


    /**
     * @var string
     */
    protected $globalPrefix;


    /**
     * TagsController constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->globalPrefix = config('laraQuiz.routesPrefix');
    }

    /**
     * @param \QuizRunner\Transformers\QuestionTransformerManager $questionTransformer
     * @param Survey $survey
     * @param Question $model
     * @return \Illuminate\View\View
     */
    public function update(QuestionTransformerManager $questionTransformer, Survey $survey, Question $model): View
    {
        return view(self::VIEWS_PREFIX . self::PREFIX . '.edit', array_merge([
            'backUrl' => route($this->globalPrefix . Router::AS_PREFIX . SurveysController::PREFIX . '::update',
                ['id' => $survey->id]),
            'httpMethod' => 'put',
            'headerText' => 'Update Question',
            'formUrl' => route($this->globalPrefix . Router::AS_PREFIX . SurveysController::PREFIX . '::' . self::PREFIX . '::updateSubmit',
                ['model' => $model['id'], 'survey' => $survey])
        ], $this->getVariables($questionTransformer, $model)));
    }

    /**
     * @param \QuizRunner\Transformers\QuestionTransformerManager $questionTransformer
     * @param \LaraQuiz\Models\Question $model
     * @return array
     */
    protected function getVariables(QuestionTransformerManager $questionTransformer, Question $model): array
    {
        $modelData = $questionTransformer->transform($model);

        return [
            'model' => $modelData,
            'questionTypes' => QuestionType::$types,
            'options' => $modelData['options'] ?? [],
        ];
    }

    /**
     * @param Request $request
     * @param Survey $survey
     * @param Question $model
     * @return RedirectResponse
     */
    public function createSubmit(Request $request, Survey $survey, Question $model): RedirectResponse
    {
        return $this->save($request, $model, $survey);
    }

    /**
     * @param Request $request
     * @param Survey $survey
     * @param Question $model
     * @return RedirectResponse
     */
    public function updateSubmit(Request $request, Survey $survey, Question $model): RedirectResponse
    {
        return $this->save($request, $model, $survey);
    }

    /**
     * @param Question $model
     * @return RedirectResponse
     */
    public function deleteSubmit(Question $model): RedirectResponse
    {
        $model->delete();

        return $this->redirectToReferrer()->with('message', 'Item has been successfully removed.');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \LaraQuiz\Models\Question $model
     * @param \LaraQuiz\Models\Survey $survey
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function save(Request $request, Question $model, Survey $survey): RedirectResponse
    {
        $validator = (new CreateValidator())->setInput($request->input());

        if (!$validator->validate()) {
            return $this->redirectToReferrer()->withInput()->withErrors($validator->getValidator());
        }

        $model->type = (int)$request->input('type', $model->type);
        $model->text = $request->input('text', $model->text ?? '');
        $model->hint = $request->input('hint', $model->hint ?? '');
        $model->feedback = $request->input('feedback', $model->feedback ?? '');
        $model->save();

        $model->surveys()->syncWithoutDetaching([$survey->id]);

        if ($model->getType()->getGroup() === QuestionType::GROUP_OPTIONS) {
            $optionIds = [];
            foreach ($request->input('options') as $index => $optionData) {
                if (!empty($optionData['id'])) {
                    $option = QuestionOption::find($optionData['id']);
                } else {
                    $option = new QuestionOption();
                }

                $option->question()->associate($model);
                $option->correctness = $optionData['correctness'] ?? $option->correctness;
                $option->score = $optionData['score'] ?? $option->score;
                $option->feedback = $optionData['feedback'] ?? $option->feedback;
                $option->save();
                $optionIds[] = $option->id;

                if (in_array($model->type, QuestionType::$listTypes, true)) {
                    $item = $option->listOption ?? new ListOption();
                    $item->title = $optionData['title'];
                    $item->order = $optionData['order'] ?? $item->order ?? $index;
                } else {
                    $item = $option->intervalOption ?? new IntervalOption();
                    $item->min_value = $optionData['minValue'];
                    $item->max_value = $optionData['maxValue'];
                }

                $item->questionOption()->associate($option);
                $item->save();
            }

            // @TODO: add check if these options are used as responses from users
            $model->questionOptions()->whereNotIn(QuestionOption::tableName() . '.id', $optionIds)->delete();
        }

        if ($model->wasRecentlyCreated) {
            return $this->redirectToReferrer()->with('message', 'Item has been successfully saved.');
        }

        return redirect()
            ->route($this->globalPrefix . Router::AS_PREFIX . SurveysController::PREFIX . '::update',
                ['model' => $survey])
            ->with('message', 'Item has been successfully saved.');
    }
}