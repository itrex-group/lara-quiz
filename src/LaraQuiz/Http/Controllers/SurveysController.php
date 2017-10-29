<?php
declare(strict_types=1);

namespace LaraQuiz\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use LaraQuiz\Helpers\Datatable;
use LaraQuiz\Models\Survey;
use LaraQuiz\Models\Tag;
use LaraQuiz\Routing\Router;
use LaraQuiz\Validators\Survey\CreateValidator;
use LaraQuiz\Validators\Survey\UpdateValidator;
use QuizRunner\Models\QuestionType;
use QuizRunner\Transformers\QuestionTransformerManager;

/**
 * Class SurveysController
 *
 * @package LaraQuiz\Http\Controllers
 */
class SurveysController extends BaseController
{
    const PREFIX = 'surveys';


    /**
     * @var string
     */
    protected $globalPrefix;


    /**
     * SurveysController constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->globalPrefix = config('laraQuiz.routesPrefix');
    }

    /**
     * @return View
     */
    public function index(): View
    {
        return view(self::VIEWS_PREFIX . self::PREFIX . '.index', [
            'columns' => $this->getColumns(),
            'createUrl' => route($this->globalPrefix . Router::AS_PREFIX . self::PREFIX . '::create'),
            'getRowsUrl' => route($this->globalPrefix . Router::AS_PREFIX . self::PREFIX . '::getRows'),
        ]);
    }

    /**
     * It's ajax request
     *
     * @param Request $request
     * @return array
     */
    public function getRows(Request $request): array
    {
        $data = $request->query();
        $columns = Datatable::columnsInterpreter($this->getColumns());

        $fullColumnsName = Datatable::getFullColumnNames($columns);

        $fullColumnsName[] = Survey::tableName() . '.id';
        $fullColumnsName[] = Survey::tableName() . '.status';
        $query = Survey::query()->select($fullColumnsName);

        $recordsTotal = $query->count();

        if ($data['search']['value'] === null) {
            $data['search']['value'] = '';
        }

        Datatable::filterQuery($query, $columns, $data);
        $recordsFiltered = $query->count();

        Datatable::sortQuery($query, $columns, $data);

        return Datatable::prepareResponse($data, $recordsTotal, $recordsFiltered, $columns, $query);
    }

    /**
     * @param QuestionTransformerManager $questionTransformer
     * @param Survey $model
     * @return View
     */
    public function create(QuestionTransformerManager $questionTransformer, Survey $model): View
    {
        return view(self::VIEWS_PREFIX . self::PREFIX . '.edit', array_merge([
            'httpMethod' => 'post',
            'headerText' => 'Create new Survey',
            'formUrl' => route($this->globalPrefix . Router::AS_PREFIX . self::PREFIX . '::createSubmit'),
            'submitText' => 'Next'
        ], $this->getVariables($questionTransformer, $model)));
    }

    /**
     * @param QuestionTransformerManager $questionTransformer
     * @param Survey $model
     * @return View
     */
    public function update(QuestionTransformerManager $questionTransformer, Survey $model): View
    {
        return view(self::VIEWS_PREFIX . self::PREFIX . '.edit', array_merge([
            'httpMethod' => 'put',
            'headerText' => 'Survey',
            'formUrl' => route($this->globalPrefix . Router::AS_PREFIX . self::PREFIX . '::updateSubmit',
                ['model' => $model['id']]),
            'submitText' => 'Save',
            'createQuestionUrl' => route($this->globalPrefix . Router::AS_PREFIX . SurveysController::PREFIX . '::' . QuestionsController::PREFIX . '::createSubmit',
                ['survey' => $model]),
            'createQuestionPartial' => self::VIEWS_PREFIX . QuestionsController::PREFIX . '._create',
            'questionTypes' => QuestionType::$types
        ], $this->getVariables($questionTransformer, $model)));
    }

    /**
     * @param QuestionTransformerManager $questionTransformer
     * @param Survey $model
     * @return array
     */
    protected function getVariables(QuestionTransformerManager $questionTransformer, Survey $model): array
    {
        $questions = $questionTransformer->transformCollection($model->exists ? $model->getQuestions() : []);

        // @TODO: add transformer for admin panel instead of using foreach below
        foreach ($questions as $index => $question) {
            $questions[$index]['updateQuestionUrl'] = route($this->globalPrefix . Router::AS_PREFIX . self::PREFIX . '::' . QuestionsController::PREFIX . '::update',
                ['model' => $question['id'], 'survey' => $model]);
            $questions[$index]['deleteQuestionUrl'] = route($this->globalPrefix . Router::AS_PREFIX . self::PREFIX . '::' . QuestionsController::PREFIX . '::deleteSubmit',
                ['model' => $question['id'], 'survey' => $model]);
        }

        return [
            'model' => $model,
            'tags' => Tag::query()->orderBy('name')->get(),
            'surveyTagsIds' => $model->tags()->pluck(Tag::tableName() . '.id')->all(),
            'createTagUrl' => route($this->globalPrefix . Router::AS_PREFIX . TagsController::PREFIX . '::create',
                ['survey' => $model]),
            'backUrl' => route($this->globalPrefix . Router::AS_PREFIX . self::PREFIX . '::index'),
            'questions' => $questions,
        ];
    }

