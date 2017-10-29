<?php
declare(strict_types=1);

namespace LaraQuiz\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseModel
 *
 * @package LaraQuiz\Models
 */
abstract class BaseModel extends Model
{
    /**
     * @return string
     */
    abstract public static function tableName(): string;

    /**
     * BaseModel constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = static::tableName();
    }
}
