<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateQuizRunnerTagsTable
 *
 * @see \QuizRunner\Contracts\Surveys\Tag
 */
class CreateQuizRunnerTagsTable extends Migration
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
        $this->table = config('laraQuiz.tableTags');
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
            $table->string('name', 64);
            $table->string('description', 191)->default('');

            $table->unique('name');
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
