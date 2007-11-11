<?php  // $Id$
    
    // vim: expandtab sw=4 ts=4 sts=4:
    
    /**
     * CLAROLINE
     *
     * @version 1.9 $Revision: 26 $
     *
     * @copyright 2001-2006 Universite catholique de Louvain (UCL)
     *
     * @license http://www.gnu.org/copyleft/gpl.html (GPL) GENERAL PUBLIC LICENSE
     *
     * @author Frederic Minne <zefredz@gmail.com>
     * @version 1.0
     * @package PlugIt
     */
    
    if ( count( get_included_files() ) == 1 ) die( '---' );
    
    /**
     * Module language library
     */
	
	function claro_module_load_language( $lang = null )
    {
        $lang = is_null( $lang ) ? $GLOBALS['language'] : $lang;
        $code = ISOCode::getCode( $lang );
        $moduleLangDir = get_module_path($GLOBALS['moduleLabel']);
        
        if ( $code )
        {
            if ( file_exists( $moduleLangDir . '/' . $code ) )
            {
                $moduleLangPath = $moduleLangDir . '/' . $code;
            }
            elseif ( file_exists( $moduleLangDir . '/en' ) )
            {
                $moduleLangPath = $moduleLangDir . '/en';
            }
            else
            {
                return false;
            }
            
            // load kernel file
            if ( file_exists ( $moduleLangPath . '/claroline.lang.php' ) )
            {
                include $moduleLangPath . '/claroline.lang.php';
            }
            // load module file
            if ( file_exists ( $moduleLangPath . '/module.lang.php' ) )
            {
                include $moduleLangPath . '/module.lang.php';
            }
            
            return true;
        }
        else
        {
            return false;
        }
    }
    
    class ISOCode
    {
        /**
         * Get an associative array of language names and their ISO codes
         *
         * @access  public
         * @static
         * @param   boolean flip if set to true, the method returns an associative
         *  array with the language name as key and the ISO code as value, if set
         *  to false (DEFAULT) it returns an array with the ISO code as key and
         *  the language name as value
         * @return  array associative array of the ISO codes and language names
         */
        function getISOcodes($flip = false)
        {
            $res = array(
                'aa' => 'afar',
                'ab' => 'abkhazian',
                'af' => 'afrikaans',
                'am' => 'amharic',
                'ar' => 'arabic',
                'as' => 'assamese',
                'ay' => 'aymara',
                'az' => 'azerbaijani',
                'ba' => 'bashkir',
                'be' => 'byelorussian',
                'bg' => 'bulgarian',
                'bh' => 'bihari',
                'bi' => 'bislama',
                'bn' => 'bengali',
                'bo' => 'tibetan',
                'br' => 'breton',
                'ca' => 'catalan',
                'co' => 'corsican',
                'cs' => 'czech',
                'cy' => 'welsh',
                'da' => 'danish',
                'de' => 'german',
                'dz' => 'bhutani',
                'el' => 'greek',
                'en' => 'english',
                'eo' => 'esperanto',
                'es' => 'epanish',
                'et' => 'estonian',
                'eu' => 'easque',
                'fa' => 'persian',
                'fi' => 'finnish',
                'fj' => 'fiji',
                'fo' => 'faeroese',
                'fr' => 'french',
                'fy' => 'frisian',
                'ga' => 'irish',
                'gd' => 'gaelic',
                'gl' => 'galician',
                'gn' => 'guarani',
                'gu' => 'gujarati',
                'ha' => 'hausa',
                'hi' => 'hindi',
                'hr' => 'croatian',
                'hu' => 'hungarian',
                'hy' => 'armenian',
                'ia' => 'interlingua',
                'ie' => 'interlingue',
                'ik' => 'inupiak',
                'in' => 'indonesian',
                'is' => 'icelandic',
                'it' => 'italian',
                'iw' => 'hebrew',
                'ja' => 'japanese',
                'ji' => 'yiddish',
                'jw' => 'javanese',
                'ka' => 'georgian',
                'kk' => 'kazakh',
                'kl' => 'greenlandic',
                'km' => 'cambodian',
                'kn' => 'kannada',
                'ko' => 'korean',
                'ks' => 'kashmiri',
                'ku' => 'kurdish',
                'ky' => 'kirghiz',
                'la' => 'latin',
                'ln' => 'lingala',
                'lo' => 'laothian',
                'lt' => 'lithuanian',
                'lv' => 'latvian',
                'mg' => 'malagasy',
                'mi' => 'maori',
                'mk' => 'macedonian',
                'ml' => 'malayalam',
                'mn' => 'mongolian',
                'mo' => 'moldavian',
                'mr' => 'marathi',
                'ms' => 'malay',
                'mt' => 'maltese',
                'my' => 'burmese',
                'na' => 'nauru',
                'ne' => 'nepali',
                'nl' => 'dutch',
                'no' => 'norwegian',
                'oc' => 'occitan',
                'om' => 'oromo',
                'or' => 'oriya',
                'pa' => 'punjabi',
                'pl' => 'polish',
                'ps' => 'pashto',
                'pt' => 'portuguese',
                'qu' => 'quechua',
                'rm' => 'rhaeto-romance',
                'rn' => 'kirundi',
                'ro' => 'romanian',
                'ru' => 'russian',
                'rw' => 'kinyarwanda',
                'sa' => 'sanskrit',
                'sd' => 'sindhi',
                'sg' => 'sangro',
                'sh' => 'serbo-croatian',
                'si' => 'singhalese',
                'sk' => 'slovak',
                'sl' => 'slovenian',
                'sm' => 'samoan',
                'sn' => 'shona',
                'so' => 'somali',
                'sq' => 'albanian',
                'sr' => 'serbian',
                'ss' => 'siswati',
                'st' => 'sesotho',
                'su' => 'sudanese',
                'sv' => 'swedish',
                'sw' => 'swahili',
                'ta' => 'tamil',
                'te' => 'tegulu',
                'tg' => 'tajik',
                'th' => 'thai',
                'ti' => 'tigrinya',
                'tk' => 'turkmen',
                'tl' => 'tagalog',
                'tn' => 'setswana',
                'to' => 'tonga',
                'tr' => 'turkish',
                'ts' => 'tsonga',
                'tt' => 'tatar',
                'tw' => 'twi',
                'uk' => 'ukrainian',
                'ur' => 'urdu',
                'uz' => 'uzbek',
                'vi' => 'vietnamese',
                'vo' => 'volapuk',
                'wo' => 'wolof',
                'xh' => 'xhosa',
                'yo' => 'yoruba',
                'zh' => 'chinese',
                'zu' => 'zulu');

            if ($flip)
            {
                $res = array_flip($res);
                ksort($res);
            }

            return $res;
        }

        /**
         * Get the ISO code for a language name
         *
         * @access  public
         * @static
         * @param   string lang language name
         * @return  string ISO code, boolean false if the language name
         *  has not been found
         */
        function getCode( $lang )
        {
            $tblCode = ISOCode::getISOcodes(true);
            return array_key_exists( $lang, $tblCode ) ? $tblCode[$lang] : false;
        }

        /**
         * Get the language name for an ISO code
         *
         * @access  public
         * @static
         * @param   string lang ISO code
         * @return  string language name, boolean false if the ISO code
         *  has not been found
         */
        function getLanguage( $iso )
        {
            $tblLanguage = ISOCode::getISOcodes(false);
            return array_key_exists( $iso, $tblLanguage ) ? $tblLanguage[$iso] : false;
        }
    }
?>