<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_id' => 'required|exists:branches,id',
            'customer_id' => 'required|exists:customers,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'total_price' => 'required|numeric|min:0',
            'duration_months' => 'required|in:6,10,12',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'advance_received' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date|after_or_equal:today',
        ];
    }

    public function messages(): array
    {
        return [
            'branch_id.required' => 'Branch is required.',
            'branch_id.exists' => 'Selected branch does not exist.',
            'customer_id.required' => 'Customer is required.',
            'customer_id.exists' => 'Selected customer does not exist.',
            'items.required' => 'At least one item is required.',
            'items.min' => 'At least one item is required.',
            'items.*.product_id.required' => 'Product is required for each item.',
            'items.*.product_id.exists' => 'Selected product does not exist.',
            'items.*.quantity.required' => 'Quantity is required for each item.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
            'items.*.unit_price.required' => 'Unit price is required for each item.',
            'items.*.unit_price.min' => 'Unit price must be greater than 0.',
            'total_price.required' => 'Total price is required.',
            'total_price.min' => 'Total price must be greater than 0.',
            'duration_months.required' => 'Installment duration is required.',
            'duration_months.in' => 'Installment duration must be 6, 10, or 12 months.',
            'discount_percent.max' => 'Discount cannot exceed 100%.',
            'advance_received.min' => 'Advance amount cannot be negative.',
            'start_date.after_or_equal' => 'Start date cannot be in the past.',
        ];
    }
}