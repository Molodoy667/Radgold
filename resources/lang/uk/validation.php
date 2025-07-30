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

    'accepted' => 'Поле :attribute має бути прийнято.',
    'active_url' => 'Поле :attribute не є дійсною URL-адресою.',
    'after' => 'Поле :attribute має бути датою після :date.',
    'after_or_equal' => 'Поле :attribute має бути датою після або рівною :date.',
    'alpha' => 'Поле :attribute може містити тільки літери.',
    'alpha_dash' => 'Поле :attribute може містити тільки літери, цифри, тире та підкреслення.',
    'alpha_num' => 'Поле :attribute може містити тільки літери та цифри.',
    'array' => 'Поле :attribute має бути масивом.',
    'before' => 'Поле :attribute має бути датою перед :date.',
    'before_or_equal' => 'Поле :attribute має бути датою перед або рівною :date.',
    'between' => [
        'numeric' => 'Поле :attribute має бути між :min та :max.',
        'file' => 'Поле :attribute має бути між :min та :max кілобайт.',
        'string' => 'Поле :attribute має бути між :min та :max символів.',
        'array' => 'Поле :attribute має містити між :min та :max елементів.',
    ],
    'boolean' => 'Поле :attribute має бути true або false.',
    'confirmed' => 'Підтвердження поля :attribute не співпадає.',
    'date' => 'Поле :attribute не є дійсною датою.',
    'date_equals' => 'Поле :attribute має бути датою рівною :date.',
    'date_format' => 'Поле :attribute не відповідає формату :format.',
    'different' => 'Поля :attribute та :other мають бути різними.',
    'digits' => 'Поле :attribute має бути :digits цифр.',
    'digits_between' => 'Поле :attribute має бути між :min та :max цифр.',
    'dimensions' => 'Поле :attribute має недійсні розміри зображення.',
    'distinct' => 'Поле :attribute має дублікат значення.',
    'email' => 'Поле :attribute має бути дійсною email-адресою.',
    'ends_with' => 'Поле :attribute має закінчуватися одним з наступних: :values.',
    'exists' => 'Вибране поле :attribute недійсне.',
    'file' => 'Поле :attribute має бути файлом.',
    'filled' => 'Поле :attribute має мати значення.',
    'gt' => [
        'numeric' => 'Поле :attribute має бути більше ніж :value.',
        'file' => 'Поле :attribute має бути більше ніж :value кілобайт.',
        'string' => 'Поле :attribute має бути більше ніж :value символів.',
        'array' => 'Поле :attribute має містити більше ніж :value елементів.',
    ],
    'gte' => [
        'numeric' => 'Поле :attribute має бути більше або рівне :value.',
        'file' => 'Поле :attribute має бути більше або рівне :value кілобайт.',
        'string' => 'Поле :attribute має бути більше або рівне :value символів.',
        'array' => 'Поле :attribute має містити :value елементів або більше.',
    ],
    'image' => 'Поле :attribute має бути зображенням.',
    'in' => 'Вибране поле :attribute недійсне.',
    'in_array' => 'Поле :attribute не існує в :other.',
    'integer' => 'Поле :attribute має бути цілим числом.',
    'ip' => 'Поле :attribute має бути дійсною IP-адресою.',
    'ipv4' => 'Поле :attribute має бути дійсною IPv4-адресою.',
    'ipv6' => 'Поле :attribute має бути дійсною IPv6-адресою.',
    'json' => 'Поле :attribute має бути дійсним JSON-рядком.',
    'lt' => [
        'numeric' => 'Поле :attribute має бути менше ніж :value.',
        'file' => 'Поле :attribute має бути менше ніж :value кілобайт.',
        'string' => 'Поле :attribute має бути менше ніж :value символів.',
        'array' => 'Поле :attribute має містити менше ніж :value елементів.',
    ],
    'lte' => [
        'numeric' => 'Поле :attribute має бути менше або рівне :value.',
        'file' => 'Поле :attribute має бути менше або рівне :value кілобайт.',
        'string' => 'Поле :attribute має бути менше або рівне :value символів.',
        'array' => 'Поле :attribute не може містити більше ніж :value елементів.',
    ],
    'max' => [
        'numeric' => 'Поле :attribute не може бути більше ніж :max.',
        'file' => 'Поле :attribute не може бути більше ніж :max кілобайт.',
        'string' => 'Поле :attribute не може бути більше ніж :max символів.',
        'array' => 'Поле :attribute не може містити більше ніж :max елементів.',
    ],
    'mimes' => 'Поле :attribute має бути файлом типу: :values.',
    'mimetypes' => 'Поле :attribute має бути файлом типу: :values.',
    'min' => [
        'numeric' => 'Поле :attribute має бути не менше ніж :min.',
        'file' => 'Поле :attribute має бути не менше ніж :min кілобайт.',
        'string' => 'Поле :attribute має бути не менше ніж :min символів.',
        'array' => 'Поле :attribute має містити не менше ніж :min елементів.',
    ],
    'multiple_of' => 'Поле :attribute має бути кратним :value',
    'not_in' => 'Вибране поле :attribute недійсне.',
    'not_regex' => 'Формат поля :attribute недійсний.',
    'numeric' => 'Поле :attribute має бути числом.',
    'password' => 'Пароль неправильний.',
    'present' => 'Поле :attribute має бути присутнім.',
    'regex' => 'Формат поля :attribute недійсний.',
    'required' => 'Поле :attribute обов\'язкове.',
    'required_if' => 'Поле :attribute обов\'язкове, коли :other є :value.',
    'required_unless' => 'Поле :attribute обов\'язкове, якщо :other не знаходиться в :values.',
    'required_with' => 'Поле :attribute обов\'язкове, коли :values присутнє.',
    'required_with_all' => 'Поле :attribute обов\'язкове, коли :values присутні.',
    'required_without' => 'Поле :attribute обов\'язкове, коли :values відсутнє.',
    'required_without_all' => 'Поле :attribute обов\'язкове, коли жодне з :values не присутнє.',
    'same' => 'Поля :attribute та :other мають співпадати.',
    'size' => [
        'numeric' => 'Поле :attribute має бути :size.',
        'file' => 'Поле :attribute має бути :size кілобайт.',
        'string' => 'Поле :attribute має бути :size символів.',
        'array' => 'Поле :attribute має містити :size елементів.',
    ],
    'starts_with' => 'Поле :attribute має починатися з одного з наступних: :values.',
    'string' => 'Поле :attribute має бути рядком.',
    'timezone' => 'Поле :attribute має бути дійсною зоною.',
    'unique' => 'Поле :attribute вже зайнято.',
    'uploaded' => 'Поле :attribute не вдалося завантажити.',
    'url' => 'Формат поля :attribute недійсний.',
    'uuid' => 'Поле :attribute має бути дійсним UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
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

    'attributes' => [],

];