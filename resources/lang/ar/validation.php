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

    'accepted' => ':attribute يجب قبوله.',
'active_url' => ':attribute ليس رابطًا صالحًا.',
'after' => ':attribute يجب أن يكون تاريخًا بعد :date.',
'after_or_equal' => ':attribute يجب أن يكون تاريخًا بعد أو يساوي :date.',
'alpha' => ':attribute يجب أن يحتوي على حروف فقط.',
'alpha_dash' => ':attribute يجب أن يحتوي على حروف وأرقام وشرطات وشرطة سفلية فقط.',
'alpha_num' => ':attribute يجب أن يحتوي على حروف وأرقام فقط.',
'array' => ':attribute يجب أن يكون مصفوفة.',
'before' => ':attribute يجب أن يكون تاريخًا قبل :date.',
'before_or_equal' => ':attribute يجب أن يكون تاريخًا قبل أو يساوي :date.',

    'between' => [
        'numeric' => ':attribute يجب أن يكون بين :min و :max.',
    'file'    => ':attribute يجب أن يكون حجمه بين :min و :max كيلوبايت.',
    'string'  => ':attribute يجب أن يحتوي على عدد أحرف بين :min و :max.',
    'array'   => ':attribute يجب أن يحتوي على عدد عناصر بين :min و :max.',
    ],
    'boolean' => ':attribute يجب أن تكون قيمته صحيحة أو خاطئة.',
