<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayInstallmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'installment_id' => 'required|exists:installments,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|in:cash,bank_transfer,cheque,card',
            'reference_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
            'payment_date' => 'nullable|date|before_or_equal:today',
        ];
    }

    public function messages(): array
    {
        return [
            'installment_id.required' => 'Installment is required.',
            'installment_id.exists' => 'Selected installment does not exist.',
            'amount.required' => 'Payment amount is required.',
            'amount.min' => 'Payment amount must be greater than 0.',
            'payment_method.required' => 'Payment method is required.',
            'payment_method.in' => 'Invalid payment method selected.',
            'reference_number.max' => 'Reference number cannot exceed 255 characters.',
            'notes.max' => 'Notes cannot exceed 500 characters.',
            'payment_date.before_or_equal' => 'Payment date cannot be in the future.',
        ];
    }
}