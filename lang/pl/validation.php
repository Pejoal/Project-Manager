<?php

return [
  /*
    |--------------------------------------------------------------------------
    | Linie Językowe Walidacji
    |--------------------------------------------------------------------------
    |
    | Poniższe linie językowe zawierają domyślne komunikaty błędów używane przez
    | klasę walidatora. Niektóre z tych reguł mają wiele wersji, np.
    | reguły dotyczące rozmiaru. Możesz dowolnie modyfikować te komunikaty.
    |
    */

  'accepted' => 'Pole :attribute musi zostać zaakceptowane.',
  'accepted_if' => 'Pole :attribute musi zostać zaakceptowane, gdy :other jest :value.',
  'active_url' => 'Pole :attribute musi być prawidłowym adresem URL.',
  'after' => 'Pole :attribute musi być datą późniejszą niż :date.',
  'after_or_equal' => 'Pole :attribute musi być datą późniejszą lub równą :date.',
  'alpha' => 'Pole :attribute może zawierać tylko litery.',
  'alpha_dash' => 'Pole :attribute może zawierać tylko litery, cyfry, myślniki i podkreślenia.',
  'alpha_num' => 'Pole :attribute może zawierać tylko litery i cyfry.',
  'array' => 'Pole :attribute musi być tablicą.',
  'ascii' => 'Pole :attribute może zawierać tylko jednobajtowe znaki alfanumeryczne i symbole.',
  'before' => 'Pole :attribute musi być datą wcześniejszą niż :date.',
  'before_or_equal' => 'Pole :attribute musi być datą wcześniejszą lub równą :date.',
  'between' => [
    'array' => 'Pole :attribute musi zawierać od :min do :max elementów.',
    'file' => 'Pole :attribute musi mieć od :min do :max kilobajtów.',
    'numeric' => 'Pole :attribute musi być między :min a :max.',
    'string' => 'Pole :attribute musi zawierać od :min do :max znaków.',
  ],
  'boolean' => 'Pole :attribute musi być prawdą lub fałszem.',
  'can' => 'Pole :attribute zawiera niedozwoloną wartość.',
  'confirmed' => 'Potwierdzenie pola :attribute nie pasuje.',
  'contains' => 'Pole :attribute nie zawiera wymaganej wartości.',
  'current_password' => 'Hasło jest nieprawidłowe.',
  'date' => 'Pole :attribute musi być prawidłową datą.',
  'date_equals' => 'Pole :attribute musi być datą równą :date.',
  'date_format' => 'Pole :attribute musi pasować do formatu :format.',
  'decimal' => 'Pole :attribute musi mieć :decimal miejsc po przecinku.',
  'declined' => 'Pole :attribute musi zostać odrzucone.',
  'declined_if' => 'Pole :attribute musi zostać odrzucone, gdy :other jest :value.',
  'different' => 'Pole :attribute i :other muszą się różnić.',
  'digits' => 'Pole :attribute musi składać się z :digits cyfr.',
  'digits_between' => 'Pole :attribute musi zawierać od :min do :max cyfr.',
  'dimensions' => 'Pole :attribute ma nieprawidłowe wymiary obrazu.',
  'distinct' => 'Pole :attribute ma zduplikowaną wartość.',
  'doesnt_end_with' => 'Pole :attribute nie może kończyć się jednym z: :values.',
  'doesnt_start_with' => 'Pole :attribute nie może zaczynać się jednym z: :values.',
  'email' => 'Pole :attribute musi być prawidłowym adresem e-mail.',
  'username' => 'Podany adres e-mail lub nazwa użytkownika jest nieprawidłowy.',
  'ends_with' => 'Pole :attribute musi kończyć się jednym z: :values.',
  'enum' => 'Wybrana wartość dla :attribute jest nieprawidłowa.',
  'exists' => 'Wybrana wartość dla :attribute jest nieprawidłowa.',
  'extensions' => 'Pole :attribute musi mieć jedno z następujących rozszerzeń: :values.',
  'file' => 'Pole :attribute musi być plikiem.',
  'filled' => 'Pole :attribute musi mieć wartość.',
  'gt' => [
    'array' => 'Pole :attribute musi zawierać więcej niż :value elementów.',
    'file' => 'Pole :attribute musi być większe niż :value kilobajtów.',
    'numeric' => 'Pole :attribute musi być większe niż :value.',
    'string' => 'Pole :attribute musi zawierać więcej niż :value znaków.',
  ],
  'gte' => [
    'array' => 'Pole :attribute musi zawierać :value lub więcej elementów.',
    'file' => 'Pole :attribute musi być większe lub równe :value kilobajtów.',
    'numeric' => 'Pole :attribute musi być większe lub równe :value.',
    'string' => 'Pole :attribute musi zawierać :value lub więcej znaków.',
  ],
  'hex_color' => 'Pole :attribute musi być prawidłowym kolorem heksadecymalnym.',
  'image' => 'Pole :attribute musi być obrazem.',
  'in' => 'Wybrana wartość dla :attribute jest nieprawidłowa.',
  'in_array' => 'Pole :attribute musi istnieć w :other.',
  'integer' => 'Pole :attribute musi być liczbą całkowitą.',
  'ip' => 'Pole :attribute musi być prawidłowym adresem IP.',
  'ipv4' => 'Pole :attribute musi być prawidłowym adresem IPv4.',
  'ipv6' => 'Pole :attribute musi być prawidłowym adresem IPv6.',
  'json' => 'Pole :attribute musi być prawidłowym ciągiem JSON.',
  'list' => 'Pole :attribute musi być listą.',
  'lowercase' => 'Pole :attribute musi być zapisane małymi literami.',
  'lt' => [
    'array' => 'Pole :attribute musi zawierać mniej niż :value elementów.',
    'file' => 'Pole :attribute musi być mniejsze niż :value kilobajtów.',
    'numeric' => 'Pole :attribute musi być mniejsze niż :value.',
    'string' => 'Pole :attribute musi zawierać mniej niż :value znaków.',
  ],
  'lte' => [
    'array' => 'Pole :attribute nie może zawierać więcej niż :value elementów.',
    'file' => 'Pole :attribute musi być mniejsze lub równe :value kilobajtów.',
    'numeric' => 'Pole :attribute musi być mniejsze lub równe :value.',
    'string' => 'Pole :attribute musi zawierać :value lub mniej znaków.',
  ],
  'mac_address' => 'Pole :attribute musi być prawidłowym adresem MAC.',
  'max' => [
    'array' => 'Pole :attribute nie może zawierać więcej niż :max elementów.',
    'file' => 'Pole :attribute nie może być większe niż :max kilobajtów.',
    'numeric' => 'Pole :attribute nie może być większe niż :max.',
    'string' => 'Pole :attribute nie może zawierać więcej niż :max znaków.',
  ],
  'max_digits' => 'Pole :attribute nie może zawierać więcej niż :max cyfr.',
  'mimes' => 'Pole :attribute musi być plikiem typu: :values.',
  'mimetypes' => 'Pole :attribute musi być plikiem typu: :values.',
  'min' => [
    'array' => 'Pole :attribute musi zawierać co najmniej :min elementów.',
    'file' => 'Pole :attribute musi mieć co najmniej :min kilobajtów.',
    'numeric' => 'Pole :attribute musi być co najmniej :min.',
    'string' => 'Pole :attribute musi zawierać co najmniej :min znaków.',
  ],
  'min_digits' => 'Pole :attribute musi zawierać co najmniej :min cyfr.',
  'missing' => 'Pole :attribute musi być brakujące.',
  'missing_if' => 'Pole :attribute musi być brakujące, gdy :other jest :value.',
  'missing_unless' => 'Pole :attribute musi być brakujące, chyba że :other jest :value.',
  'missing_with' => 'Pole :attribute musi być brakujące, gdy :values jest obecne.',
  'missing_with_all' => 'Pole :attribute musi być brakujące, gdy :values są obecne.',
  'multiple_of' => 'Pole :attribute musi być wielokrotnością :value.',
  'not_in' => 'Wybrana wartość dla :attribute jest nieprawidłowa.',
  'not_regex' => 'Format pola :attribute jest nieprawidłowy.',
  'numeric' => 'Pole :attribute musi być liczbą.',
  'password' => [
    'letters' => 'Pole :attribute musi zawierać co najmniej jedną literę.',
    'mixed' => 'Pole :attribute musi zawierać co najmniej jedną wielką i jedną małą literę.',
    'numbers' => 'Pole :attribute musi zawierać co najmniej jedną cyfrę.',
    'symbols' => 'Pole :attribute musi zawierać co najmniej jeden symbol.',
    'uncompromised' => 'Podana wartość :attribute pojawiła się w wycieku danych. Wybierz inną wartość :attribute.',
  ],
  'present' => 'Pole :attribute musi być obecne.',
  'present_if' => 'Pole :attribute musi być obecne, gdy :other jest :value.',
  'present_unless' => 'Pole :attribute musi być obecne, chyba że :other jest :value.',
  'present_with' => 'Pole :attribute musi być obecne, gdy :values jest obecne.',
  'present_with_all' => 'Pole :attribute musi być obecne, gdy :values są obecne.',
  'prohibited' => 'Pole :attribute jest zabronione.',
  'prohibited_if' => 'Pole :attribute jest zabronione, gdy :other jest :value.',
  'prohibited_unless' => 'Pole :attribute jest zabronione, chyba że :other jest w :values.',
  'prohibits' => 'Pole :attribute zabrania obecności :other.',
  'regex' => 'Format pola :attribute jest nieprawidłowy.',
  'required' => 'Pole :attribute jest wymagane.',
  'required_array_keys' => 'Pole :attribute musi zawierać wpisy dla: :values.',
  'required_if' => 'Pole :attribute jest wymagane, gdy :other jest :value.',
  'required_if_accepted' => 'Pole :attribute jest wymagane, gdy :other jest zaakceptowane.',
  'required_if_declined' => 'Pole :attribute jest wymagane, gdy :other jest odrzucone.',
  'required_unless' => 'Pole :attribute jest wymagane, chyba że :other jest w :values.',
  'required_with' => 'Pole :attribute jest wymagane, gdy :values jest obecne.',
  'required_with_all' => 'Pole :attribute jest wymagane, gdy :values są obecne.',
  'required_without' => 'Pole :attribute jest wymagane, gdy :values nie jest obecne.',
  'required_without_all' => 'Pole :attribute jest wymagane, gdy żadne z :values nie jest obecne.',
  'same' => 'Pole :attribute musi pasować do :other.',
  'size' => [
    'array' => 'Pole :attribute musi zawierać :size elementów.',
    'file' => 'Pole :attribute musi mieć :size kilobajtów.',
    'numeric' => 'Pole :attribute musi być :size.',
    'string' => 'Pole :attribute musi zawierać :size znaków.',
  ],
  'starts_with' => 'Pole :attribute musi zaczynać się jednym z: :values.',
  'string' => 'Pole :attribute musi być ciągiem znaków.',
  'timezone' => 'Pole :attribute musi być prawidłową strefą czasową.',
  'unique' => 'Pole :attribute jest już zajęte.',
  'uploaded' => 'Przesyłanie pola :attribute nie powiodło się.',
  'uppercase' => 'Pole :attribute musi być zapisane wielkimi literami.',
  'url' => 'Pole :attribute musi być prawidłowym adresem URL.',
  'ulid' => 'Pole :attribute musi być prawidłowym ULID.',
  'uuid' => 'Pole :attribute musi być prawidłowym UUID.',

  /*
    |--------------------------------------------------------------------------
    | Niestandardowe Linie Językowe Walidacji
    |--------------------------------------------------------------------------
    |
    | Tutaj możesz określić niestandardowe komunikaty walidacji dla atrybutów, używając
    | konwencji "attribute.rule" do nazwania linii. To pozwala szybko
    | określić konkretny niestandardowy komunikat dla danej reguły atrybutu.
    |
    */

  'custom' => [
    'attribute-name' => [
      'rule-name' => 'niestandardowy-komunikat',
    ],
  ],

  /*
    |--------------------------------------------------------------------------
    | Niestandardowe Atrybuty Walidacji
    |--------------------------------------------------------------------------
    |
    | Poniższe linie językowe są używane do zamiany symbolu zastępczego atrybutu
    | na coś bardziej przyjaznego dla czytelnika, np. "Adres E-mail" zamiast "email".
    | To po prostu pomaga nam uczynić nasze komunikaty bardziej ekspresyjnymi.
    |
    */

  'attributes' => [],
];
