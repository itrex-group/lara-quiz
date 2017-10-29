<?php
declare(strict_types=1);

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateQuizRunnerQuestionOptionSurveySessionAnswerTable
 */
class CreateQuizRunnerQuestionOptionSurveySessionAnswerTable extends Migration
{
    /**
     * @var string
     */
    private $table;

    /**
     * @var string
     */
    private $surveySessionAnswerTable;

    /**
     * @var string
     */
    private $questionOptionsTable;


    /**
     * CreateQuizRunnerQuestionOptionSurveySessionAnswerTable constructor.
     */
    public function __construct()
    {
        $config = Container::getInstance()->make(Repository::class);
        $this->table = $config->get('laraQuiz.tableQuestionOptionSurveySessionAnswer');
        $this->surveySessionAnswerTable = $config->get('laraQuiz.tableSurveySessionAnswers');
        $this->questionOptionsTable = $config->get('laraQuiz.tableQuestionOptions');
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

            $table->unsignedInteger('survey_session_answer_id');
            $table->unsignedInteger('question_option_id');

            $table->primary(['survey_session_answer_id', 'question_option_id'], 'session_answer_question_option_primary');

            $table->foreign('survey_session_answer_id', 'survey_session_answer_id_foreign')
                ->references('id')->on($this->surveySessionAnswerTable)
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('question_option_id', 'question_option_id_foreign')
                ->references('id')->on($this->questionOptionsTable)
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
