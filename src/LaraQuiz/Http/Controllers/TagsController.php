<?php
declare(strict_types=1);

namespace LaraQuiz\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use LaraQuiz\Helpers\Datatable;
use LaraQuiz\Models\Tag;
use LaraQuiz\Routing\Router;
use LaraQuiz\Validators\Tag\CreateValidator;
use LaraQuiz\Validators\Tag\UpdateValidator;

/**
 * Class TagsController
 *
 * @package LaraQuiz\Http\Controllers
 */
class TagsController extends BaseController
{
    const PREFIX = 'tags';


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

        $fullColumnsName[] = Tag::tableName() . '.id';
        $query = Tag::query()->select($fullColumnsName);

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
     * @param Tag $model
     * @return View
     */
    public function create(Tag $model): View
    {
        return view(self::VIEWS_PREFIX . self::PREFIX . '.edit', [
            'model' => $model,
            'backUrl' => route($this->globalPrefix . Router::AS_PREFIX . self::PREFIX . '::index'),
            'httpMethod' => 'post',
            'headerText' => 'Create new Tag',
            'formUrl' => route($this->globalPrefix . Router::AS_PREFIX . self::PREFIX . '::createSubmit'),
        ]);
    }

    /**
     * @param Tag $model
     * @return View
     */
    public function update(Tag $model): View
    {
        return view(self::VIEWS_PREFIX . self::PREFIX . '.edit', [
            'model' => $model,
            'backUrl' => route($this->globalPrefix . Router::AS_PREFIX . self::PREFIX . '::index'),
            'httpMethod' => 'put',
            'headerText' => 'Update Tag',
            'formUrl' => route($this->globalPrefix . Router::AS_PREFIX . self::PREFIX . '::updateSubmit',
                ['model' => $model['id']])
        ]);
    }

    /**
     * @param Request $request
     * @param Tag $model
     * @return RedirectResponse
     */
    public function createSubmit(Request $request, Tag $model): RedirectResponse
    {
        return $this->save($request, $model);
    }

    /**
     * @param Request $request
     * @param Tag $model
     * @return RedirectResponse
     */
    public function updateSubmit(Request $request, Tag $model): RedirectResponse
    {
        return $this->save($request, $model);
    }

    /**
     * @param Tag $model
     * @return RedirectResponse
     */
    public function deleteSubmit(Tag $model): RedirectResponse
    {
        $model->delete();

        return $this->redirectToReferrer()->with('message', 'Item has been successfully removed.');
    }

    /**
     * @param Request $request
     * @param Tag $model
     * @return RedirectResponse
     */
    protected function save(Request $request, Tag $model): RedirectResponse
    {
        $validator = $model->exists ? new UpdateValidator($model) : new CreateValidator();
        $validator->setInput($request->input());

        if (!$validator->validate()) {
            return $this->redirectToReferrer()->withInput()->withErrors($validator->getValidator());
        }

        $model->name = $request->input('name', $model->name);
        $model->description = $request->input('description', $model->description);

        $model->save();

        return $this->redirectToReferrer()->with('message', 'Item has been successfully saved.');
    }

    /**
     * @return array
     */
    protected function getColumns(): array
    {
        $table = Tag::tableName();

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
                'db' => 'name',
                'table' => $table,
                'name' => 'Name',
                'css_class' => [
                    'footer' => 'searchable-text'
                ]
            ],
            [
                'db' => 'description',
                'table' => $table,
                'name' => 'Description',
                'css_class' => [
                    'footer' => 'searchable-text'
                ]
            ],
            [
                'db' => 'id',
                'table' => $table,
                'formatter' => function ($d, $row) {
                    return '<a href="' . route($this->globalPrefix . Router::AS_PREFIX . self::PREFIX . '::update',
                            ['id' => $d]) . '" class="btn btn-success" data-action="approve">Edit</a>';
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
                            ['id' => $d]) . '\').submit();">Delete</a>';
                },
                'name' => '',
                'css_class' => [
                    'header' => 'no-sort'
                ]
            ]
        ];
    }
}