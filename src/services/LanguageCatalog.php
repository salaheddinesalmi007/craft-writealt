<?php

namespace writealt\services;

final class LanguageCatalog
{
    public static function languages(): array
    {
        return [
            'af' => 'Afrikaans', 'sq' => 'Albanian', 'am' => 'Amharic', 'ar' => 'Arabic',
            'hy' => 'Armenian', 'as' => 'Assamese', 'ay' => 'Aymara', 'az' => 'Azerbaijani',
            'bm' => 'Bambara', 'eu' => 'Basque', 'be' => 'Belarusian', 'bn' => 'Bengali',
            'bho' => 'Bhojpuri', 'bs' => 'Bosnian', 'br' => 'Breton', 'bg' => 'Bulgarian',
            'my' => 'Burmese', 'ca' => 'Catalan', 'ceb' => 'Cebuano', 'ny' => 'Chichewa',
            'zh' => 'Chinese', 'co' => 'Corsican', 'hr' => 'Croatian', 'cs' => 'Czech',
            'da' => 'Danish', 'dv' => 'Dhivehi', 'doi' => 'Dogri', 'nl' => 'Dutch',
            'en' => 'English', 'eo' => 'Esperanto', 'et' => 'Estonian', 'ee' => 'Ewe',
            'fo' => 'Faroese', 'tl' => 'Filipino', 'fi' => 'Finnish', 'fr' => 'French',
            'fy' => 'Frisian', 'gl' => 'Galician', 'ka' => 'Georgian', 'de' => 'German',
            'el' => 'Greek', 'gn' => 'Guarani', 'gu' => 'Gujarati', 'ht' => 'Haitian Creole',
            'ha' => 'Hausa', 'haw' => 'Hawaiian', 'he' => 'Hebrew', 'hi' => 'Hindi',
            'hmn' => 'Hmong', 'hu' => 'Hungarian', 'is' => 'Icelandic', 'ig' => 'Igbo',
            'ilo' => 'Ilocano', 'id' => 'Indonesian', 'ga' => 'Irish', 'it' => 'Italian',
            'ja' => 'Japanese', 'jv' => 'Javanese', 'kl' => 'Kalaallisut', 'kn' => 'Kannada',
            'kk' => 'Kazakh', 'km' => 'Khmer', 'rw' => 'Kinyarwanda', 'ko' => 'Korean',
            'ku' => 'Kurdish', 'ky' => 'Kyrgyz', 'lo' => 'Lao', 'la' => 'Latin',
            'lv' => 'Latvian', 'ln' => 'Lingala', 'lt' => 'Lithuanian', 'lg' => 'Luganda',
            'lb' => 'Luxembourgish', 'mk' => 'Macedonian', 'mai' => 'Maithili', 'mg' => 'Malagasy',
            'ms' => 'Malay', 'ml' => 'Malayalam', 'mt' => 'Maltese', 'mni' => 'Manipuri',
            'mi' => 'Maori', 'mr' => 'Marathi', 'lus' => 'Mizo', 'mn' => 'Mongolian',
            'ne' => 'Nepali', 'no' => 'Norwegian', 'or' => 'Odia', 'om' => 'Oromo',
            'ps' => 'Pashto', 'fa' => 'Persian', 'pl' => 'Polish', 'pt' => 'Portuguese',
            'pa' => 'Punjabi', 'qu' => 'Quechua', 'ro' => 'Romanian', 'ru' => 'Russian',
            'sm' => 'Samoan', 'sa' => 'Sanskrit', 'gd' => 'Scottish Gaelic', 'nso' => 'Sepedi',
            'sr' => 'Serbian', 'st' => 'Sesotho', 'sn' => 'Shona', 'sd' => 'Sindhi',
            'si' => 'Sinhala', 'sk' => 'Slovak', 'sl' => 'Slovenian', 'so' => 'Somali',
            'es' => 'Spanish', 'su' => 'Sundanese', 'sw' => 'Swahili', 'sv' => 'Swedish',
            'tg' => 'Tajik', 'ta' => 'Tamil', 'tt' => 'Tatar', 'te' => 'Telugu',
            'th' => 'Thai', 'bo' => 'Tibetan', 'ti' => 'Tigrinya', 'to' => 'Tongan',
            'ts' => 'Tsonga', 'tn' => 'Tswana', 'tr' => 'Turkish', 'tk' => 'Turkmen',
            'uk' => 'Ukrainian', 'ur' => 'Urdu', 'ug' => 'Uyghur', 'uz' => 'Uzbek',
            'vi' => 'Vietnamese', 'wa' => 'Walloon', 'cy' => 'Welsh', 'wo' => 'Wolof',
            'xh' => 'Xhosa', 'yi' => 'Yiddish', 'yo' => 'Yoruba', 'zza' => 'Zaza', 'zu' => 'Zulu',
        ];
    }

    public static function styles(): array
    {
        return [
            2 => 'Recommended - Balanced and descriptive',
            1 => 'Professional - Factual business tone',
            3 => 'Shorter - Concise essential details',
            4 => 'Comprehensive - Detailed description',
            5 => 'Briefest - One short sentence',
        ];
    }
}
