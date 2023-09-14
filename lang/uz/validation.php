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

    'accepted' => ":attribute maydon qabul qilinishi shart.",
    'accepted_if' => ":attribute maydon faqat :other :value bo'lganida qabul qilinishi shart.",
    'active_url' => ":attribute maydon to'g'ri URL bo'lishi kerak.",
    'after' => ":attribute maydon belgilangan sanadan keyin bo'lishi kerak.",
    'after_or_equal' => ":attribute maydon belgilangan sanaga teng yoki keyin bo'lishi kerak.",
    'alpha' => ":attribute maydon faqat harflardan iborat bo'lishi kerak.",
    'alpha_dash' => ":attribute maydon faqat harflar, raqamlar, chiziq va pastki chiziq belgilardan iborat bo'lishi kerak.",
    'alpha_num' => ":attribute maydon faqat harflar va raqamlardan iborat bo'lishi kerak.",
    'array' => ":attribute maydon massiv bo'lishi kerak.",
    'ascii' => ":attribute maydon faqat bir baytlik alfa sonlar va belgilar bo'lishi kerak.",
    'before' => ":attribute maydon belgilangan sanadan oldin bo'lishi kerak.",
    'before_or_equal' => ":attribute maydon belgilangan sanaga teng yoki oldin bo'lishi kerak.",
    'between' => [
        'array' => ':attribute maydonida :min va :max orasida bo\'lishi kerak.',
        'file' => ':attribute maydoni :min va :max kilobaytlar orasida bo\'lishi kerak.',
        'numeric' => ':attribute maydoni :min va :max orasida bo\'lishi kerak',
        'string' => ':attribute maydoni :min va :max belgilar orasida bo\'lishi kerak.',
    ],
    'boolean' => ":attribute maydon \"rost\" yoki \"yolg'on\" qiymatlarda bo'lishi kerak.",
    'can' => ":attribute maydon ruxsat berilmagan qiymatni o'z ichiga olishi kerak.",
    'confirmed' => ":attribute maydon mos kelmadi.",
    'current_password' => "Parol noto'g'ri.",
    'date' => ":attribute maydon to'g'ri san'ga ega bo'lishi kerak.",
    'date_equals' => ":attribute maydon :date ga teng sanaga ega bo'lishi kerak.",
    'date_format' => ":attribute maydon :format formatiga mos kelishi kerak.",
    'decimal' => ":attribute maydon :decimal bo'ladigan burchaklarga ega bo'lishi kerak.",
    'declined' => ":attribute maydon rad etilishi shart.",
    'declined_if' => ":attribute maydon faqat :other :value bo'lganida rad etilishi shart.",
    'different' => ":attribute maydon :other dan farq qilishi kerak.",
    'digits' => ":attribute maydon :digits raqamdan iborat bo'lishi kerak.",
    'digits_between' => ":attribute maydon :min va :max oraliqda raqamdan iborat bo'lishi kerak.",
    'dimensions' => ":attribute maydon tasvirning noto'g'ri o'lchamlarga ega.",
    'distinct' => ":attribute maydon unikal qiymatga ega bo'lishi kerak.",
    'doesnt_end_with' => ":attribute maydon quyidagi belgilardan biri bilan tugamaydi: :values.",
    'doesnt_start_with' => ":attribute maydon quyidagi belgilardan biri bilan boshlanmaydi: :values.",
    'email' => ":attribute maydon to'g'ri elektron pochta manziliga ega bo'lishi kerak.",
    'ends_with' => ":attribute maydon quyidagi belgilardan biri bilan tugashi kerak: :values.",
    'enum' => " :attribute yaroqsiz.",
    'exists' => ":attribute yaroqsiz.",
    'file' => ":attribute maydon fayl bo'lishi kerak.",
    'filled' => ":attribute maydon qiymati to'ldirilishi kerak.",
    'gt' => [
        'array' => ":attribute maydonida :value dan ortiq bo'lishi kerak.",
        'file' => ":attribute maydoni :value kilobaytdan katta bo'lishi kerak.",
        'numeric' => ":attribute maydoni :value dan katta bo'lishi kerak.",
        'string' => ":attribute maydoni :value belgilaridan katta bo'lishi kerak.",
    ],
    'gte' => [
        'array' => ":attribute maydonida :value elementi yoki undan ko'p bo'lishi kerak.",
        'file' => ":attribute maydoni :value kilobaytdan katta yoki teng bo'lishi kerak.",
        'numeric' => ":attribute maydoni :value dan katta yoki teng bo'lishi kerak.",
        'string' => ":attribute maydoni :value belgilaridan katta yoki teng bo'lishi kerak.",
    ],
    'image' => ":attribute maydon tasvir bo'lishi kerak.",
    'in' => ":attribute yaroqsiz.",
    'in_array' => ":attribute maydon :other ichida bo'lishi kerak.",
    'integer' => ":attribute maydon butun son bo'lishi kerak.",
    'ip' => ":attribute maydon to'g'ri IP manziliga ega bo'lishi kerak.",
    'ipv4' => ":attribute maydon to'g'ri IPv4 manziliga ega bo'lishi kerak.",
    'ipv6' => ":attribute maydon to'g'ri IPv6 manziliga ega bo'lishi kerak.",
    'json' => ":attribute maydon to'g'ri JSON qatoriga ega bo'lishi kerak.",
    'lowercase' => ":attribute maydon kichik harflardan iborat bo'lishi kerak.",
    'lt' => [
        'array' => ":attribute maydonida :value elementidan kamroq bo'lishi kerak.",
        'file' => ":attribute maydoni :value kilobaytdan kichik bo'lishi kerak.",
        'numeric' => ":attribute maydoni :value dan kichik bo'lishi kerak.",
        'string' => ":attribute maydoni :value belgilardan kichik bo'lishi kerak.",
    ],
    'lte' => [
        'array' => ":attribute maydonida :value dan ortiq bo'lmasligi kerak.",
        'file' => ":attribute maydoni :value kilobaytdan kichik yoki teng bo'lishi kerak.",
        'numeric' => ":attribute maydoni :value dan kichik yoki teng bo'lishi kerak.",
        'string' => ":attribute maydoni :value belgilaridan kichik yoki teng bo'lishi kerak.",
    ],
    'mac_address' => ":attribute maydon to'g'ri MAC manziliga ega bo'lishi kerak.",
    'max' => [
        'array' => ":attribute maydonida :max dan ortiq elementlar bo'lmasligi kerak.",
        'file' => ":attribute maydoni :max kilobaytdan katta bo'lmasligi kerak.",
        'numeric' => ":attribute maydoni :max dan katta bo'lmasligi kerak",
        'string' => ":attribute maydoni :max belgilardan katta bo'lmasligi kerak.",
    ],
    'max_digits' => ":attribute maydon :max ta raqamdan ko'p bo'lmagan bo'lishi kerak.",
    'mimes' => ":attribute maydon quyidagi fayl turlaridan biriga ega bo'lishi kerak: :values.",
    'mimetypes' => ":attribute maydon quyidagi fayl turlaridan biriga ega bo'lishi kerak: {values}.",
    'min' => [
        'array' => ":attribute maydonida kamida :min elementlar bo'lishi kerak.",
        'file' => ":attribute maydoni kamida :min kilobayt bo'lishi kerak.",
        'numeric' => ":attribute maydoni kamida :min bo'lishi kerak",
        'string' => ":attribute maydoni kamida :min belgidan iborat bo'lishi kerak.",
    ],
    'min_digits' => ":attribute maydon kamida :min ta raqamdan iborat bo'lishi kerak.",
    'missing' => ":attribute maydon yo'q bo'lishi kerak.",
    'missing_if' => ":attribute maydon faqat :other :value bo'lganida yo'q bo'lishi kerak.",
    'missing_unless' => ":attribute maydon faqat :other :value bo'lganida yo'q bo'lishi kerak.",
    'missing_with' => ":attribute maydon :values mavjud bo'lganda yo'q bo'lishi kerak.",
    'missing_with_all' => ":attribute maydon :values mavjud bo'lganda yo'q bo'lishi kerak.",
    'multiple_of' => ":attribute maydon :value ga ko'paytma bo'lishi kerak.",
    'not_in' => "Tanlangan :attribute yaroqda yo'q.",
    'not_regex' => ":attribute maydon formati noto'g'ri.",
    'numeric' => ":attribute maydon raqam bo'lishi kerak.",
    'password' => [
        'letters' => ":attribute maydonida kamida bitta harf bo'lishi kerak.",
        'mixed' => ":attribute maydonida kamida bitta katta va bitta kichik harf bo'lishi kerak.",
        'numbers' => ":attribute maydonida kamida bitta raqam bo'lishi kerak.",
        'symbols' => ":attribute maydonida kamida bitta belgi bo'lishi kerak.",
        'uncompromised' => ":attribute ma'lumotlar sizib chiqishida paydo bo'ldi. Boshqa :attribute tanlang.",
    ],
    'present' => ":attribute maydon mavjud bo'lishi kerak.",
    'prohibited' => ":attribute maydon taqiqlangan.",
    'prohibited_if' => ":attribute maydon faqat :other :value bo'lganida taqiqlangan.",
    'prohibited_unless' => ":attribute maydon faqat :other :value bo'lganida taqiqlangan.",
    'prohibits' => ":attribute maydon quyidagi :other mavjud bo'lganda :attribute ni taqiqlaydi.",
    'regex' => ":attribute maydon formati noto'g'ri.",
    'required' => ":attribute maydoni talab qilinadi.",
    'required_array_keys' => ":attribute maydonida: :values uchun yozuvlar bo'lishi kerak.",
    'required_if' => ":attribute maydoni faqat :other :value bo'lganida talab qilinadi.",
    'required_if_accepted' => ":attribute maydoni faqat :other qabul qilinayotganida talab qilinadi.",
    'required_unless' => ":attribute maydoni faqat :other :values ichida bo'lganida talab qilinadi.",
    'required_with' => ":attribute maydoni faqat :values mavjud bo'lganda talab qilinadi.",
    'required_with_all' => ":attribute maydoni faqat :values mavjud bo'lganda talab qilinadi.",
    'required_without' => ":attribute maydoni faqat :values mavjud bo'lmaganida talab qilinadi.",
    'required_without_all' => ":attribute maydoni :values mavjud bo'lmaganida talab qilinadi.",
    'same' => ":attribute maydoni :other bilan mos kelishi kerak.",
    'size' => [
        'array' => ":attribute maydonida :size elementlari bo'lishi kerak.",
        'file' => ":attribute maydoni :size kilobayt bo'lishi kerak.",
        'numeric' => ":attribute maydoni :size bo'lishi kerak.",
        'string' => ":attribute maydoni :size belgilar bo'lishi kerak.",
    ],
    'starts_with' => ":attribute quyidagi belgilardan biri bilan boshlanishi kerak: :values.",
    'string' => ":attribute maydoni qator bo'lishi kerak.",
    'timezone' => ":attribute to'g'ri vaqt zonasiga ega bo'lishi kerak.",
    'unique' => ":attribute allaqachon olingan.",
    'uploaded' => ":attribute yuklab bo'lmadi.",
    'uppercase' => ":attribute maydoni katta harflardan iborat bo'lishi kerak.",
    'url' => ":attribute maydoni to'g'ri URL manziliga ega bo'lishi kerak.",
    'ulid' => ":attribute maydoni to'g'ri ULID manziliga ega bo'lishi kerak.",
    'uuid' => ":attribute maydoni to'g'ri UUID manziliga ega bo'lishi kerak.",

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
        'users' => 'Rollar',
        'roles' => 'Foydalanuvchilar',
        'inactive_users' => 'Faol bo\'lmagan foydalanuvchilar',
        'weekdays' => 'Hafta kunlari',
        'teachers' => 'O\'qituvchilar',
        'assistant_teachers' => 'O\'qituvchining yordamchilari',
        'courses' => 'Kurslar',
        'lessons' => 'Darslar',
        'groups' => 'Guruhlar',
        'students' => 'Talabalar',
        'stparents' => 'Ota-ona',
        'sessions' => 'Sessiyalar',
        'branches' => 'Filiallar',
        'rooms' => 'Xonalar',
        'schedules' => 'Jadvallar',
        'certificates' => 'Sertifikatlar',
        'failedsts' => 'Muvaffaqiyatsiz talabalar',
        'failedgroups' => 'Muvaffaqiyatsiz guruhlar',
        'cashiers' => 'Kassir',
        'access_for_courses' => 'Kurslarga kirish',
        'cards' => 'Kartalar',
        'payments' => 'To\'lovlar',
        'changes' => 'O\'zgarishlar',
        'name' => 'Ism',
        'location' => 'Joylashuv',
        'firstname' => 'Ism',
        'lastname' => 'Familiya',
        'contact' => 'Kontakt',
        'email' => 'Elektron pochta',
        'password' => 'Parol',
        'status' => 'Status',
        'lang' => 'Til',
        'price' => 'Narx',
        'sequence_number' => 'Tartib raqami',
        'completed_lessons' => 'Tugallangan darslar',
        'start' => 'Boshlash',
        'end' => 'Tugatish',
        'reason' => 'Sabab',
        'cashier_key' => 'Kassir kaliti',
        'pay_time' => 'To\'lov vaqti',
        'expire_time' => 'Muddati tugashi',
        'card_number' => 'Karta raqami',
        'card_expiration' => 'Kartaning amal qilish muddati',
        'card_token' => 'Karta tokeni',
        'type' => 'Tur',
        'card' => 'Karta',
        'cash' => 'Naqd',
        'amount' => 'Summa',
        'created_at' => 'Yaratilgan',
        'role_id' => 'Rol identifikatori',
        'teacher_id' => 'O\'qituvchi identifikatori',
        'assistant_teacher_id' => 'O\'qituvchi yordamchisi identifikatori',
        'course_id' => 'Kurs identifikatori',
        'branch_id' => 'Filial identifikatori',
        'student_id' => 'Talaba identifikatori',
        'cashier_id' => 'Kassir identifikatori',
    ],
];
