<?php

declare(strict_types=1);

namespace PlanificationManagement\Domain\Model\ValueObject;

final readonly class DateCollection
{
    /** @var string[] */
    private array $dates;

    /**
     * @param string[] $dates Array of YYYY-MM-DD date strings
     */
    public function __construct(array $dates = [])
    {
        $validDates = [];
        foreach ($dates as $dateStr) {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateStr)) {
                throw new \InvalidArgumentException("Invalid date format: {$dateStr}. Expected YYYY-MM-DD.");
            }
            $validDates[] = $dateStr;
        }

        $this->dates = array_unique($validDates);
    }

    public function contains(\DateTimeImmutable $date): bool
    {
        return in_array($date->format('Y-m-d'), $this->dates, true);
    }

    /** @return string[] */
    public function toArray(): array
    {
        return $this->dates;
    }
}
