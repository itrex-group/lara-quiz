<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use LaraQuiz\Models\Survey;

/**
 * Class CreateQuizRunnerSurveysTable
 *
 * @see \QuizRunner\Contracts\Surveys\Survey
 */
class CreateQuizRunnerSurveysTable extends Migration
{
    /**
     * @var string
     */
    private $table;

    /**
     * CreateQuizRunnerQuestionsTable constructor.
     */
    public function __construct()
    {
        $this->table = config('laraQuiz.tableSurveys');
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->increments('id');
            $table->string('title', 191);
            $table->string('description', 255)->default('');
            $table->unsignedInteger('time_limit')->default(0);
            $table->unsignedTinyInteger('status')->default(Survey::STATUS_NON_ACTIVE);
            $table->timestamps();

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop($this->table);
    }
}
