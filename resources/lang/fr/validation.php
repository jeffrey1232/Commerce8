<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'Le champ :attribute doit être accepté.',
    'accepted_if' => 'Le champ :attribute doit être accepté quand :other est :value.',
    'active_url' => 'Le champ :attribute n\'est pas une URL valide.',
    'after' => 'Le champ :attribute doit être une date postérieure au :date.',
    'after_or_equal' => 'Le champ :attribute doit être une date postérieure ou égale au :date.',
    'alpha' => 'Le champ :attribute doit contenir uniquement des lettres.',
    'alpha_dash' => 'Le champ :attribute doit contenir uniquement des lettres, chiffres et tirets.',
    'alpha_num' => 'Le champ :attribute doit contenir uniquement des lettres et chiffres.',
    'array' => 'Le champ :attribute doit être un tableau.',
    'ascii' => 'Le champ :attribute doit contenir uniquement des caractères alphanumériques et des symboles.',
    'before' => 'Le champ :attribute doit être une date antérieure au :date.',
    'before_or_equal' => 'Le champ :attribute doit être une date antérieure ou égale au :date.',
    'between' => [
        'array' => 'Le champ :attribute doit contenir entre :min et :max éléments.',
        'file' => 'La taille du fichier :attribute doit être comprise entre :min et :max kiloctets.',
        'numeric' => 'La valeur de :attribute doit être comprise entre :min et :max.',
        'string' => 'Le texte :attribute doit contenir entre :min et :max caractères.',
    ],
    'boolean' => 'Le champ :attribute doit être vrai ou faux.',
    'can' => 'Le champ :attribute contient une valeur non autorisée.',
    'confirmed' => 'Le champ de confirmation :attribute ne correspond pas.',
    'current_password' => 'Le mot de passe est incorrect.',
    'date' => 'Le champ :attribute n\'est pas une date valide.',
    'date_equals' => 'Le champ :attribute doit être une date égale à :date.',
    'date_format' => 'Le champ :attribute ne correspond pas au format :format.',
    'decimal' => 'Le champ :attribute doit avoir :decimal décimales.',
    'declined' => 'Le champ :attribute doit être refusé.',
    'declined_if' => 'Le champ :attribute doit être refusé quand :other est :value.',
    'different' => 'Les champs :attribute et :other doivent être différents.',
    'digits' => 'Le champ :attribute doit contenir :digits chiffres.',
    'digits_between' => 'Le champ :attribute doit contenir entre :min et :max chiffres.',
    'dimensions' => 'L\'image :attribute n\'a pas de dimensions valides.',
    'distinct' => 'Le champ :attribute a une valeur en double.',
    'doesnt_end_with' => 'Le champ :attribute ne peut pas se terminer par une des valeurs suivantes : :values.',
    'doesnt_start_with' => 'Le champ :attribute ne peut pas commencer par une des valeurs suivantes : :values.',
    'email' => 'Le champ :attribute doit être une adresse email valide.',
    'ends_with' => 'Le champ :attribute doit se terminer par une des valeurs suivantes : :values.',
    'enum' => 'Le champ :attribute sélectionné est invalide.',
    'exists' => 'Le champ :attribute sélectionné est invalide.',
    'file' => 'Le champ :attribute doit être un fichier.',
    'filled' => 'Le champ :attribute doit avoir une valeur.',
    'gt' => [
        'array' => 'Le champ :attribute doit contenir plus de :value éléments.',
        'file' => 'La taille du fichier :attribute doit être supérieure à :value kiloctets.',
        'numeric' => 'La valeur de :attribute doit être supérieure à :value.',
        'string' => 'Le texte :attribute doit contenir plus de :value caractères.',
    ],
    'gte' => [
        'array' => 'Le champ :attribute doit contenir au moins :value éléments.',
        'file' => 'La taille du fichier :attribute doit être supérieure ou égale à :value kiloctets.',
        'numeric' => 'La valeur de :attribute doit être supérieure ou égale à :value.',
        'string' => 'Le texte :attribute doit contenir au moins :value caractères.',
    ],
    'image' => 'Le champ :attribute doit être une image.',
    'in' => 'Le champ :attribute sélectionné est invalide.',
    'in_array' => 'Le champ :attribute n\'existe pas dans :other.',
    'integer' => 'Le champ :attribute doit être un entier.',
    'ip' => 'Le champ :attribute doit être une adresse IP valide.',
    'ipv4' => 'Le champ :attribute doit être une adresse IPv4 valide.',
    'ipv6' => 'Le champ :attribute doit être une adresse IPv6 valide.',
    'json' => 'Le champ :attribute doit être une chaîne JSON valide.',
    'lowercase' => 'Le champ :attribute doit être en minuscules.',
    'lt' => [
        'array' => 'Le champ :attribute doit contenir moins de :value éléments.',
        'file' => 'La taille du fichier :attribute doit être inférieure à :value kiloctets.',
        'numeric' => 'La valeur de :attribute doit être inférieure à :value.',
        'string' => 'Le texte :attribute doit contenir moins de :value caractères.',
    ],
    'lte' => [
        'array' => 'Le champ :attribute doit contenir au plus :value éléments.',
        'file' => 'La taille du fichier :attribute doit être inférieure ou égale à :value kiloctets.',
        'numeric' => 'La valeur de :attribute doit être inférieure ou égale à :value.',
        'string' => 'Le texte :attribute doit contenir au plus :value caractères.',
    ],
    'mac_address' => 'Le champ :attribute doit être une adresse MAC valide.',
    'max' => [
        'array' => 'Le champ :attribute ne peut pas contenir plus de :max éléments.',
        'file' => 'La taille du fichier :attribute ne peut pas dépasser :max kiloctets.',
        'numeric' => 'La valeur de :attribute ne peut pas dépasser :max.',
        'string' => 'Le texte :attribute ne peut pas contenir plus de :max caractères.',
    ],
    'max_digits' => 'Le champ :attribute ne peut pas avoir plus de :max chiffres.',
    'mimes' => 'Le champ :attribute doit être un fichier de type : :values.',
    'mimetypes' => 'Le champ :attribute doit être un fichier de type : :values.',
    'min' => [
        'array' => 'Le champ :attribute doit contenir au moins :min éléments.',
        'file' => 'La taille du fichier :attribute doit être d\'au moins :min kiloctets.',
        'numeric' => 'La valeur de :attribute doit être au moins :min.',
        'string' => 'Le texte :attribute doit contenir au moins :min caractères.',
    ],
    'min_digits' => 'Le champ :attribute doit avoir au moins :min chiffres.',
    'missing' => 'Le champ :attribute doit être manquant.',
    'missing_if' => 'Le champ :attribute doit être manquant quand :other est :value.',
    'missing_unless' => 'Le champ :attribute doit être manquant à moins que :other ne soit :value.',
    'missing_with' => 'Le champ :attribute doit être manquant quand :values est présent.',
    'missing_with_all' => 'Le champ :attribute doit être manquant quand :values sont présents.',
    'multiple_of' => 'Le champ :attribute doit être un multiple de :value.',
    'not_in' => 'Le champ :attribute sélectionné est invalide.',
    'not_regex' => 'Le format du champ :attribute est invalide.',
    'numeric' => 'Le champ :attribute doit être un nombre.',
    'password' => 'Le mot de passe est incorrect.',
    'present' => 'Le champ :attribute doit être présent.',
    'prohibited' => 'Le champ :attribute est interdit.',
    'prohibited_if' => 'Le champ :attribute est interdit quand :other est :value.',
    'prohibited_unless' => 'Le champ :attribute est interdit à moins que :other ne soit dans :values.',
    'prohibits' => 'Le champ :attribute interdit :other d\'être présent.',
    'regex' => 'Le format du champ :attribute est invalide.',
    'required' => 'Le champ :attribute est requis.',
    'required_array_keys' => 'Le champ :attribute doit contenir les clés : :values.',
    'required_if' => 'Le champ :attribute est requis quand :other est :value.',
    'required_unless' => 'Le champ :attribute est requis sauf si :other est dans :values.',
    'required_with' => 'Le champ :attribute est requis quand :values est présent.',
    'required_with_all' => 'Le champ :attribute est requis quand :values sont présents.',
    'required_without' => 'Le champ :attribute est requis quand :values n\'est pas présent.',
    'required_without_all' => 'Le champ :attribute est requis quand aucun des :values n\'est présent.',
    'same' => 'Le champ :attribute et :other doivent être identiques.',
    'size' => [
        'array' => 'Le champ :attribute doit contenir :size éléments.',
        'file' => 'La taille du fichier :attribute doit être de :size kiloctets.',
        'numeric' => 'La valeur de :attribute doit être :size.',
        'string' => 'Le texte :attribute doit contenir :size caractères.',
    ],
    'starts_with' => 'Le champ :attribute doit commencer par une des valeurs suivantes : :values.',
    'string' => 'Le champ :attribute doit être une chaîne de caractères.',
    'timezone' => 'Le champ :attribute doit être une zone horaire valide.',
    'unique' => 'Le champ :attribute est déjà utilisé.',
    'uploaded' => 'Le fichier :attribute n\'a pas pu être téléchargé.',
    'uppercase' => 'Le champ :attribute doit être en majuscules.',
    'url' => 'Le champ :attribute doit être une URL valide.',
    'ulid' => 'Le champ :attribute doit être un ULID valide.',
    'uuid' => 'Le champ :attribute doit être un UUID valide.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "rule.attribute" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [
        'name' => 'nom',
        'email' => 'adresse email',
        'password' => 'mot de passe',
        'password_confirmation' => 'confirmation du mot de passe',
        'first_name' => 'prénom',
        'last_name' => 'nom de famille',
        'phone' => 'téléphone',
        'address' => 'adresse',
        'city' => 'ville',
        'country' => 'pays',
        'postal_code' => 'code postal',
        'tracking_code' => 'code de suivi',
        'product_name' => 'nom du produit',
        'description' => 'description',
        'price' => 'prix',
        'quantity' => 'quantité',
        'total_amount' => 'montant total',
        'client_phone' => 'téléphone client',
        'client_email' => 'email client',
        'vendor_id' => 'vendeur',
        'point_relais_id' => 'point relais',
        'status' => 'statut',
    ],
];
