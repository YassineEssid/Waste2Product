<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Registration is public
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
                'regex:/^[a-zA-ZÀ-ÿ\s\'-]+$/',
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
                'in:user,repairer,artisan',
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
            'terms' => [
                'required',
                'accepted',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom complet est obligatoire.',
            'name.min' => 'Le nom doit contenir au moins 3 caractères.',
            'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
            'name.regex' => 'Le nom ne peut contenir que des lettres, espaces, apostrophes et traits d\'union.',
            
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email doit être valide et vérifiable.',
            'email.unique' => 'Cette adresse email est déjà utilisée. Veuillez vous connecter ou utiliser une autre adresse.',
            'email.regex' => 'Le format de l\'email n\'est pas valide.',
            
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            
            'role.required' => 'Le rôle est obligatoire.',
            'role.in' => 'Le rôle sélectionné n\'est pas valide. Choisissez parmi : Utilisateur, Réparateur ou Artisan.',
            
            'phone.regex' => 'Le format du numéro de téléphone n\'est pas valide.',
            'phone.min' => 'Le numéro de téléphone doit contenir au moins 8 chiffres.',
            'phone.max' => 'Le numéro de téléphone ne peut pas dépasser 20 caractères.',
            
            'address.min' => 'L\'adresse doit contenir au moins 5 caractères.',
            'address.max' => 'L\'adresse ne peut pas dépasser 500 caractères.',
            
            'terms.required' => 'Vous devez accepter les conditions d\'utilisation.',
            'terms.accepted' => 'Vous devez accepter les conditions d\'utilisation pour créer un compte.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'nom complet',
            'email' => 'adresse email',
            'password' => 'mot de passe',
            'role' => 'rôle',
            'phone' => 'téléphone',
            'address' => 'adresse',
            'terms' => 'conditions d\'utilisation',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Additional custom validation if needed
            if ($this->filled('email')) {
                // Check if email domain is blacklisted (optional)
                $blacklistedDomains = ['tempmail.com', 'throwaway.email', '10minutemail.com'];
                $emailDomain = substr(strrchr($this->email, "@"), 1);
                
                if (in_array($emailDomain, $blacklistedDomains)) {
                    $validator->errors()->add(
                        'email', 
                        'Les adresses email temporaires ne sont pas autorisées.'
                    );
                }
            }
        });
    }
}
