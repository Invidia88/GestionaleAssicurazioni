<?php

namespace App\Models;

use Database\Factories\PolicyFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Policy extends Model
{
    /** @use HasFactory<PolicyFactory> */
    use HasFactory;

    public const TYPES = [
        'Auto',
        'Moto',
        'Casa',
        'Vita',
        'Infortuni',
        'Aziendale',
        'Altro',
    ];

    public const STATUSES = [
        'attiva' => 'Attiva',
        'in_scadenza' => 'In scadenza',
        'scaduta' => 'Scaduta',
        'rinnovata' => 'Rinnovata',
    ];

    protected $fillable = [
        'client_id',
        'insurance_company_id',
        'type',
        'number',
        'start_date',
        'end_date',
        'annual_premium',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'annual_premium' => 'decimal:2',
    ];

    protected $appends = [
        'status_label',
        'whatsapp_url',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function insuranceCompany(): BelongsTo
    {
        return $this->belongsTo(InsuranceCompany::class);
    }

    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: fn () => self::STATUSES[$this->status] ?? $this->status,
        );
    }

    protected function whatsappUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->relationLoaded('client') || ! $this->relationLoaded('insuranceCompany')) {
                    return null;
                }

                $phone = $this->normalizePhoneForWhatsapp($this->client?->phone);

                if (! $phone) {
                    return null;
                }

                $message = sprintf(
                    'Ciao %s, ti ricordiamo che la tua polizza %s con compagnia %s scadrà il %s. Contattaci per il rinnovo.',
                    $this->client->first_name,
                    $this->type,
                    $this->insuranceCompany->name,
                    $this->end_date?->format('d/m/Y'),
                );

                return 'https://wa.me/'.$phone.'?text='.rawurlencode($message);
            },
        );
    }

    public function scopeExpiringWithin($query, int $days)
    {
        return $query
            ->whereDate('end_date', '>=', today())
            ->whereDate('end_date', '<=', today()->addDays($days));
    }

    public function scopeExpired($query)
    {
        return $query->whereDate('end_date', '<', today());
    }

    public static function options(): array
    {
        return [
            'types' => self::TYPES,
            'statuses' => collect(self::STATUSES)
                ->map(fn (string $label, string $value) => compact('value', 'label'))
                ->values()
                ->all(),
        ];
    }

    private function normalizePhoneForWhatsapp(?string $phone): ?string
    {
        if (! $phone) {
            return null;
        }

        $clean = preg_replace('/[^0-9+]/', '', $phone);

        if (str_starts_with($clean, '00')) {
            $clean = substr($clean, 2);
        }

        return ltrim($clean, '+') ?: null;
    }
}
