<?php
declare(strict_types=1);

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateQuizRunnerSurveySessionAnswersTable
 *
 * @see \QuizRunner\Contracts\Answers\Answer
 */
class CreateQuizRunnerSurveySessionAnswersTable extends Migration
{
    /**
     * @var string
     */
    private $table;

    /**
     * @var string
     */
    private $surveySessionTable;

    /**
     * @var string
     */
    private $questionTable;


    /**
     * CreateQuizRunnerSurveySessionAnswersTable constructor.
     */
    public function __construct()
    {
        $config = Container::getInstance()->make(Repository::class);
        $this->table = $config->get('laraQuiz.tableSurveySessionAnswers');
        $this->surveySessionTable = $config->get('laraQuiz.tableSurveySessions');
        $this->questionTable = $config->get('laraQuiz.tableQuestions');
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
            $table->unsignedInteger('survey_session_id');
            $table->unsignedInteger('question_id');
            $table->timestamp('created_at')->nullable();

            $table->index('survey_session_id');
            $table->index('question_id');

            $table->foreign('survey_session_id')
                ->references('id')->on($this->surveySessionTable)
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('question_id')
                ->references('id')->on($this->questionTable)
                ->onUpdate('cascade')
                ->onDelete('cascade');
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
