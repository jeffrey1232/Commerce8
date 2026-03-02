<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShipmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->isVendor();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20|regex:/^[+]?[0-9\s\-\(\)]+$/',
            'product_name' => 'required|string|max:255',
            'product_description' => 'required|string|min:10|max:2000',
            'product_images' => 'nullable|array|max:5',
            'product_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'total_amount' => 'required|numeric|min:1000|max:10000000',
            'special_instructions' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'client_name.required' => 'Le nom du client est obligatoire',
            'client_name.max' => 'Le nom du client ne peut pas dépasser 255 caractères',
            'client_phone.required' => 'Le téléphone du client est obligatoire',
            'client_phone.regex' => 'Le format du numéro de téléphone est invalide',
            'product_name.required' => 'Le nom du produit est obligatoire',
            'product_name.max' => 'Le nom du produit ne peut pas dépasser 255 caractères',
            'product_description.required' => 'La description du produit est obligatoire',
            'product_description.min' => 'La description doit contenir au moins 10 caractères',
            'product_description.max' => 'La description ne peut pas dépasser 2000 caractères',
            'product_images.max' => 'Vous ne pouvez pas télécharger plus de 5 images',
            'product_images.*.image' => 'Les fichiers doivent être des images',
            'product_images.*.mimes' => 'Les images doivent être au format JPEG, PNG ou GIF',
            'product_images.*.max' => 'Les images ne peuvent pas dépasser 2MB',
            'total_amount.required' => 'Le montant total est obligatoire',
            'total_amount.numeric' => 'Le montant doit être un nombre',
            'total_amount.min' => 'Le montant minimum est de 1000 FCFA',
            'total_amount.max' => 'Le montant maximum est de 10,000,000 FCFA',
            'special_instructions.max' => 'Les instructions spéciales ne peuvent pas dépasser 500 caractères',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'client_name' => 'nom du client',
            'client_phone' => 'téléphone du client',
            'product_name' => 'nom du produit',
            'product_description' => 'description du produit',
            'product_images' => 'images du produit',
            'total_amount' => 'montant total',
            'special_instructions' => 'instructions spéciales',
        ];
    }
}
