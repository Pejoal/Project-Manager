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

  'accepted' => 'Das :attribute muss akzeptiert werden.',
  'accepted_if' =>
    'Das :attribute muss akzeptiert werden, wenn :other :value ist.',
  'active_url' => 'Das :attribute ist keine gültige URL.',
  'after' => 'Das :attribute muss ein Datum nach :date sein.',
  'after_or_equal' =>
    'Das :attribute muss ein Datum nach oder gleich :date sein.',
  'alpha' => 'Das :attribute darf nur Buchstaben enthalten.',
  'alpha_dash' =>
    'Das :attribute darf nur Buchstaben, Zahlen, Bindestriche und Unterstriche enthalten.',
  'alpha_num' => 'Das :attribute darf nur Buchstaben und Zahlen enthalten.',
  'array' => 'Das :attribute muss ein Array sein.',
  'ascii' =>
    'Das :attribute darf nur einbyte alphanumerische Zeichen und Symbole enthalten.',
  'before' => 'Das :attribute muss ein Datum vor :date sein.',
  'before_or_equal' =>
    'Das :attribute muss ein Datum vor oder gleich :date sein.',
  'between' => [
    'array' => 'Das :attribute muss zwischen :min und :max Elemente haben.',
    'file' => 'Das :attribute muss zwischen :min und :max Kilobytes groß sein.',
    'numeric' => 'Das :attribute muss zwischen :min und :max liegen.',
    'string' => 'Das :attribute muss zwischen :min und :max Zeichen haben.',
  ],
  'boolean' => 'Das :attribute Feld muss wahr oder falsch sein.',
  'can' => 'Das :attribute enthält einen unzulässigen Wert.',
  'confirmed' => 'Die :attribute Bestätigung stimmt nicht überein.',
  'contains' => 'Das :attribute fehlt ein erforderlicher Wert.',
  'current_password' => 'Das Passwort ist falsch.',
  'date' => 'Das :attribute ist kein gültiges Datum.',
  'date_equals' => 'Das :attribute muss ein Datum gleich :date sein.',
  'date_format' => 'Das :attribute entspricht nicht dem Format :format.',
  'decimal' => 'Das :attribute muss :decimal Dezimalstellen haben.',
  'declined' => 'Das :attribute muss abgelehnt werden.',
  'declined_if' =>
    'Das :attribute muss abgelehnt werden, wenn :other :value ist.',
  'different' => 'Das :attribute und :other müssen unterschiedlich sein.',
  'digits' => 'Das :attribute muss :digits Ziffern haben.',
  'digits_between' =>
    'Das :attribute muss zwischen :min und :max Ziffern haben.',
  'dimensions' => 'Das :attribute hat ungültige Bildabmessungen.',
  'distinct' => 'Das :attribute Feld hat einen doppelten Wert.',
  'doesnt_end_with' =>
    'Das :attribute darf nicht mit einem der folgenden enden: :values.',
  'doesnt_start_with' =>
    'Das :attribute darf nicht mit einem der folgenden beginnen: :values.',
  'email' => 'Das :attribute muss eine gültige E-Mail-Adresse sein.',
  'username' => 'Die angegebene E-Mail oder der Benutzername ist falsch.',
  'ends_with' => 'Das :attribute muss mit einem der folgenden enden: :values.',
  'enum' => 'Das ausgewählte :attribute ist ungültig.',
  'exists' => 'Das ausgewählte :attribute ist ungültig.',
  'extensions' =>
    'Das :attribute muss eine der folgenden Erweiterungen haben: :values.',
  'file' => 'Das :attribute muss eine Datei sein.',
  'filled' => 'Das :attribute Feld muss einen Wert haben.',
  'gt' => [
    'array' => 'Das :attribute muss mehr als :value Elemente haben.',
    'file' => 'Das :attribute muss größer als :value Kilobytes sein.',
    'numeric' => 'Das :attribute muss größer als :value sein.',
    'string' => 'Das :attribute muss größer als :value Zeichen sein.',
  ],
  'gte' => [
    'array' => 'Das :attribute muss :value Elemente oder mehr haben.',
    'file' => 'Das :attribute muss größer oder gleich :value Kilobytes sein.',
    'numeric' => 'Das :attribute muss größer oder gleich :value sein.',
    'string' => 'Das :attribute muss größer oder gleich :value Zeichen sein.',
  ],
  'hex_color' => 'Das :attribute muss eine gültige hexadezimale Farbe sein.',
  'image' => 'Das :attribute muss ein Bild sein.',
  'in' => 'Das ausgewählte :attribute ist ungültig.',
  'in_array' => 'Das :attribute Feld existiert nicht in :other.',
  'integer' => 'Das :attribute muss eine Ganzzahl sein.',
  'ip' => 'Das :attribute muss eine gültige IP-Adresse sein.',
  'ipv4' => 'Das :attribute muss eine gültige IPv4-Adresse sein.',
  'ipv6' => 'Das :attribute muss eine gültige IPv6-Adresse sein.',
  'json' => 'Das :attribute muss ein gültiger JSON-String sein.',
  'list' => 'Das :attribute muss eine Liste sein.',
  'lowercase' => 'Das :attribute muss klein geschrieben sein.',
  'lt' => [
    'array' => 'Das :attribute muss weniger als :value Elemente haben.',
    'file' => 'Das :attribute muss kleiner als :value Kilobytes sein.',
    'numeric' => 'Das :attribute muss kleiner als :value sein.',
    'string' => 'Das :attribute muss kleiner als :value Zeichen sein.',
  ],
  'lte' => [
    'array' => 'Das :attribute darf nicht mehr als :value Elemente haben.',
    'file' => 'Das :attribute muss kleiner oder gleich :value Kilobytes sein.',
    'numeric' => 'Das :attribute muss kleiner oder gleich :value sein.',
    'string' => 'Das :attribute muss kleiner oder gleich :value Zeichen sein.',
  ],
  'mac_address' => 'Das :attribute muss eine gültige MAC-Adresse sein.',
  'max' => [
    'array' => 'Das :attribute darf nicht mehr als :max Elemente haben.',
    'file' => 'Das :attribute darf nicht größer als :max Kilobytes sein.',
    'numeric' => 'Das :attribute darf nicht größer als :max sein.',
    'string' => 'Das :attribute darf nicht größer als :max Zeichen sein.',
  ],
  'max_digits' => 'Das :attribute darf nicht mehr als :max Ziffern haben.',
  'mimes' => 'Das :attribute muss eine Datei des Typs: :values sein.',
  'mimetypes' => 'Das :attribute muss eine Datei des Typs: :values sein.',
  'min' => [
    'array' => 'Das :attribute muss mindestens :min Elemente haben.',
    'file' => 'Das :attribute muss mindestens :min Kilobytes groß sein.',
    'numeric' => 'Das :attribute muss mindestens :min sein.',
    'string' => 'Das :attribute muss mindestens :min Zeichen haben.',
  ],
  'min_digits' => 'Das :attribute muss mindestens :min Ziffern haben.',
  'missing' => 'Das :attribute muss fehlen.',
  'missing_if' => 'Das :attribute muss fehlen, wenn :other :value ist.',
  'missing_unless' =>
    'Das :attribute muss fehlen, es sei denn, :other ist :value.',
  'missing_with' => 'Das :attribute muss fehlen, wenn :values vorhanden ist.',
  'missing_with_all' =>
    'Das :attribute muss fehlen, wenn :values vorhanden sind.',
  'multiple_of' => 'Das :attribute muss ein Vielfaches von :value sein.',
  'not_in' => 'Das ausgewählte :attribute ist ungültig.',
  'not_regex' => 'Das :attribute Format ist ungültig.',
  'numeric' => 'Das :attribute muss eine Zahl sein.',
  'password' => [
    'letters' => 'Das :attribute muss mindestens einen Buchstaben enthalten.',
    'mixed' =>
      'Das :attribute muss mindestens einen Großbuchstaben und einen Kleinbuchstaben enthalten.',
    'numbers' => 'Das :attribute muss mindestens eine Zahl enthalten.',
    'symbols' => 'Das :attribute muss mindestens ein Symbol enthalten.',
    'uncompromised' =>
      'Das :attribute wurde in einem Datenleck gefunden. Bitte wählen Sie ein anderes :attribute.',
  ],
  'present' => 'Das :attribute Feld muss vorhanden sein.',
  'present_if' =>
    'Das :attribute Feld muss vorhanden sein, wenn :other :value ist.',
  'present_unless' =>
    'Das :attribute Feld muss vorhanden sein, es sei denn, :other ist :value.',
  'present_with' =>
    'Das :attribute Feld muss vorhanden sein, wenn :values vorhanden ist.',
  'present_with_all' =>
    'Das :attribute Feld muss vorhanden sein, wenn :values vorhanden sind.',
  'prohibited' => 'Das :attribute Feld ist verboten.',
  'prohibited_if' =>
    'Das :attribute Feld ist verboten, wenn :other :value ist.',
  'prohibited_unless' =>
    'Das :attribute Feld ist verboten, es sei denn, :other ist in :values.',
  'prohibits' => 'Das :attribute Feld verbietet :other vorhanden zu sein.',
  'regex' => 'Das :attribute Format ist ungültig.',
  'required' => 'Das :attribute Feld ist erforderlich.',
  'required_array_keys' =>
    'Das :attribute Feld muss Einträge für: :values enthalten.',
  'required_if' =>
    'Das :attribute Feld ist erforderlich, wenn :other :value ist.',
  'required_if_accepted' =>
    'Das :attribute Feld ist erforderlich, wenn :other akzeptiert wird.',
  'required_if_declined' =>
    'Das :attribute Feld ist erforderlich, wenn :other abgelehnt wird.',
  'required_unless' =>
    'Das :attribute Feld ist erforderlich, es sei denn, :other ist in :values.',
  'required_with' =>
    'Das :attribute Feld ist erforderlich, wenn :values vorhanden ist.',
  'required_with_all' =>
    'Das :attribute Feld ist erforderlich, wenn :values vorhanden sind.',
  'required_without' =>
    'Das :attribute Feld ist erforderlich, wenn :values nicht vorhanden ist.',
  'required_without_all' =>
    'Das :attribute Feld ist erforderlich, wenn keiner der :values vorhanden ist.',
  'same' => 'Das :attribute und :other müssen übereinstimmen.',
  'size' => [
    'array' => 'Das :attribute muss :size Elemente enthalten.',
    'file' => 'Das :attribute muss :size Kilobytes groß sein.',
    'numeric' => 'Das :attribute muss :size sein.',
    'string' => 'Das :attribute muss :size Zeichen haben.',
  ],
  'starts_with' =>
    'Das :attribute muss mit einem der folgenden beginnen: :values.',
  'string' => 'Das :attribute muss eine Zeichenkette sein.',
  'timezone' => 'Das :attribute muss eine gültige Zeitzone sein.',
  'unique' => 'Das :attribute ist bereits vergeben.',
  'uploaded' => 'Das :attribute konnte nicht hochgeladen werden.',
  'uppercase' => 'Das :attribute muss groß geschrieben sein.',
  'url' => 'Das :attribute muss eine gültige URL sein.',
  'ulid' => 'Das :attribute muss eine gültige ULID sein.',
  'uuid' => 'Das :attribute muss eine gültige UUID sein.',

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
