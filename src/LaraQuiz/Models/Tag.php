<?php
declare(strict_types=1);

namespace LaraQuiz\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use QuizRunner\Contracts\Surveys\Survey;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|Survey[] $surveys
 */
class Tag extends BaseModel implements \QuizRunner\Contracts\Surveys\Tag
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * @var array
     */
    protected $casts = [
        'id' => 'int',
    ];


    /**
     * @return string
     */
    public static function tableName(): string
    {
        return config('laraQuiz.tableTags');
    }


    /*
    |--------------------------------------------------------------------------
    |  Tag interface
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
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return iterable|\Illuminate\Database\Eloquent\Collection|Survey[]
     */
    public function getSurveys(): iterable
    {
        return $this->surveys;
    }


    /*
    |--------------------------------------------------------------------------
    |  Relations
    |--------------------------------------------------------------------------
    */

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function surveys(): BelongsToMany
    {
        return $this->belongsToMany(Survey::class);
    }


    /*
    |--------------------------------------------------------------------------
    |  Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * @param string|null $value
     * @return string
     */
    public function getDescriptionAttribute(?string $value): string
    {
        return $value ?? '';
    }
}
