<?php

namespace Nabcellent\Kyanda\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Nabcellent\Kyanda\Models\KyandaTransaction
 *
 * @property int                $id
 * @property string             $transaction_reference
 * @property string             $category
 * @property string             $source
 * @property string             $destination
 * @property string             $merchant_id
 * @property string             $status
 * @property int                $status_code
 * @property string             $message
 * @property array              $details
 * @property int                $amount
 * @property Carbon|null        $transaction_date
 * @property Carbon|null        $created_at
 * @property Carbon|null        $updated_at
 * @property-read KyandaRequest $request
 */
class KyandaTransaction extends Model
{
    protected $guarded = ['id'];

    protected $dates = [
        'transaction_date'
    ];

    protected $casts = [
        'details' => 'array'
    ];

    public function request(): HasOne
    {
        return $this->hasOne(KyandaRequest::class, 'merchant_reference', 'transaction_reference');
    }
}
