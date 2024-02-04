<?php

declare(strict_types=1);

namespace App\Helper;

final class RandomnessGenerator
{
    private string $digits;
    private string $alphaNum;
    private string $alphabet;

    public function __construct()
    {
        $this->digits = implode(range(1, 9));
        $this->alphaNum = implode(range(1, 9)).implode(range('a', 'z')).implode(range('A', 'Z'));
        $this->alphabet = implode(range('a', 'z'));
    }

    public function generateUriSafeString(int $length): string
    {
        return $this->generateStringOfLength($length, $this->alphaNum);
    }

    public function generateAlpha(int $length): string
    {
        return $this->generateStringOfLength($length, $this->alphabet);
    }

    public function generateNumeric(int $length): string
    {
        return $this->generateStringOfLength($length, $this->digits);
    }

    public function generateInt(int $min, int $max): int
    {
        return random_int($min, $max);
    }

    private function generateStringOfLength(int $length, string $pool): string
    {
        $alphabetMaxIndex = strlen($pool) - 1;
        $randomString = '';

        for ($i = 0; $i < $length; ++$i) {
            $index = random_int(0, $alphabetMaxIndex); // @phpstan-ignore-line
            $randomString .= $pool[$index];
        }

        return $randomString;
    }
}