    /**
     * @param Request $request
     * @param Survey $model
     * @return RedirectResponse
     */
    public function createSubmit(Request $request, Survey $model): RedirectResponse
    {
        return $this->save($request, $model);
    }

    /**
     * @param Request $request
     * @param Survey $model
     * @return RedirectResponse
     */
    public function updateSubmit(Request $request, Survey $model): RedirectResponse
    {
        return $this->save($request, $model);
    }

    /**
     * @param Survey $model
     * @return RedirectResponse
     */
    public function deleteSubmit(Survey $model): RedirectResponse
    {
        $model->delete();

        return $this->redirectToReferrer()->with('message', 'Item has been successfully removed.');
    }

    /**
     * @param Request $request
     * @param Survey $model
     * @return RedirectResponse
     */
    protected function save(Request $request, Survey $model): RedirectResponse
    {
        $validator = $model->exists ? new UpdateValidator($model) : new CreateValidator();
        $validator->setInput($request->input());

        if (!$validator->validate()) {
            return $this->redirectToReferrer()->withInput()->withErrors($validator->getValidator());
        }

        $model->title = $request->input('title', $model->title);
        $model->description = $request->input('description', $model->description);
        $model->time_limit = $request->input('timeLimit', $model->time_limit);
        $model->status = $request->input('status', $model->status);
        $model->save();

        $tagsIds = array_filter($request->input('tagsIds', []));

        $model->tags()->sync($tagsIds);

        if (!$model->wasRecentlyCreated) {
            return $this->redirectToReferrer()->with('message', 'Item has been successfully saved.');
        }

        return redirect()
            ->route($this->globalPrefix . Router::AS_PREFIX . self::PREFIX . '::update', ['model' => $model])
            ->with('message', 'Item has been successfully saved.');
    }

    /**
     * @return array
     */
    protected function getColumns(): array
    {
        $table = Survey::tableName();

        return [
            [
                'db' => 'id',
                'table' => $table,
                'name' => 'Id',
                'css_class' => [
                    'header' => 'no-sort'
                ]
            ],
            [
                'db' => 'title',
                'table' => $table,
                'name' => 'Title',
                'css_class' => [
                    'footer' => 'searchable-text'
                ]
            ],
            [
                'db' => 'description',
                'table' => $table,
                'name' => 'Description',
                'css_class' => [
                    'header' => 'no-sort'
                ]
            ],
            [
                'db' => 'status',
                'table' => $table,
                'name' => 'Status',
                'css_class' => [
                    'header' => 'no-sort'
                ],
                'formatter' => function ($d, $row) {
                    /** @var Survey $row */
                    return $row->getStatusName();
                },
            ],
            [
                'db' => 'id',
                'table' => $table,
                'name' => 'Tags',
                'css_class' => [
                    'header' => 'no-sort'
                ],
                'formatter' => function ($d, $row) {
                    /** @var Survey $row */
                    return $row->tags()->pluck(Tag::tableName() . '.name')->implode(', ');
                },
            ],
            [
                'db' => 'id',
                'table' => $table,
                'formatter' => function ($d, $row) {
                    return '<a href="' . route($this->globalPrefix . Router::AS_PREFIX . self::PREFIX . '::update',
                            ['model' => $d]) . '" class="btn btn-success" data-action="approve">Edit</a>';
                },
                'name' => '',
                'css_class' => [
                    'header' => 'no-sort'
                ]
            ],
            [
                'db' => 'id',
                'table' => $table,
                'formatter' => function ($d, $row) {
                    return '<a class="btn btn-danger" data-action="approve" 
onclick="event.preventDefault();$(\'#delete-form\').attr(\'action\', \'' . route($this->globalPrefix . Router::AS_PREFIX . self::PREFIX . '::deleteSubmit',
                            ['model' => $d]) . '\').submit();">Delete</a>';
                },
                'name' => '',
                'css_class' => [
                    'header' => 'no-sort'
                ]
            ]
        ];
    }
}