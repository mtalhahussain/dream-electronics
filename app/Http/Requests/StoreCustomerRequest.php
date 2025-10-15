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
            'name' => 'required|string|max:255',
            'cnic' => 'required|string|size:13|unique:customers,cnic',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'email' => 'nullable|email|max:255',
            'biometric' => 'nullable|file|mimes:jpg,jpeg,png|max:1024',
            'face_photo' => 'nullable|file|mimes:jpg,jpeg,png|max:1024',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Customer name is required.',
            'cnic.required' => 'CNIC is required.',
            'cnic.size' => 'CNIC must be exactly 13 digits.',
            'cnic.unique' => 'This CNIC is already registered.',
            'phone.required' => 'Phone number is required.',
            'address.required' => 'Address is required.',
            'email.email' => 'Please provide a valid email address.',
            'biometric.mimes' => 'Biometric must be an image file.',
            'biometric.max' => 'Biometric file size cannot exceed 1MB.',
            'face_photo.mimes' => 'Face photo must be an image file.',
            'face_photo.max' => 'Face photo file size cannot exceed 1MB.',
        ];
    }
}