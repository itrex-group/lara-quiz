<?php
declare(strict_types=1);

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class CreateQuizRunnerSurveyUserTable
 */
class CreateQuizRunnerSurveyUserTable extends Migration
{
    /**
     * @var string
     */
    private $table;

    /**
     * @var string
     */
    private $userForeignKey;

    /**
     * @var string
     */
    private $userTable;

    /**
     * @var string
     */
    private $surveyTable;


    /**
     * CreateQuizRunnerSurveyUserTable constructor.
     */
    public function __construct()
    {
        $config = Container::getInstance()->make(Repository::class);
        $this->table = $config->get('laraQuiz.tableSurveyUser');
        $this->userForeignKey = $config->get('laraQuiz.userForeignKey');
        $this->userTable = $config->get('laraQuiz.tableUser');
        $this->surveyTable = $config->get('laraQuiz.tableSurveys');
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

            $table->unsignedInteger('survey_id');
            $table->unsignedInteger($this->userForeignKey);

            $table->primary(['survey_id', $this->userForeignKey]);

            $table->foreign('survey_id')
                ->references('id')->on($this->surveyTable)
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign($this->userForeignKey)
                ->references('id')->on($this->userTable)
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
