<?php

declare(strict_types=1);

namespace RouteManagement\Domain\Model\ValueObject;

enum ApprovalStatus: string
{
    case Pending  = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
}
