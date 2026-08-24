<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetalsPurity extends Model
{
    use HasFactory;

    protected $table = 'metals_purity'; // Use metals_purity table instead of metal_purities
    protected $primaryKey = 'metalpurity_id'; // Primary key is metalpurity_id
    public $timestamps = false; // No created_at/updated_at columns

    protected $fillable = ['metal_id', 'name', 'code', 'description'];

    public function metal()
    {
        return $this->belongsTo(Metal::class);
    }
}
