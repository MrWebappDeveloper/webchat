<?php

namespace MrWebappDeveloper\Webchat\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Webchat\Database\factories\CardFactory;

class Card extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'shortcut',
        'order_index',
    ];

    public function messages():HasMany
    {
        return $this->hasMany(CardMessage::class);
    }

    protected static function newFactory(): CardFactory
    {
        return CardFactory::new();
    }
}
