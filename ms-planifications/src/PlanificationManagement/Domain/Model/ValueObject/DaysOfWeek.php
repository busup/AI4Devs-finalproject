<?php

declare(strict_types=1);

namespace PlanificationManagement\Domain\Model\ValueObject;

final readonly class DaysOfWeek
{
    public const MONDAY    = 1;
    public const TUESDAY   = 2;
    public const WEDNESDAY = 4;
    public const THURSDAY  = 8;
    public const FRIDAY    = 16;
    public const SATURDAY  = 32;
    public const SUNDAY    = 64;

    private const ALL_DAYS = 127;

    public function __construct(public readonly int $bitmask)
    {
        if ($bitmask < 0 || $bitmask > self::ALL_DAYS) {
            throw new \InvalidArgumentException("Invalid bitmask for DaysOfWeek: {$bitmask}");
        }
    }

    /**
     * @param string $day 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun' etc.
     */
    public function isActiveOn(string $dayName): bool
    {
        $dayMapping = [
            'Mon' => self::MONDAY,
            'Tue' => self::TUESDAY,
            'Wed' => self::WEDNESDAY,
            'Thu' => self::THURSDAY,
            'Fri' => self::FRIDAY,
            'Sat' => self::SATURDAY,
            'Sun' => self::SUNDAY,
        ];

        $dayKey = substr(ucfirst(strtolower($dayName)), 0, 3);
        if (!isset($dayMapping[$dayKey])) {
            throw new \InvalidArgumentException("Unknown day name: {$dayName}");
        }

        return $this->includes($dayMapping[$dayKey]);
    }

    public function includes(int $dayBit): bool
    {
        return ($this->bitmask & $dayBit) === $dayBit;
    }
}
