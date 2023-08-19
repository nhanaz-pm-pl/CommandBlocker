<?php

declare(strict_types=1);

namespace NhanAZ\CommandBlocker\blocker;

use NhanAZ\CommandBlocker\blocker\command\CommandBlocker;

class WorldBlocker {

    /** @var CommandBlocker[] $blocker*/
    private array $blocker;

    public function __construct(array $blocker) {
        $this->blocker = $blocker;
    }

    public function getCommandBlockers(): array {
        return $this->blocker;
    }

    public function getCommandBlocker(string $command): ?CommandBlocker {
        foreach ($this->blocker as $blocker) {
            if ($blocker->getCommand() === $command) {
                return $blocker;
            }
        }
        return null;
    }

    public function testPermissions(array $permissions): ?CommandBlocker {
        foreach ($this->blocker as $blocker) {
            $intersect = array_intersect($blocker->getPermissions(), $permissions);
            if (!empty($intersect)) {
                return $blocker;
            }
        }
        return null;
    }

    /**
     * @param CommandBlocker[] $commandBlockers
     *
     * @return WorldBlocker
     */
    public static function fromArray(array $commandBlockers) : WorldBlocker {
        return new WorldBlocker($commandBlockers);
    }

}