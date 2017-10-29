<?php
declare(strict_types=1);

namespace LaraQuiz\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Config;

/**
 * @property int $id
 * @property int $time_limit
 * @property int $status
 * @property string $title
 * @property string $description
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|Tag[] $tags
 * @property-read \Illuminate\Database\Eloquent\Collection|Question[] $questions
 * @property-read \Illuminate\Database\Eloquent\Collection|\QuizRunner\Contracts\Users\Respondent[] $users
 */
class Survey extends BaseModel implements \QuizRunner\Contracts\Surveys\Survey
{
    const STATUS_ACTIVE = 1;
    const STATUS_NON_ACTIVE = 2;

    /**
     * @var array
     */
    public static $statuses = [
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_NON_ACTIVE => 'Non-Active',
    ];


    /**
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * @var array
     */
    protected $casts = [
        'id' => 'int',
        'time_limit' => 'int',
        'status' => 'int',
    ];


    /**
     * @return string
     */
    public static function tableName(): string
    {
        return config('laraQuiz.tableSurveys');
    }


    /*
    |--------------------------------------------------------------------------
    |  Survey interface
    |--------------------------------------------------------------------------
    */

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return iterable|\Illuminate\Database\Eloquent\Collection|\QuizRunner\Contracts\Questions\Question[]
     */
    public function getQuestions(): iterable
    {
        return $this->questions;
    }

    /**
     * @return iterable|\Illuminate\Database\Eloquent\Collection|Tag[]
     */
    public function getTags(): iterable
    {
        return $this->tags;
    }

    /**
     * Time limit in seconds (positive integer). If 0, it means that survey does not have a time limit.
     *
     * @return int
     */
    public function getTimeLimit(): int
    {
        return $this->time_limit;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }


    /*
    |--------------------------------------------------------------------------
    |  Relations
    |--------------------------------------------------------------------------
    */

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            Config::get('laraQuiz.modelUser'),
            Config::get('laraQuiz.tableSurveyUser'),
            'survey_id',
            Config::get('laraQuiz.userForeignKey')
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class);
    }


    /*
    |--------------------------------------------------------------------------
    |  Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * @param int|null $value
     * @return int
     */
    public function getStatusAttribute(?int $value): int
    {
        return $value ?? self::STATUS_NON_ACTIVE;
    }

    /**
     * @param int|null $value
     * @return int
     */
    public function getTimeLimitAttribute(?int $value): int
    {
        return $value ?? 0;
    }

    /**
     * @param string|null $value
     * @return string
     */
    public function getDescriptionAttribute(?string $value): string
    {
        return $value ?? '';
    }

    /*
    |--------------------------------------------------------------------------
    |  Other methods
    |--------------------------------------------------------------------------
    */

    /**
     * @return string
     */
    public function getStatusName(): string
    {
        return static::$statuses[$this->getStatus()];
    }
}
