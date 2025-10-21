<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:3',
                'max:255',
                'regex:/^[a-zA-ZÀ-ÿ\s\'-]+$/', // Lettres, espaces, apostrophes, traits d'union
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                'unique:users,email',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            ],
            'password' => [
                'required',
                'string',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
                'confirmed',
            ],
            'role' => [
                'required',
                'string',
                'in:user,repairer,artisan,admin',
            ],
            'phone' => [
                'nullable',
                'string',
                'regex:/^[\+]?[(]?[0-9]{1,4}[)]?[-\s\.]?[(]?[0-9]{1,4}[)]?[-\s\.]?[0-9]{1,9}$/',
                'min:8',
                'max:20',
            ],
            'address' => [
                'nullable',
                'string',
                'min:5',
                'max:500',
            ],
            'bio' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'avatar' => [
                'nullable',
                'image',
                'mimes:jpeg,jpg,png,gif,webp',
                'max:2048', // 2MB max
                'dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000',
            ],
            'location_lat' => [
                'nullable',
                'numeric',
                'between:-90,90',
                'regex:/^-?([1-8]?[0-9]\.{1}\d+|90\.{1}0+)$/',
            ],
            'location_lng' => [
                'nullable',
                'numeric',
                'between:-180,180',
                'regex:/^-?((1[0-7][0-9]|[1-9]?[0-9])\.{1}\d+|180\.{1}0+)$/',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom est obligatoire.',
            'name.min' => 'Le nom doit contenir au moins 3 caractères.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'name.regex' => 'Le nom ne peut contenir que des lettres, espaces, apostrophes et traits d\'union.',
            
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email doit être valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'email.regex' => 'Le format de l\'email n\'est pas valide.',
            
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            
            'role.required' => 'Le rôle est obligatoire.',
            'role.in' => 'Le rôle sélectionné n\'est pas valide.',
            
            'phone.regex' => 'Le format du numéro de téléphone n\'est pas valide.',
            'phone.min' => 'Le numéro de téléphone doit contenir au moins 8 chiffres.',
            'phone.max' => 'Le numéro de téléphone ne peut pas dépasser 20 caractères.',
            
            'address.min' => 'L\'adresse doit contenir au moins 5 caractères.',
            'address.max' => 'L\'adresse ne peut pas dépasser 500 caractères.',
            
            'bio.max' => 'La biographie ne peut pas dépasser 1000 caractères.',
            
            'avatar.image' => 'Le fichier doit être une image.',
            'avatar.mimes' => 'L\'avatar doit être au format: jpeg, jpg, png, gif ou webp.',
            'avatar.max' => 'L\'avatar ne peut pas dépasser 2 Mo.',
            'avatar.dimensions' => 'L\'image doit avoir une taille entre 100x100 et 2000x2000 pixels.',
            
            'location_lat.between' => 'La latitude doit être entre -90 et 90.',
            'location_lat.regex' => 'Le format de la latitude n\'est pas valide.',
            
            'location_lng.between' => 'La longitude doit être entre -180 et 180.',
            'location_lng.regex' => 'Le format de la longitude n\'est pas valide.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nom',
            'email' => 'adresse email',
            'password' => 'mot de passe',
            'role' => 'rôle',
            'phone' => 'téléphone',
            'address' => 'adresse',
            'bio' => 'biographie',
            'avatar' => 'avatar',
            'location_lat' => 'latitude',
            'location_lng' => 'longitude',
        ];
    }
}

