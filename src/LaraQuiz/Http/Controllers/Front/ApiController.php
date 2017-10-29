<?php
declare(strict_types=1);

namespace LaraQuiz\Http\Controllers\Front;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use LaraQuiz\Exceptions\CustomValidationException;
use LaraQuiz\Helpers\Api;

/**
 * Class ApiController
 *
 * @package LaraQuiz\Http\Controllers\Front
 */
class ApiController extends Controller
{
    const PAGINATE_OFFSET_DEFAULT = 0;
    const PAGINATE_LIMIT_DEFAULT = 10;


    /**
     * This endpoint is required by some JavaScript frameworks like AngularJs
     *
     * @return Response
     */
    public function options(): Response
    {
        return $this->respondSuccess();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder
     */
    protected function paginate($query)
    {
        $request = request();

        $offset = $request->get('offset', self::PAGINATE_OFFSET_DEFAULT);
        $limit = $request->get('limit', self::PAGINATE_LIMIT_DEFAULT);

        $query->offset($offset)->limit($limit);

        return $query;
    }

    /**
     * @param string|array $msg
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondNotFound($msg = 'Resource not found.'): JsonResponse
    {
        return Api::respondNotFound($msg);
    }

    /**
     * @param string|array $msg
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondBadRequest($msg = 'Bad request.'): JsonResponse
    {
        return Api::respondBadRequest($msg);
    }

    /**
     * @param string|array $msg
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondForbidden($msg = 'Forbidden.'): JsonResponse
    {
        return Api::respondForbidden($msg);
    }

    /**
     * @param string|array $msg
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondUnauthorized($msg = 'Unauthorized.'): JsonResponse
    {
        return Api::respondUnauthorized($msg);
    }

    /**
     * @param string $msg
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondServerError(string $msg = 'Internal server error.'): JsonResponse
    {
        return Api::respondServerError($msg);
    }

    /**
     * @param string|array $msg
     * @param int $code
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithError($msg = 'Some error occurred', int $code = 500, array $headers = []): JsonResponse
    {
        return Api::respondError($msg, $code, $headers);
    }

    /**
     * @param mixed $data
     * @param array $headers
     * @param int $statusCode
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respond($data, array $headers = [], int $statusCode = 200): JsonResponse
    {
        return Api::respond($data, $headers, $statusCode);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    protected function respondSuccess(): Response
    {
        return Api::respondSuccess();
    }

    /**
     * @param mixed $item
     * @param array $data
     * @return JsonResponse
     */
    protected function respondWithItem($item, array $data = []): JsonResponse
    {
        return $this->respond(array_merge(['item' => $item], $data));
    }

    /**
     * @param \Illuminate\Http\Request|array $request
     * @param array $rules
     * @param array $messages
     * @return void
     * @throws CustomValidationException
     */
    protected function validation($request, array $rules, array $messages = []): void
    {
        $data = $request instanceof Request ? $request->all() : $request;
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new CustomValidationException($validator, $this->respondBadRequest($validator->errors()->all()));
        }
    }

    /**
     * @param array|string $rules
     * @param array $messages
     * @return void
     */
    protected function check($rules, array $messages = []): void
    {
        $request = request();
        if (is_string($rules)) {
            /** @var \LaraQuiz\Validators\BaseValidator $validator */
            $validator = (new $rules())->setInput($request->all());
            $validator->pass();
        } else {
            $this->validation($request, $rules, $messages);
        }
    }

    /**
     * @param array $data
     * @param null|array $pagination
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithPagination(array $data, ?array $pagination = null): JsonResponse
    {
        if ($pagination === null) {
            $request = request();
            $pagination['offset'] = $request->get('offset', self::PAGINATE_OFFSET_DEFAULT);
            $pagination['limit'] = $request->get('limit', self::PAGINATE_LIMIT_DEFAULT);
        }

        return $this->respond(array_merge($data, [
            'pagination' => [
                'offset' => $pagination['limit'] + $pagination['offset'],
                'limit' => (int)$pagination['limit'],
                'moreAvailable' => count($data['items']) === (int)$pagination['limit']
            ]
        ]));
    }

    /**
     * @param array $data
     * @param null|array $pagination
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithPaginatedItems(array $data, ?array $pagination = null): JsonResponse
    {
        return $this->respondWithPagination(['items' => $data], $pagination);
    }
}