<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('country_code', 3);
            $table->string('phone_prefix', 10);
            $table->string('currency_code', 3);
            $table->string('currency_name');
            $table->string('flag')->nullable();
            $table->timestamps();
        });

        $asianCountries = [
            ['AF', 'Afghanistan', '93', 'AFN', 'Afghan Afghani', '🇦🇫'],
            ['AM', 'Armenia', '374', 'AMD', 'Armenian Dram', '🇦🇲'],
            ['AZ', 'Azerbaijan', '994', 'AZN', 'Azerbaijani Manat', '🇦🇿'],
            ['BH', 'Bahrain', '973', 'BHD', 'Bahraini Dinar', '🇧🇭'],
            ['BD', 'Bangladesh', '880', 'BDT', 'Bangladeshi Taka', '🇧🇩'],
            ['BT', 'Bhutan', '975', 'BTN', 'Bhutanese Ngultrum', '🇧🇹'],
            ['BN', 'Brunei', '673', 'BND', 'Brunei Dollar', '🇧🇳'],
            ['KH', 'Cambodia', '855', 'KHR', 'Cambodian Riel', '🇰🇭'],
            ['CN', 'China', '86', 'CNY', 'Chinese Yuan', '🇨🇳'],
            ['CY', 'Cyprus', '357', 'EUR', 'Euro', '🇨🇾'],
            ['GE', 'Georgia', '995', 'GEL', 'Georgian Lari', '🇬🇪'],
            ['IN', 'India', '91', 'INR', 'Indian Rupee', '🇮🇳'],
            ['ID', 'Indonesia', '62', 'IDR', 'Indonesian Rupiah', '🇮🇩'],
            ['IR', 'Iran', '98', 'IRR', 'Iranian Rial', '🇮🇷'],
            ['IQ', 'Iraq', '964', 'IQD', 'Iraqi Dinar', '🇮🇶'],
            ['IL', 'Israel', '972', 'ILS', 'Israeli New Shekel', '🇮🇱'],
            ['JP', 'Japan', '81', 'JPY', 'Japanese Yen', '🇯🇵'],
            ['JO', 'Jordan', '962', 'JOD', 'Jordanian Dinar', '🇯🇴'],
            ['KZ', 'Kazakhstan', '7', 'KZT', 'Kazakhstani Tenge', '🇰🇿'],
            ['KW', 'Kuwait', '965', 'KWD', 'Kuwaiti Dinar', '🇰🇼'],
            ['KG', 'Kyrgyzstan', '996', 'KGS', 'Kyrgystani Som', '🇰🇬'],
            ['LA', 'Laos', '856', 'LAK', 'Laotian Kip', '🇱🇦'],
            ['LB', 'Lebanon', '961', 'LBP', 'Lebanese Pound', '🇱🇧'],
            ['MY', 'Malaysia', '60', 'MYR', 'Malaysian Ringgit', '🇲🇾'],
            ['MV', 'Maldives', '960', 'MVR', 'Maldivian Rufiyaa', '🇲🇻'],
            ['MN', 'Mongolia', '976', 'MNT', 'Mongolian Tugrik', '🇲🇳'],
            ['MM', 'Myanmar', '95', 'MMK', 'Myanmar Kyat', '🇲🇲'],
            ['NP', 'Nepal', '977', 'NPR', 'Nepalese Rupee', '🇳🇵'],
            ['KP', 'North Korea', '850', 'KPW', 'North Korean Won', '🇰🇵'],
            ['OM', 'Oman', '968', 'OMR', 'Omani Rial', '🇴🇲'],
            ['PK', 'Pakistan', '92', 'PKR', 'Pakistani Rupee', '🇵🇰'],
            ['PS', 'Palestine', '970', 'ILS', 'Israeli New Shekel', '🇵🇸'],
            ['PH', 'Philippines', '63', 'PHP', 'Philippine Peso', '🇵🇭'],
            ['QA', 'Qatar', '974', 'QAR', 'Qatari Rial', '🇶🇦'],
            ['RU', 'Russia', '7', 'RUB', 'Russian Ruble', '🇷🇺'],
            ['SA', 'Saudi Arabia', '966', 'SAR', 'Saudi Riyal', '🇸🇦'],
            ['SG', 'Singapore', '65', 'SGD', 'Singapore Dollar', '🇸🇬'],
            ['KR', 'South Korea', '82', 'KRW', 'South Korean Won', '🇰🇷'],
            ['LK', 'Sri Lanka', '94', 'LKR', 'Sri Lankan Rupee', '🇱🇰'],
            ['SY', 'Syria', '963', 'SYP', 'Syrian Pound', '🇸🇾'],
            ['TW', 'Taiwan', '886', 'TWD', 'New Taiwan Dollar', '🇹🇼'],
            ['TJ', 'Tajikistan', '992', 'TJS', 'Tajikistani Somoni', '🇹🇯'],
            ['TH', 'Thailand', '66', 'THB', 'Thai Baht', '🇹🇭'],
            ['TL', 'Timor-Leste', '670', 'USD', 'US Dollar', '🇹🇱'],
            ['TR', 'Turkey', '90', 'TRY', 'Turkish Lira', '🇹🇷'],
            ['TM', 'Turkmenistan', '993', 'TMT', 'Turkmenistani Manat', '🇹🇲'],
            ['AE', 'United Arab Emirates', '971', 'AED', 'UAE Dirham', '🇦🇪'],
            ['UZ', 'Uzbekistan', '998', 'UZS', 'Uzbekistani Som', '🇺🇿'],
            ['VN', 'Vietnam', '84', 'VND', 'Vietnamese Dong', '🇻🇳'],
            ['YE', 'Yemen', '967', 'YER', 'Yemeni Rial', '🇾🇪'],
        ];

        $countries = array_map(function ($country) {
            return [
                'id' => (string) Str::uuid(),
                'name' => $country[1],
                'country_code' => $country[0],
                'phone_prefix' => '+' . $country[2],
                'currency_code' => $country[3],
                'currency_name' => $country[4],
                'flag' => $country[5],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $asianCountries);

        foreach (array_chunk($countries, 20) as $chunk) {
            DB::table('countries')->insert($chunk);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
