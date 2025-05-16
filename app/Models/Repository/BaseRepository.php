<?php

declare(strict_types=1);

namespace App\Models\Repository;

use Nette\Database\Explorer;

class BaseRepository
{
    public function __construct(
        public readonly Explorer $db
    ) {}
}
