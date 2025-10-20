<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_id' => 'nullable|exists:branches,id',
            'name' => 'required|string|max:255',
            'cnic' => 'required|string|size:15|unique:customers,cnic,' . $this->route('customer')?->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'profession' => 'nullable|string|max:255',
            'father_husband_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
            'biometric' => 'nullable|file|mimes:jpg,jpeg,png|max:1024',
            'face_photo' => 'nullable|file|mimes:jpg,jpeg,png|max:1024',
            
            // Guarantor 1 fields
            'guarantor_1_name' => 'nullable|string|max:255',
            'guarantor_1_cnic' => 'nullable|string|size:15',
            'guarantor_1_phone' => 'nullable|string|max:20',
            'guarantor_1_address' => 'nullable|string|max:500',
            'guarantor_1_relationship' => 'nullable|string|max:100',
            'guarantor_1_profession' => 'nullable|string|max:255',
            'guarantor_1_father_husband_name' => 'nullable|string|max:255',
            'guarantor_1_biometric' => 'nullable|file|mimes:jpg,jpeg,png|max:1024',
            
            // Guarantor 2 fields
            'guarantor_2_name' => 'nullable|string|max:255',
            'guarantor_2_cnic' => 'nullable|string|size:15',
            'guarantor_2_phone' => 'nullable|string|max:20',
            'guarantor_2_address' => 'nullable|string|max:500',
            'guarantor_2_relationship' => 'nullable|string|max:100',
            'guarantor_2_profession' => 'nullable|string|max:255',
            'guarantor_2_father_husband_name' => 'nullable|string|max:255',
            'guarantor_2_biometric' => 'nullable|file|mimes:jpg,jpeg,png|max:1024',
            
            // Legacy guarantor fields (for backward compatibility)
            'guarantor_name' => 'nullable|string|max:255',
            'guarantor_cnic' => 'nullable|string|size:15',
            'guarantor_phone' => 'nullable|string|max:20',
            'guarantor_address' => 'nullable|string|max:500',
            'guarantor_relation' => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'branch_id.exists' => 'The selected branch is invalid.',
            'name.required' => 'Customer name is required.',
            'cnic.required' => 'CNIC is required.',
            'cnic.size' => 'CNIC must be exactly 15 characters including dashes.',
            'cnic.unique' => 'This CNIC is already registered.',
            'phone.required' => 'Phone number is required.',
            'address.required' => 'Address is required.',
            'email.email' => 'Please provide a valid email address.',
            'biometric.mimes' => 'Biometric must be an image file.',
            'biometric.max' => 'Biometric file size cannot exceed 1MB.',
            'face_photo.mimes' => 'Face photo must be an image file.',
            'face_photo.max' => 'Face photo file size cannot exceed 1MB.',
            'guarantor_cnic.size' => 'Guarantor CNIC must be exactly 15 characters including dashes.',
        ];
    }
}