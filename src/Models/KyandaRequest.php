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
 * @property string $merchant_reference
 * @property string $description
 * @property string $message
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read KyandaTransaction $transaction
 *
 */
class KyandaRequest extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function transaction(): HasOne
    {
        return $this->hasOne(KyandaTransaction::class, 'transaction_reference', 'merchant_reference');
    }

    protected static function newFactory(): \Nabcellent\Kyanda\Database\Factories\KyandaRequestFactory
    {
        return \Nabcellent\Kyanda\Database\Factories\KyandaRequestFactory::new();
    }
}
