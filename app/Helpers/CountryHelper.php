<?php

class CountryHelper
{
    /**
     * Get country name in Arabic
     */
    public static function getCountryNameArabic($countryCode)
    {
        $countries = self::getCountries();
        return $countries[$countryCode]['name_ar'] ?? $countryCode;
    }
    
    /**
     * Get country flag emoji
     */
    public static function getCountryFlag($countryCode)
    {
        $countries = self::getCountries();
        return $countries[$countryCode]['flag'] ?? '🌍';
    }
    
    /**
     * Get all countries data
     */
    private static function getCountries()
    {
        return [
            'SA' => ['name_ar' => 'السعودية', 'flag' => '🇸🇦'],
            'AE' => ['name_ar' => 'الإمارات', 'flag' => '🇦🇪'],
            'EG' => ['name_ar' => 'مصر', 'flag' => '🇪🇬'],
            'IQ' => ['name_ar' => 'العراق', 'flag' => '🇮🇶'],
            'JO' => ['name_ar' => 'الأردن', 'flag' => '🇯🇴'],
            'KW' => ['name_ar' => 'الكويت', 'flag' => '🇰🇼'],
            'LB' => ['name_ar' => 'لبنان', 'flag' => '🇱🇧'],
            'LY' => ['name_ar' => 'ليبيا', 'flag' => '🇱🇾'],
            'MA' => ['name_ar' => 'المغرب', 'flag' => '🇲🇦'],
            'OM' => ['name_ar' => 'عمان', 'flag' => '🇴🇲'],
            'PS' => ['name_ar' => 'فلسطين', 'flag' => '🇵🇸'],
            'QA' => ['name_ar' => 'قطر', 'flag' => '🇶🇦'],
            'SY' => ['name_ar' => 'سوريا', 'flag' => '🇸🇾'],
            'TN' => ['name_ar' => 'تونس', 'flag' => '🇹🇳'],
            'YE' => ['name_ar' => 'اليمن', 'flag' => '🇾🇪'],
            'DZ' => ['name_ar' => 'الجزائر', 'flag' => '🇩🇿'],
            'BH' => ['name_ar' => 'البحرين', 'flag' => '🇧🇭'],
            'SD' => ['name_ar' => 'السودان', 'flag' => '🇸🇩'],
            'US' => ['name_ar' => 'الولايات المتحدة', 'flag' => '🇺🇸'],
            'GB' => ['name_ar' => 'المملكة المتحدة', 'flag' => '🇬🇧'],
            'CA' => ['name_ar' => 'كندا', 'flag' => '🇨🇦'],
            'AU' => ['name_ar' => 'أستراليا', 'flag' => '🇦🇺'],
            'DE' => ['name_ar' => 'ألمانيا', 'flag' => '🇩🇪'],
            'FR' => ['name_ar' => 'فرنسا', 'flag' => '🇫🇷'],
            'IT' => ['name_ar' => 'إيطاليا', 'flag' => '🇮🇹'],
            'ES' => ['name_ar' => 'إسبانيا', 'flag' => '🇪🇸'],
            'NL' => ['name_ar' => 'هولندا', 'flag' => '🇳🇱'],
            'BE' => ['name_ar' => 'بلجيكا', 'flag' => '🇧🇪'],
            'CH' => ['name_ar' => 'سويسرا', 'flag' => '🇨🇭'],
            'AT' => ['name_ar' => 'النمسا', 'flag' => '🇦🇹'],
            'SE' => ['name_ar' => 'السويد', 'flag' => '🇸🇪'],
            'NO' => ['name_ar' => 'النرويج', 'flag' => '🇳🇴'],
            'DK' => ['name_ar' => 'الدنمارك', 'flag' => '🇩🇰'],
            'FI' => ['name_ar' => 'فنلندا', 'flag' => '🇫🇮'],
            'PL' => ['name_ar' => 'بولندا', 'flag' => '🇵🇱'],
            'RU' => ['name_ar' => 'روسيا', 'flag' => '🇷🇺'],
            'CN' => ['name_ar' => 'الصين', 'flag' => '🇨🇳'],
            'JP' => ['name_ar' => 'اليابان', 'flag' => '🇯🇵'],
            'KR' => ['name_ar' => 'كوريا الجنوبية', 'flag' => '🇰🇷'],
            'IN' => ['name_ar' => 'الهند', 'flag' => '🇮🇳'],
            'BR' => ['name_ar' => 'البرازيل', 'flag' => '🇧🇷'],
            'MX' => ['name_ar' => 'المكسيك', 'flag' => '🇲🇽'],
            'AR' => ['name_ar' => 'الأرجنتين', 'flag' => '🇦🇷'],
            'ZA' => ['name_ar' => 'جنوب أفريقيا', 'flag' => '🇿🇦'],
            'TR' => ['name_ar' => 'تركيا', 'flag' => '🇹🇷'],
            'ID' => ['name_ar' => 'إندونيسيا', 'flag' => '🇮🇩'],
            'MY' => ['name_ar' => 'ماليزيا', 'flag' => '🇲🇾'],
            'SG' => ['name_ar' => 'سنغافورة', 'flag' => '🇸🇬'],
            'TH' => ['name_ar' => 'تايلاند', 'flag' => '🇹🇭'],
            'VN' => ['name_ar' => 'فيتنام', 'flag' => '🇻🇳'],
            'PH' => ['name_ar' => 'الفلبين', 'flag' => '🇵🇭'],
            'NZ' => ['name_ar' => 'نيوزيلندا', 'flag' => '🇳🇿'],
            'IE' => ['name_ar' => 'أيرلندا', 'flag' => '🇮🇪'],
            'PT' => ['name_ar' => 'البرتغال', 'flag' => '🇵🇹'],
            'GR' => ['name_ar' => 'اليونان', 'flag' => '🇬🇷'],
            'CZ' => ['name_ar' => 'التشيك', 'flag' => '🇨🇿'],
            'HU' => ['name_ar' => 'المجر', 'flag' => '🇭🇺'],
            'RO' => ['name_ar' => 'رومانيا', 'flag' => '🇷🇴'],
            'BG' => ['name_ar' => 'بلغاريا', 'flag' => '🇧🇬'],
            'HR' => ['name_ar' => 'كرواتيا', 'flag' => '🇭🇷'],
            'SK' => ['name_ar' => 'سلوفاكيا', 'flag' => '🇸🇰'],
            'SI' => ['name_ar' => 'سلوفينيا', 'flag' => '🇸🇮'],
            'LT' => ['name_ar' => 'ليتوانيا', 'flag' => '🇱🇹'],
            'LV' => ['name_ar' => 'لاتفيا', 'flag' => '🇱🇻'],
            'EE' => ['name_ar' => 'إستونيا', 'flag' => '🇪🇪'],
            'IL' => ['name_ar' => 'إسرائيل', 'flag' => '🇮🇱'],
            'IR' => ['name_ar' => 'إيران', 'flag' => '🇮🇷'],
            'PK' => ['name_ar' => 'باكستان', 'flag' => '🇵🇰'],
            'BD' => ['name_ar' => 'بنغلاديش', 'flag' => '🇧🇩'],
            'AF' => ['name_ar' => 'أفغانستان', 'flag' => '🇦🇫'],
            'NG' => ['name_ar' => 'نيجيريا', 'flag' => '🇳🇬'],
            'KE' => ['name_ar' => 'كينيا', 'flag' => '🇰🇪'],
            'GH' => ['name_ar' => 'غانا', 'flag' => '🇬🇭'],
            'ET' => ['name_ar' => 'إثيوبيا', 'flag' => '🇪🇹'],
            'TZ' => ['name_ar' => 'تنزانيا', 'flag' => '🇹🇿'],
            'UG' => ['name_ar' => 'أوغندا', 'flag' => '🇺🇬'],
            'AO' => ['name_ar' => 'أنغولا', 'flag' => '🇦🇴'],
            'MZ' => ['name_ar' => 'موزمبيق', 'flag' => '🇲🇿'],
            'MG' => ['name_ar' => 'مدغشقر', 'flag' => '🇲🇬'],
            'CM' => ['name_ar' => 'الكاميرون', 'flag' => '🇨🇲'],
            'CI' => ['name_ar' => 'ساحل العاج', 'flag' => '🇨🇮'],
            'NE' => ['name_ar' => 'النيجر', 'flag' => '🇳🇪'],
            'BF' => ['name_ar' => 'بوركينا فاسو', 'flag' => '🇧🇫'],
            'ML' => ['name_ar' => 'مالي', 'flag' => '🇲🇱'],
            'MW' => ['name_ar' => 'مالاوي', 'flag' => '🇲🇼'],
            'ZM' => ['name_ar' => 'زامبيا', 'flag' => '🇿🇲'],
            'SN' => ['name_ar' => 'السنغال', 'flag' => '🇸🇳'],
            'TD' => ['name_ar' => 'تشاد', 'flag' => '🇹🇩'],
            'SO' => ['name_ar' => 'الصومال', 'flag' => '🇸🇴'],
            'ZW' => ['name_ar' => 'زيمبابوي', 'flag' => '🇿🇼'],
            'GN' => ['name_ar' => 'غينيا', 'flag' => '🇬🇳'],
            'RW' => ['name_ar' => 'رواندا', 'flag' => '🇷🇼'],
            'BJ' => ['name_ar' => 'بنين', 'flag' => '🇧🇯'],
            'BI' => ['name_ar' => 'بوروندي', 'flag' => '🇧🇮'],
            'TN' => ['name_ar' => 'تونس', 'flag' => '🇹🇳'],
            'SS' => ['name_ar' => 'جنوب السودان', 'flag' => '🇸🇸'],
            'TG' => ['name_ar' => 'توغو', 'flag' => '🇹🇬'],
            'SL' => ['name_ar' => 'سيراليون', 'flag' => '🇸🇱'],
            'LR' => ['name_ar' => 'ليبيريا', 'flag' => '🇱🇷'],
            'LY' => ['name_ar' => 'ليبيا', 'flag' => '🇱🇾'],
            'MR' => ['name_ar' => 'موريتانيا', 'flag' => '🇲🇷'],
            'ER' => ['name_ar' => 'إريتريا', 'flag' => '🇪🇷'],
            'GW' => ['name_ar' => 'غينيا بيساو', 'flag' => '🇬🇼'],
            'DJ' => ['name_ar' => 'جيبوتي', 'flag' => '🇩🇯'],
            'GM' => ['name_ar' => 'غامبيا', 'flag' => '🇬🇲'],
            'LS' => ['name_ar' => 'ليسوتو', 'flag' => '🇱🇸'],
            'GA' => ['name_ar' => 'الغابون', 'flag' => '🇬🇦'],
            'GQ' => ['name_ar' => 'غينيا الاستوائية', 'flag' => '🇬🇶'],
            'BW' => ['name_ar' => 'بوتسوانا', 'flag' => '🇧🇼'],
            'NA' => ['name_ar' => 'ناميبيا', 'flag' => '🇳🇦'],
            'MU' => ['name_ar' => 'موريشيوس', 'flag' => '🇲🇺'],
            'SC' => ['name_ar' => 'سيشيل', 'flag' => '🇸🇨'],
            'CV' => ['name_ar' => 'الرأس الأخضر', 'flag' => '🇨🇻'],
            'ST' => ['name_ar' => 'ساو تومي وبرينسيبي', 'flag' => '🇸🇹'],
            'KM' => ['name_ar' => 'جزر القمر', 'flag' => '🇰🇲'],
        ];
    }
}

