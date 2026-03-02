<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessPaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Les clients peuvent faire des paiements
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tracking_code' => 'required|string|exists:packages,tracking_code',
            'payment_method' => 'required|in:cash,mobile_money,wave,wizall',
            'phone_number' => 'required_if:payment_method,mobile_money,wave,wizall|string|regex:/^[+]?[0-9\s\-\(\)]+$/',
            'use_fitting_room' => 'sometimes|boolean',
            'guarantee_type' => 'required_if:use_fitting_room,true|in:id_card,phone,cash',
            'guarantee_details' => 'required_if:use_fitting_room,true|string|max:255',
            'confirm_terms' => 'required|accepted',
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
            'tracking_code.required' => 'Le code de suivi est obligatoire',
            'tracking_code.exists' => 'Ce code de suivi n\'existe pas',
            'payment_method.required' => 'Le moyen de paiement est obligatoire',
            'payment_method.in' => 'Le moyen de paiement n\'est pas valide',
            'phone_number.required_if' => 'Le numéro de téléphone est obligatoire pour ce moyen de paiement',
            'phone_number.regex' => 'Le format du numéro de téléphone est invalide',
            'use_fitting_room.boolean' => 'La valeur pour l\'utilisation de la cabine d\'essayage doit être vrai ou faux',
            'guarantee_type.required_if' => 'Le type de garantie est obligatoire si vous utilisez la cabine d\'essayage',
            'guarantee_type.in' => 'Le type de garantie doit être: carte d\'identité, téléphone ou espèces',
            'guarantee_details.required_if' => 'Les détails de la garantie sont obligatoires si vous utilisez la cabine d\'essayage',
            'guarantee_details.max' => 'Les détails de la garantie ne peuvent pas dépasser 255 caractères',
            'confirm_terms.required' => 'Vous devez accepter les termes et conditions',
            'confirm_terms.accepted' => 'Vous devez accepter les termes et conditions',
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
            'tracking_code' => 'code de suivi',
            'payment_method' => 'moyen de paiement',
            'phone_number' => 'numéro de téléphone',
            'use_fitting_room' => 'utilisation cabine d\'essayage',
            'guarantee_type' => 'type de garantie',
            'guarantee_details' => 'détails de la garantie',
            'confirm_terms' => 'acceptation des termes',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->payment_method !== 'cash' && !$this->phone_number) {
                $validator->errors()->add('phone_number', 'Le numéro de téléphone est obligatoire pour ce moyen de paiement');
            }

            if ($this->boolean('use_fitting_room') && !$this->guarantee_type) {
                $validator->errors()->add('guarantee_type', 'Le type de garantie est obligatoire pour utiliser la cabine d\'essayage');
            }
        });
    }
}
