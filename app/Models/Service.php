<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ["name", "price", "description", "status"];

    protected function casts(): array
    {
        return [
            "status" => "boolean",
            "price" => "integer",
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
