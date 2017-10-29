<?php
declare(strict_types=1);

namespace LaraQuiz\Helpers;

use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Class Logger
 *
 * @package LaraQuiz\Helpers
 */
class Logger
{
    /**
     * @param Throwable $e
     * @return void
     */
    public static function exception(Throwable $e): void
    {
        Log::debug(get_class($e) . ' has been thrown with message: "' . $e->getMessage() . '" in file: ' . $e->getFile()
            . ' at line: ' . $e->getLine() . '. Trace:' . $e->getTraceAsString());
    }

    /**
     * @param string $string
     * @return void
     */
    public static function log(string $string): void
    {
        Log::debug($string);
    }
}