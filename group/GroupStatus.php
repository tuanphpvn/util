<?php

namespace go1\util\group;

class GroupStatus
{
    const PRIVATE = 0;
    const PUBLIC  = 1;
    const LOCKED  = 2;
    const ALL     = [self::PUBLIC, self::LOCKED, self::PRIVATE];

    public static function label(int $status): string
    {
        switch ($status) {
            case self::PRIVATE:
                $label = "Private";
                break;

            case self::PUBLIC:
                $label = "Public";
                break;

          case self::LOCKED:
                $label = "Locked";
                break;

            default:
                $label = "";
                break;
        }

        return $label;
    }

    public static function value(string $label): int
    {
        switch ($label) {
            case "Private":
            case "private":
                $status = self::PRIVATE;
                break;

            case "Public":
            case "public":
                $status = self::PUBLIC;
                break;

            case "Locked":
            case "locked":
                $status = self::LOCKED;
                break;

            default:
                $status = 0;
                break;
        }

        return $status;
    }
}
