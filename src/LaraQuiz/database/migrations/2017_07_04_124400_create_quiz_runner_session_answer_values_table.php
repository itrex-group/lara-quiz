<?php
declare(strict_types=1);

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateQuizRunnerSessionAnswerValuesTable
 */
class CreateQuizRunnerSessionAnswerValuesTable extends Migration
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
     * CreateQuizRunnerSessionAnswerValuesTable constructor.
     */
    public function __construct()
    {
        $config = Container::getInstance()->make(Repository::class);
        $this->table = $config->get('laraQuiz.tableSessionAnswerValues');
        $this->surveySessionAnswerTable = $config->get('laraQuiz.tableSurveySessionAnswers');
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
            $table->unsignedInteger('survey_session_answer_id');
            $table->text('value');

            $table->unique('survey_session_answer_id');

            $table->foreign('survey_session_answer_id')
                ->references('id')->on($this->surveySessionAnswerTable)
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
