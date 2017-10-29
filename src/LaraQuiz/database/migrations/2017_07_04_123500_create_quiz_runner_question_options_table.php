<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use LaraQuiz\Models\QuestionOption;

/**
 * Class CreateQuizRunnerQuestionsTable
 *
 * @see \QuizRunner\Contracts\Options\Option
 */
class CreateQuizRunnerQuestionOptionsTable extends Migration
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
        $this->table = config('laraQuiz.tableQuestionOptions');
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
            $table->unsignedInteger('question_id');
            $table->unsignedTinyInteger('correctness')->default(QuestionOption::CORRECTNESS_NEUTRAL);
            $table->tinyInteger('score')->default(0);
            $table->string('feedback', 255)->default('');
            $table->timestamps();

            $table->index('question_id');

            $table->foreign('question_id')
                ->references('id')->on(config('laraQuiz.tableQuestions'))
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
