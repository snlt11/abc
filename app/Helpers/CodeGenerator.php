<?php

namespace App\Helpers;

class CodeGenerator
{
    /**
     * Generate random passwords with uppercase letters and numbers, with no repeated characters
     * 
     * @param int $length The length of each password (minimum 8, maximum 36)
     * @param int|null $count If provided, generates this many passwords
     * @return string|array Single password string or array of passwords
     * 
     * Generate a single 8-character password
     * $password = PasswordGenerator::generate();
     *
     * Generate a single 12-character password
     * $password = PasswordGenerator::generate(12);
     * 
     * Generate 100,000 passwords with 8 characters each
     * $passwords = PasswordGenerator::generate(8, 100000);
     * 
     */
    public static function generate(int $length = 8, ?int $count = null)
    {
        // For batch generation
        if ($count !== null && $count > 0) {
            $passwords = [];
            for ($i = 0; $i < $count; $i++) {
                $passwords[] = self::generateSingle($length);
            }
            return $passwords;
        }
        
        // For single password generation
        return self::generateSingle($length);
    }
    
    /**
     * Generate a single random password
     * 
     * @param int $length The length of the password
     * @return string The generated password
     */
    private static function generateSingle(int $length): string
    {
        // Ensure minimum length of 8
        $length = max(8, $length);
        
        // Define character sets - only uppercase and numbers
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $numbers = '0123456789';
        
        // Combine all characters
        $allChars = $uppercase . $numbers;
        
        // Maximum possible length is the number of available characters
        $maxLength = strlen($allChars);
        $length = min($length, $maxLength);
        
        // Pre-calculate lengths for performance
        $uppercaseLength = strlen($uppercase);
        $numbersLength = strlen($numbers);
        
        // Initialize result array with the required size
        $result = array_fill(0, $length, '');
        
        // Ensure we have at least one uppercase and one number
        $result[0] = $uppercase[random_int(0, $uppercaseLength - 1)];
        $result[1] = $numbers[random_int(0, $numbersLength - 1)];
        
        // Fill the rest with random characters, ensuring no duplicates
        $usedChars = [$result[0] => true, $result[1] => true];
        $allCharsArray = str_split($allChars);
        
        // Shuffle the remaining characters for better randomness
        $remainingChars = array_diff($allCharsArray, array_keys($usedChars));
        shuffle($remainingChars);
        
        // Fill the remaining positions
        $j = 0;
        for ($i = 2; $i < $length; $i++) {
            // If we've used all available characters, just use random ones
            if ($j >= count($remainingChars)) {
                $result[$i] = $allChars[random_int(0, $maxLength - 1)];
            } else {
                $result[$i] = $remainingChars[$j++];
            }
        }
        
        // Shuffle to randomize positions
        shuffle($result);
        
        return implode('', $result);
    }
}