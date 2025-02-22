<?php

namespace Puzzle\Enums;

enum FieldValueType: string
{
    case Int = 'int';
    case Varchar = 'varchar';
    case Text = 'text';
    case Json = 'json';
    case Boolean = 'boolean';
    case Blob = 'bob';
}
