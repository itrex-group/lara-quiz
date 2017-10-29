<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateQuizRunnerQuestionsTable
 *
 * @see \QuizRunner\Contracts\Options\IntervalOption
 */
class CreateQuizRunnerIntervalOptionsTable extends Migration
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
        $this->table = config('laraQuiz.tableIntervalOptions');
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
            $table->unsignedInteger('question_option_id');
            $table->smallInteger('min_value');
            $table->smallInteger('max_value');

            $table->index('question_option_id');

            $table->foreign('question_option_id')
                ->references('id')->on(config('laraQuiz.tableQuestionOptions'))
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