'confirmed' => ':attribute غير متطابقة.',
'date' => ':attribute ليس تاريخًا صالحًا.',
'date_equals' => ':attribute يجب أن يكون تاريخًا مساويًا لـ :date.',
'date_format' => ':attribute لا يطابق التنسيق :format.',
'different' => ':attribute و :other يجب أن يكونا مختلفين.',
'digits' => ':attribute يجب أن يحتوي على :digits أرقام.',
'digits_between' => ':attribute يجب أن يحتوي على عدد أرقام بين :min و :max.',
'dimensions' => ':attribute أبعاده غير صالحة.',
'distinct' => ':attribute يحتوي على قيمة مكررة.',
'email' => ':attribute يجب أن يكون بريدًا إلكترونيًا صالحًا.',
'ends_with' => ':attribute يجب أن ينتهي بأحد القيم التالية: :values.',
'exists' => ':attribute المحدد غير صالح.',
'file' => ':attribute يجب أن يكون ملفًا.',
'filled' => ':attribute يجب أن يحتوي على قيمة.',
'gt' => [
    'numeric' => ':attribute يجب أن يكون أكبر من :value.',
    'file' => ':attribute يجب أن يكون حجمه أكبر من :value كيلوبايت.',
    'string' => ':attribute يجب أن يحتوي على عدد أحرف أكبر من :value.',
    'array' => ':attribute يجب أن يحتوي على عدد عناصر أكبر من :value.',
],
'gte' => [
    'numeric' => ':attribute يجب أن يكون أكبر من أو يساوي :value.',
    'file' => ':attribute يجب أن يكون حجمه أكبر من أو يساوي :value كيلوبايت.',
    'string' => ':attribute يجب أن يحتوي على عدد أحرف أكبر من أو يساوي :value.',
    'array' => ':attribute يجب أن يحتوي على :value عنصر أو أكثر.',
],
'image' => ':attribute يجب أن يكون صورة.',
'in' => ':attribute المحدد غير صالح.',
'in_array' => ':attribute غير موجود في :other.',
'integer' => ':attribute يجب أن يكون عددًا صحيحًا.',
'ip' => ':attribute يجب أن يكون عنوان IP صالحًا.',
'ipv4' => ':attribute يجب أن يكون عنوان IPv4 صالحًا.',
'ipv6' => ':attribute يجب أن يكون عنوان IPv6 صالحًا.',
'json' => ':attribute يجب أن يكون نص JSON صالحًا.',
'lt' => [
    'numeric' => ':attribute يجب أن يكون أقل من :value.',
    'file' => ':attribute يجب أن يكون حجمه أقل من :value كيلوبايت.',
    'string' => ':attribute يجب أن يحتوي على عدد أحرف أقل من :value.',
    'array' => ':attribute يجب أن يحتوي على عدد عناصر أقل من :value.',
],
'lte' => [
    'numeric' => ':attribute يجب أن يكون أقل من أو يساوي :value.',
    'file' => ':attribute يجب أن يكون حجمه أقل من أو يساوي :value كيلوبايت.',
    'string' => ':attribute يجب أن يحتوي على عدد أحرف أقل من أو يساوي :value.',
    'array' => ':attribute يجب ألا يحتوي على أكثر من :value عنصر.',
],
'max' => [
    'numeric' => ':attribute يجب ألا يكون أكبر من :max.',
    'file' => ':attribute يجب ألا يكون حجمه أكبر من :max كيلوبايت.',
    'string' => ':attribute يجب ألا يحتوي على عدد أحرف أكبر من :max.',
    'array' => ':attribute يجب ألا يحتوي على أكثر من :max عنصر.',
],
'mimes' => ':attribute يجب أن يكون ملفًا من النوع: :values.',
'mimetypes' => ':attribute يجب أن يكون ملفًا من النوع: :values.',
'min' => [
    'numeric' => ':attribute يجب أن يكون على الأقل :min.',
    'file' => ':attribute يجب أن يكون حجمه على الأقل :min كيلوبايت.',
    'string' => ':attribute يجب أن يحتوي على الأقل :min حرفًا.',
    'array' => ':attribute يجب أن يحتوي على الأقل :min عنصرًا.',
],
'multiple_of' => ':attribute يجب أن يكون مضاعفًا لـ :value.',
'not_in' => ':attribute المحدد غير صالح.',
'not_regex' => ':attribute تنسيقه غير صالح.',
'numeric' => ':attribute يجب أن يكون رقمًا.',
'password' => [
    'min' => 'كلمة المرور يجب أن تكون على الأقل :min حرفًا.',
    'mixed' => 'كلمة المرور يجب أن تحتوي على حرف كبير وحرف صغير على الأقل.',
    'numbers' => 'كلمة المرور يجب أن تحتوي على رقم واحد على الأقل.',
    'symbols' => 'كلمة المرور يجب أن تحتوي على رمز واحد على الأقل.',
    'uncompromised' => 'كلمة المرور ظهرت في تسريب بيانات ولا يمكن استخدامها. يرجى اختيار كلمة مرور أخرى.',
],
'present' => ':attribute يجب أن يكون موجودًا.',
'regex' => ':attribute تنسيقه غير صالح.',
'Email field is required' => 'حقل البريد الإلكتروني مطلوب',
'required' => ':attribute مطلوب.',
'required_if' => ':attribute مطلوب عندما يكون :other هو :value.',
'required_unless' => ':attribute مطلوب إلا إذا كان :other ضمن :values.',
'required_with' => ':attribute مطلوب عند وجود :values.',
'required_with_all' => ':attribute مطلوب عند وجود جميع :values.',
'required_without' => ':attribute مطلوب عند غياب :values.',
'required_without_all' => ':attribute مطلوب عند غياب جميع :values.',
'prohibited_if' => ':attribute ممنوع عندما يكون :other هو :value.',
'prohibited_unless' => ':attribute ممنوع إلا إذا كان :other ضمن :values.',
'same' => ':attribute يجب أن يطابق :other.',
'size' => [
    'numeric' => ':attribute يجب أن يكون :size.',
    'file' => ':attribute يجب أن يكون حجمه :size كيلوبايت.',
    'string' => ':attribute يجب أن يحتوي على :size حرفًا.',
    'array' => ':attribute يجب أن يحتوي على :size عنصرًا.',
],
'starts_with' => ':attribute يجب أن يبدأ بأحد القيم التالية: :values.',
'string' => ':attribute يجب أن يكون نصًا.',
'timezone' => ':attribute يجب أن يكون نطاقًا زمنيًا صالحًا.',
'unique' => ':attribute مستخدم من قبل.',
'uploaded' => 'فشل رفع :attribute.',
'url' => ':attribute تنسيقه غير صالح.',
'uuid' => 'يجب أن يكون صالحًا :attribute  UUID .', 
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

    'attributes' => [
    'email' => 'البريد الإلكتروني',
    'password' => 'كلمة المرور',
    'username' => 'اسم المستخدم',
    'name' => 'الاسم',
],


];
