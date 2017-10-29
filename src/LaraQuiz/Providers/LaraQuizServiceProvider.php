<?php
declare(strict_types=1);

namespace LaraQuiz\Providers;

use Illuminate\Config\Repository;
use Illuminate\Support\ServiceProvider;
use LaraQuiz\Factories\RespondentFactory;
use QuizRunner\Contracts\Users\Respondent;
use QuizRunner\Transformers\QuestionTransformerManager;

/**
 * Class LaraQuizServiceProvider
 *
 * @package LaraQuiz\Providers
 */
class LaraQuizServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/laraQuiz.php' => config_path('laraQuiz.php'),
        ]);

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'laraQuiz');
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/laraQuiz.php', 'laraQuiz'
        );

        $this->app->bind(QuestionTransformerManager::class, function ($app) {
            /** @var \Illuminate\Contracts\Foundation\Application $app */
            $config = $app->make(Repository::class);
            $optionManagerClass = $config->get('laraQuiz.optionTransformerManager');

            return (new QuestionTransformerManager)
                ->setOptionTransformer(new $optionManagerClass)
                ->setTransformersNamespaces($config->get('laraQuiz.transformersNamespaces'));
        });

        $this->app->bind(RespondentFactory::class, function ($app) {
            /** @var \Illuminate\Contracts\Foundation\Application $app */
            $config = $app->make(Repository::class);

            return new RespondentFactory($config->get('laraQuiz.modelUser'));
        });

        $this->app->bind(Respondent::class, $this->app->make(Repository::class)->get('laraQuiz.modelUser'));
    }

}
