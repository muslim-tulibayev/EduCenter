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

    'accepted' => 'Поле :attribute должно быть принято.',
    'accepted_if' => 'Поле :attribute должно быть принято, если :other равно :value.',
    'active_url' => 'Поле :attribute должно быть действительным URL-адресом.',
    'after' => 'Поле :attribute должно быть датой после :date.',
    'after_or_equal' => 'Поле :attribute должно быть датой после или равной :date.',
    'alpha' => 'Поле :attribute должно содержать только буквы.',
    'alpha_dash' => 'Поле :attribute должно содержать только буквы, цифры, тире и символы подчеркивания.',
    'alpha_num' => 'Поле :attribute должно содержать только буквы и цифры.',
    'array' => 'Поле :attribute должно быть массивом.',
    'ascii' => 'Поле :attribute должно содержать только однобайтовые буквенно-цифровые символы и символы.',
    'before' => 'Поле :attribute должно быть датой до :date.',
    'before_or_equal' => 'Поле :attribute должно быть датой, предшествующей :date или равной ей.',
    'between' => [
        'array' => 'Поле :attribute должно содержать элементы от :min до :max.',
        'file' => 'Поле :attribute должно быть в диапазоне от :min до :max килобайт.',
        'numeric' => 'Поле :attribute должно быть между :min и :max.',
        'string' => 'Поле :attribute должно содержать символы от :min до :max.',
    ],
    'boolean' => 'Поле :attribute должно иметь значение true или false.',
    'can' => 'Поле :attribute содержит несанкционированное значение.',
    'confirmed' => 'Подтверждение поля :attribute не соответствует.',
    'current_password' => 'Пароль неверен.',
    'date' => 'В поле :attribute должна быть указана действительная дата.',
    'date_equals' => 'Поле :attribute должна содержать дату, равную :date.',
    'date_format' => 'Поле :attribute должно соответствовать формату :format.',
    'decimal' => 'Поле :attribute должно содержать :decimal десятичные знаки.',
    'declined' => 'Поле :attribute должно быть отклонено.',
    'declined_if' => 'Поле :attribute должно быть отклонено, если :other равно :value.',
    'different' => 'Поля :attribute и :other должны быть разными.',
    'digits' => 'Поле :attribute должно быть :digits digits.',
    'digits_between' => 'Поле :attribute должно содержать цифры от :min до :max.',
    'dimensions' => 'Поле :attribute имеет недопустимые размеры изображения.',
    'distinct' => 'Поле :attribute имеет повторяющееся значение.',
    'doesnt_end_with' => 'Поле :attribute не должно заканчиваться одним из следующих символов: :values.',
    'doesnt_start_with' => 'Поле :attribute не должно начинаться с одного из следующих значений: :values.',
    'email' => 'Поле :attribute должно быть действительным адресом электронной почты.',
    'ends_with' => 'Поле :attribute должно заканчиваться одним из следующих символов: :values.',
    'enum' => 'Выбранный :attribute недействителен.',
    'exists' => 'Выбранный :attribute недействителен.',
    'file' => 'Поле :attribute должно быть файлом.',
    'filled' => 'Поле :attribute должно иметь значение.',
    'gt' => [
        'array' => 'Поле :attribute должно содержать более :value элементов.',
        'file' => 'Поле :attribute должно быть больше :value в килобайтах.',
        'numeric' => 'Поле :attribute должно быть больше, чем :value.',
        'string' => 'Поле :attribute должно содержать больше символов :value.',
    ],
    'gte' => [
        'array' => 'Поле :attribute должно содержать элементы :value или более.',
        'file' => 'Поле :attribute должно быть больше или равно :value в килобайтах.',
        'numeric' => 'Поле :attribute должно быть больше или равно :value.',
        'string' => 'Поле :attribute должно быть больше или равно количеству символов :value.',
    ],
    'image' => 'Поле :attribute должно быть изображением.',
    'in' => 'Выбранный :attribute недействителен.',
    'in_array' => 'Поле :attribute должно существовать в :other.',
    'integer' => 'Поле :attribute должно быть целым числом.',
    'ip' => 'Поле :attribute должно быть действительным IP-адресом.',
    'ipv4' => 'Поле :attribute должно быть действительным IPv4-адресом.',
    'ipv6' => 'Поле :attribute должно быть действительным IPv6-адресом.',
    'json' => 'Поле :attribute должно быть допустимой строкой JSON.',
    'lowercase' => 'Поле :attribute должно быть в нижнем регистре.',
    'lt' => [
        'array' => 'Поле :attribute должно содержать менее :value элементов.',
        'file' => 'Поле :attribute должно быть меньше :value килобайт.',
        'numeric' => 'Поле :attribute должно быть меньше :value.',
        'string' => 'Поле :attribute должно содержать меньше символов :value.',
    ],
    'lte' => [
        'array' => 'Поле :attribute не должно содержать более :value элементов.',
        'file' => 'Поле :attribute должно быть меньше или равно :value в килобайтах.',
        'numeric' => 'Поле :attribute должно быть меньше или равно :value.',
        'string' => 'Поле :attribute должно быть меньше или равно количеству символов :value.',
    ],
    'mac_address' => 'Поле :attribute должно быть действительным MAC-адресом.',
    'max' => [
        'array' => 'Поле :attribute не должно содержать более :max элементов.',
        'file' => 'Поле :attribute не должно превышать :max килобайт.',
        'numeric' => 'Поле :attribute не должно быть больше :max.',
        'string' => 'Поле :attribute не должно содержать более :max символов.',
    ],
    'max_digits' => 'Поле :attribute не должно содержать более :max цифр.',
    'mimes' => 'Поле :attribute должно быть файлом типа: :values.',
    'mimetypes' => 'Поле :attribute должно быть файлом типа: :values.',
    'min' => [
        'array' => 'Поле :attribute должно содержать не менее :min элементов.',
        'file' => 'Поле :attribute должно быть не менее :min килобайт.',
        'numeric' => 'Поле :attribute должно быть не меньше :min.',
        'string' => 'Поле :attribute должно содержать не менее :min символов.',
    ],
    'min_digits' => 'Поле :attribute должно содержать не менее :min цифр.',
    'missing' => 'Поле :attribute должно отсутствовать.',
    'missing_if' => 'Поле :attribute должно отсутствовать, если :other равно :value.',
    'missing_unless' => 'Поле :attribute должно отсутствовать, если только :other не равно :value.',
    'missing_with' => 'Поле :attribute должно отсутствовать, если присутствует :values.',
    'missing_with_all' => 'Поле :attribute должно отсутствовать, если присутствуют :values.',
    'multiple_of' => 'Поле :attribute должно быть кратно :value.',
    'not_in' => 'Выбранный :attribute недействителен.',
    'not_regex' => 'Формат поля :attribute недействителен.',
    'numeric' => 'Поле :attribute должно быть числом.',
    'password' => [
        'letters' => 'Поле :attribute должно содержать хотя бы одну букву.',
        'mixed' => 'Поле :attribute должно содержать хотя бы одну прописную и одну строчную букву.',
        'numbers' => 'Поле :attribute должно содержать хотя бы одно число.',
        'symbols' => 'Поле :attribute должно содержать хотя бы один символ.',
        'uncompromized' => 'Данный :attribute появился в результате утечки данных. Пожалуйста, выберите другой :attribute.',
    ],
    'present' => 'Поле :attribute должно присутствовать.',
    'prohibited' => 'Поле :attribute запрещено.',
    'prohibited_if' => 'Поле :attribute запрещено, если :other равно :value.',
    'prohibited_unless' => 'Поле :attribute запрещено, если только :other не находится в :values.',
    'prohibits' => 'Поле :attribute запрещает присутствие :other.',
    'regex' => 'Формат поля :attribute недействителен.',
    'required' => 'Поле :attribute является обязательным.',
    'required_array_keys' => 'Поле :attribute должно содержать записи для: :values.',
    'required_if' => 'Поле :attribute является обязательным, если :other равно :value.',
    'required_if_accepted' => 'Поле :attribute является обязательным, если принято :other.',
    'required_unless' => 'Поле :attribute является обязательным, если только :other не находится в :values.',
    'required_with' => 'Поле :attribute является обязательным, если присутствует :values.',
    'required_with_all' => 'Поле :attribute является обязательным, если присутствуют :values.',
    'required_without' => 'Поле :attribute является обязательным, если :values отсутствует.',
    'required_without_all' => 'Поле :attribute является обязательным, если ни одно из :values не присутствует.',
    'same' => 'Поле :attribute должно соответствовать :other.',
    'size' => [
        'array' => 'Поле :attribute должно содержать элементы :size.',
        'file' => 'Поле :attribute должно быть равно :size в килобайтах.',
        'numeric' => 'Поле :attribute должно быть :size.',
        'string' => 'Поле :attribute должно содержать символы :size.',
    ],
    'starts_with' => 'Поле :attribute должно начинаться с одного из следующих значений: :values.',
    'string' => 'Поле :attribute должно быть строкой.',
    'timezone' => 'Поле :attribute должно указывать действительный часовой пояс.',
    'unique' => 'Атрибут :attribute уже занят.',
    'uploaded' => 'Не удалось загрузить :attribute.',
    'uppercase' => 'Поле :attribute должно быть в верхнем регистре.',
    'url' => 'Поле :attribute должно быть действительным URL.',
    'ulid' => 'Поле :attribute должно быть действительным ULID.',
    'uuid' => 'Поле :attribute должно быть допустимым UUID.',

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
        'users' => 'пользователи',
        'roles' => 'роли',
        'inactive_users' => 'неактивные пользователи',
        'weekdays' => 'будние дни',
        'teachers' => 'учителя',
        'assistant_teachers' => 'помощники учителя',
        'courses' => 'курсы',
        'lessons' => 'уроки',
        'groups' => 'группы',
        'students' => 'студенты',
        'stparents' => 'родители',
        'sessions' => 'сессии',
        'branches' => 'ветви',
        'rooms' => 'комнаты',
        'schedules' => 'расписания',
        'certificates' => 'сертификаты',
        'failedsts' => 'неудавшиеся студенты',
        'failedgroups' => 'неудачные группы',
        'cashiers' => 'кассир',
        'access_for_courses' => 'доступ к курсам',
        'cards' => 'карты',
        'payments' => 'платежи',
        'changes' => 'изменения',
        'name' => 'имя',
        'location' => 'местоположение',
        'firstname' => 'имя',
        'lastname' => 'фамилия',
        'contact' => 'контакт',
        'email' => 'электронная почта',
        'password' => 'пароль',
        'status' => 'статус',
        'lang' => 'язык',
        'price' => 'цена',
        'sequence_number' => 'порядковый номер',
        'completed_lessons' => 'завершенные уроки',
        'start' => 'начало',
        'end' => 'конец',
        'reason' => 'причина',
        'cashier_key' => 'ключ кассира',
        'pay_time' => 'время оплаты',
        'expire_time' => 'время истечения срока действия',
        'card_number' => 'номер карты',
        'card_expiration' => 'срок действия карты',
        'card_token' => 'токен карты',
        'type' => 'тип',
        'card' => 'карта',
        'cash' => 'наличные',
        'amount' => 'сумма',
        'created_at' => 'создано',
        'role_id' => 'идентификатор роли',
        'teacher_id' => 'идентификатор учителя',
        'assistant_teacher_id' => 'идентификатор помощника учителя',
        'course_id' => 'идентификатор курса',
        'branch_id' => 'идентификатор ветки',
        'student_id' => 'идентификатор студента',
        'cashier_id' => 'идентификатор кассира',

        'value' => 'val/ru',
        'comment' => 'com/ru',
        'filter' => 'fil/ru',
    ],

];


