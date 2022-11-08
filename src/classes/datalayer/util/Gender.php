<?php

namespace Application\datalayer\util;

enum Gender
{
    case Male;
    case Female;
    case Other;

    public static function humanize(Gender $gender): string
    {
        return $gender->name;
    }
}