<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointHistory extends Model
{
    use HasFactory, Uuid;

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'guid';
    // protected $table = 'merchant_locations';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    protected $fillable = [
        'total',
        'point',
        'file_url',
        'point_category_guid',
        'user_guid',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
        // 'status' => StatusEnum::class
    ];

    /**
     * POINT CATEGORY OBJECT
     */
    public function point_category()
    {
        return $this->belongsTo(PointCategory::class, 'point_category_guid', 'guid');
    }

    /**
     * USER OBJECT
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_guid', 'guid');
    }
}
