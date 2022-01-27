<?php

namespace Nabcellent\Kyanda\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Nabcellent\Kyanda\Models\KyandaRequest
 *
 * @property int $id
 * @property string $status
 * @property int $status_code
 * @property string $reference
 * @property string $description
 * @property string $message
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read KyandaTransaction $transaction
 *
 */
class KyandaRequest extends Model
{
    protected $guarded = ['id'];

    public function transaction(): HasOne
    {
        return $this->hasOne(KyandaTransaction::class, 'reference', 'reference');
    }
}
