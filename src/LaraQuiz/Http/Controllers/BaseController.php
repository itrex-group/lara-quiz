<?php
declare(strict_types=1);

namespace LaraQuiz\Http\Controllers;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\URL;

/**
 * Class BaseController
 *
 * @package LaraQuiz\Http\Controllers
 */
class BaseController extends Controller
{
    use ValidatesRequests;


    const VIEWS_PREFIX = 'laraQuiz::';


    /**
     * @var string
     */
    private $referrer;

    /**
     * @var Request
     */
    protected $request;


    /**
     * BaseController constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->referrer = $request->input('_referrer_url', URL::previous());
    }

    /**
     * @param null|string $default
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    protected function redirectToReferrer(?string $default = null): RedirectResponse
    {
        $currentURL = URL::current();
        $previousURL = $this->getReferrer();

        if ($currentURL !== $previousURL) {
            $url = $previousURL;
        } else {
            $url = $default ?? $previousURL;
        }

        return redirect($url);
    }

    /**
     * @return string
     */
    protected function getReferrer(): string
    {
        return $this->referrer;
    }
}
