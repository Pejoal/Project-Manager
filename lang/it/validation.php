<?php

return [
  /*
    |--------------------------------------------------------------------------
    | Righe di Testo per la Validazione
    |--------------------------------------------------------------------------
    |
    | Le seguenti righe di testo contengono i messaggi di errore predefiniti utilizzati dalla
    | classe del validatore. Alcune di queste regole hanno più versioni, come
    | le regole per le dimensioni. Sentiti libero di modificare ciascuno di questi messaggi qui.
    |
    */

  'accepted' => 'Il campo :attribute deve essere accettato.',
  'accepted_if' => 'Il campo :attribute deve essere accettato quando :other è :value.',
  'active_url' => 'Il campo :attribute deve essere un URL valido.',
  'after' => 'Il campo :attribute deve essere una data successiva a :date.',
  'after_or_equal' => 'Il campo :attribute deve essere una data successiva o uguale a :date.',
  'alpha' => 'Il campo :attribute deve contenere solo lettere.',
  'alpha_dash' => 'Il campo :attribute deve contenere solo lettere, numeri, trattini e underscore.',
  'alpha_num' => 'Il campo :attribute deve contenere solo lettere e numeri.',
  'array' => 'Il campo :attribute deve essere un array.',
  'ascii' => 'Il campo :attribute deve contenere solo caratteri alfanumerici e simboli a byte singolo.',
  'before' => 'Il campo :attribute deve essere una data precedente a :date.',
  'before_or_equal' => 'Il campo :attribute deve essere una data precedente o uguale a :date.',
  'between' => [
    'array' => 'Il campo :attribute deve contenere tra :min e :max elementi.',
    'file' => 'Il campo :attribute deve essere compreso tra :min e :max kilobyte.',
    'numeric' => 'Il campo :attribute deve essere compreso tra :min e :max.',
    'string' => 'Il campo :attribute deve contenere tra :min e :max caratteri.',
  ],
  'boolean' => 'Il campo :attribute deve essere vero o falso.',
  'can' => 'Il campo :attribute contiene un valore non autorizzato.',
  'confirmed' => 'La conferma del campo :attribute non corrisponde.',
  'contains' => 'Il campo :attribute non contiene un valore richiesto.',
  'current_password' => 'La password non è corretta.',
  'date' => 'Il campo :attribute deve essere una data valida.',
  'date_equals' => 'Il campo :attribute deve essere una data uguale a :date.',
  'date_format' => 'Il campo :attribute deve corrispondere al formato :format.',
  'decimal' => 'Il campo :attribute deve avere :decimal cifre decimali.',
  'declined' => 'Il campo :attribute deve essere rifiutato.',
  'declined_if' => 'Il campo :attribute deve essere rifiutato quando :other è :value.',
  'different' => 'Il campo :attribute e :other devono essere diversi.',
  'digits' => 'Il campo :attribute deve essere di :digits cifre.',
  'digits_between' => 'Il campo :attribute deve essere compreso tra :min e :max cifre.',
  'dimensions' => 'Il campo :attribute ha dimensioni dell\'immagine non valide.',
  'distinct' => 'Il campo :attribute ha un valore duplicato.',
  'doesnt_end_with' => 'Il campo :attribute non deve terminare con uno dei seguenti: :values.',
  'doesnt_start_with' => 'Il campo :attribute non deve iniziare con uno dei seguenti: :values.',
  'email' => 'Il campo :attribute deve essere un indirizzo email valido.',
  'username' => 'L\'email o il nome utente forniti non sono corretti.',
  'ends_with' => 'Il campo :attribute deve terminare con uno dei seguenti: :values.',
  'enum' => 'Il valore selezionato per :attribute non è valido.',
  'exists' => 'Il valore selezionato per :attribute non è valido.',
  'extensions' => 'Il campo :attribute deve avere una delle seguenti estensioni: :values.',
  'file' => 'Il campo :attribute deve essere un file.',
  'filled' => 'Il campo :attribute deve avere un valore.',
  'gt' => [
    'array' => 'Il campo :attribute deve contenere più di :value elementi.',
    'file' => 'Il campo :attribute deve essere maggiore di :value kilobyte.',
    'numeric' => 'Il campo :attribute deve essere maggiore di :value.',
    'string' => 'Il campo :attribute deve contenere più di :value caratteri.',
  ],
  'gte' => [
    'array' => 'Il campo :attribute deve contenere :value elementi o più.',
    'file' => 'Il campo :attribute deve essere maggiore o uguale a :value kilobyte.',
    'numeric' => 'Il campo :attribute deve essere maggiore o uguale a :value.',
    'string' => 'Il campo :attribute deve contenere :value caratteri o più.',
  ],
  'hex_color' => 'Il campo :attribute deve essere un colore esadecimale valido.',
  'image' => 'Il campo :attribute deve essere un\'immagine.',
  'in' => 'Il valore selezionato per :attribute non è valido.',
  'in_array' => 'Il campo :attribute deve esistere in :other.',
  'integer' => 'Il campo :attribute deve essere un numero intero.',
  'ip' => 'Il campo :attribute deve essere un indirizzo IP valido.',
  'ipv4' => 'Il campo :attribute deve essere un indirizzo IPv4 valido.',
  'ipv6' => 'Il campo :attribute deve essere un indirizzo IPv6 valido.',
  'json' => 'Il campo :attribute deve essere una stringa JSON valida.',
  'list' => 'Il campo :attribute deve essere una lista.',
  'lowercase' => 'Il campo :attribute deve essere in minuscolo.',
  'lt' => [
    'array' => 'Il campo :attribute deve contenere meno di :value elementi.',
    'file' => 'Il campo :attribute deve essere inferiore a :value kilobyte.',
    'numeric' => 'Il campo :attribute deve essere inferiore a :value.',
    'string' => 'Il campo :attribute deve contenere meno di :value caratteri.',
  ],
  'lte' => [
    'array' => 'Il campo :attribute non deve contenere più di :value elementi.',
    'file' => 'Il campo :attribute deve essere inferiore o uguale a :value kilobyte.',
    'numeric' => 'Il campo :attribute deve essere inferiore o uguale a :value.',
    'string' => 'Il campo :attribute deve contenere :value caratteri o meno.',
  ],
  'mac_address' => 'Il campo :attribute deve essere un indirizzo MAC valido.',
  'max' => [
    'array' => 'Il campo :attribute non deve contenere più di :max elementi.',
    'file' => 'Il campo :attribute non deve superare :max kilobyte.',
    'numeric' => 'Il campo :attribute non deve essere maggiore di :max.',
    'string' => 'Il campo :attribute non deve contenere più di :max caratteri.',
  ],
  'max_digits' => 'Il campo :attribute non deve contenere più di :max cifre.',
  'mimes' => 'Il campo :attribute deve essere un file di tipo: :values.',
  'mimetypes' => 'Il campo :attribute deve essere un file di tipo: :values.',
  'min' => [
    'array' => 'Il campo :attribute deve contenere almeno :min elementi.',
    'file' => 'Il campo :attribute deve essere almeno :min kilobyte.',
    'numeric' => 'Il campo :attribute deve essere almeno :min.',
    'string' => 'Il campo :attribute deve contenere almeno :min caratteri.',
  ],
  'min_digits' => 'Il campo :attribute deve contenere almeno :min cifre.',
  'missing' => 'Il campo :attribute deve essere mancante.',
  'missing_if' => 'Il campo :attribute deve essere mancante quando :other è :value.',
  'missing_unless' => 'Il campo :attribute deve essere mancante a meno che :other non sia :value.',
  'missing_with' => 'Il campo :attribute deve essere mancante quando :values è presente.',
  'missing_with_all' => 'Il campo :attribute deve essere mancante quando :values sono presenti.',
  'multiple_of' => 'Il campo :attribute deve essere un multiplo di :value.',
  'not_in' => 'Il valore selezionato per :attribute non è valido.',
  'not_regex' => 'Il formato del campo :attribute non è valido.',
  'numeric' => 'Il campo :attribute deve essere un numero.',
  'password' => [
    'letters' => 'Il campo :attribute deve contenere almeno una lettera.',
    'mixed' => 'Il campo :attribute deve contenere almeno una lettera maiuscola e una minuscola.',
    'numbers' => 'Il campo :attribute deve contenere almeno un numero.',
    'symbols' => 'Il campo :attribute deve contenere almeno un simbolo.',
    'uncompromised' => 'Il valore di :attribute è apparso in una violazione di dati. Scegli un altro :attribute.',
  ],
  'present' => 'Il campo :attribute deve essere presente.',
  'present_if' => 'Il campo :attribute deve essere presente quando :other è :value.',
  'present_unless' => 'Il campo :attribute deve essere presente a meno che :other non sia :value.',
  'present_with' => 'Il campo :attribute deve essere presente quando :values è presente.',
  'present_with_all' => 'Il campo :attribute deve essere presente quando :values sono presenti.',
  'prohibited' => 'Il campo :attribute è proibito.',
  'prohibited_if' => 'Il campo :attribute è proibito quando :other è :value.',
  'prohibited_unless' => 'Il campo :attribute è proibito a meno che :other non sia in :values.',
  'prohibits' => 'Il campo :attribute proibisce la presenza di :other.',
  'regex' => 'Il formato del campo :attribute non è valido.',
  'required' => 'Il campo :attribute è obbligatorio.',
  'required_array_keys' => 'Il campo :attribute deve contenere voci per: :values.',
  'required_if' => 'Il campo :attribute è obbligatorio quando :other è :value.',
  'required_if_accepted' => 'Il campo :attribute è obbligatorio quando :other è accettato.',
  'required_if_declined' => 'Il campo :attribute è obbligatorio quando :other è rifiutato.',
  'required_unless' => 'Il campo :attribute è obbligatorio a meno che :other non sia in :values.',
  'required_with' => 'Il campo :attribute è obbligatorio quando :values è presente.',
  'required_with_all' => 'Il campo :attribute è obbligatorio quando :values sono presenti.',
  'required_without' => 'Il campo :attribute è obbligatorio quando :values non è presente.',
  'required_without_all' => 'Il campo :attribute è obbligatorio quando nessuno di :values è presente.',
  'same' => 'Il campo :attribute deve corrispondere a :other.',
  'size' => [
    'array' => 'Il campo :attribute deve contenere :size elementi.',
    'file' => 'Il campo :attribute deve essere :size kilobyte.',
    'numeric' => 'Il campo :attribute deve essere :size.',
    'string' => 'Il campo :attribute deve contenere :size caratteri.',
  ],
  'starts_with' => 'Il campo :attribute deve iniziare con uno dei seguenti: :values.',
  'string' => 'Il campo :attribute deve essere una stringa.',
  'timezone' => 'Il campo :attribute deve essere un fuso orario valido.',
  'unique' => 'Il campo :attribute è già stato utilizzato.',
  'uploaded' => 'Il caricamento del campo :attribute non è riuscito.',
  'uppercase' => 'Il campo :attribute deve essere in maiuscolo.',
  'url' => 'Il campo :attribute deve essere un URL valido.',
  'ulid' => 'Il campo :attribute deve essere un ULID valido.',
  'uuid' => 'Il campo :attribute deve essere un UUID valido.',

  /*
    |--------------------------------------------------------------------------
    | Righe di Testo per la Validazione Personalizzata
    |--------------------------------------------------------------------------
    |
    | Qui puoi specificare messaggi di validazione personalizzati per gli attributi utilizzando
    | la convenzione "attribute.rule" per nominare le righe. Questo rende veloce
    | specificare un messaggio personalizzato per una determinata regola di attributo.
    |
    */

  'custom' => [
    'attribute-name' => [
      'rule-name' => 'messaggio-personalizzato',
    ],
  ],

  /*
    |--------------------------------------------------------------------------
    | Attributi di Validazione Personalizzati
    |--------------------------------------------------------------------------
    |
    | Le seguenti righe di testo sono utilizzate per sostituire il segnaposto dell'attributo
    | con qualcosa di più leggibile, come "Indirizzo Email" invece di "email".
    | Questo ci aiuta semplicemente a rendere i nostri messaggi più espressivi.
    |
    */

  'attributes' => [],
];
