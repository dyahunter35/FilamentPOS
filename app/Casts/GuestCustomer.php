<?php

namespace App\Casts;

use App\Models\Customer;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class GuestCustomer implements CastsAttributes
{
    /**
     * Cast the stored value to a temporary Customer model.
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?Customer
    {
        if (is_null($value)) {
            return null;
        }
        // Decode the JSON and create a new, non-persisted Customer model
        $data = json_decode($value, true);
        $data['name'] = $data['name'] . '  '.__('customer.guest_suffix');
        return new Customer($data);
    }

    /**
     * Prepare the given value for storage.
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if (is_null($value)) {
            return null;
        }

        // Ensure the value is an array and encode it to JSON for storage
        $data = is_array($value) ? $value : $value->toArray();
        return json_encode($data);
    }
}
