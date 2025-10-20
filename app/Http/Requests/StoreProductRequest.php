<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'purchase_cost' => 'nullable|numeric|min:0',
            'purchased_from' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'serial_number' => 'nullable|string|max:100|unique:products,serial_number',
            'stock_quantity' => 'required|integer|min:0',
            'description' => 'nullable|string|max:1000',
            'active' => 'required|boolean',
            'purchase_invoice' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'branch_id.required' => 'Branch selection is required.',
            'branch_id.exists' => 'Selected branch is invalid.',
            'name.required' => 'Product name is required.',
            'model.required' => 'Product model is required.',
            'brand.required' => 'Product brand is required.',
            'category_id.required' => 'Product category is required.',
            'category_id.exists' => 'Selected category is invalid.',
            'price.required' => 'Product price is required.',
            'price.min' => 'Product price must be greater than 0.',
            'purchase_cost.min' => 'Purchase cost must be greater than 0.',
            'sku.unique' => 'This SKU is already in use.',
            'serial_number.unique' => 'This serial number is already in use.',
            'stock_quantity.required' => 'Stock quantity is required.',
            'stock_quantity.min' => 'Stock quantity cannot be negative.',
            'active.required' => 'Product status is required.',
            'purchase_invoice.mimes' => 'Purchase invoice must be a PDF or image file.',
            'purchase_invoice.max' => 'Purchase invoice size cannot exceed 2MB.',
        ];
    }
}