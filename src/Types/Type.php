<?php

Namespace Crecket\DependencyManager\Types;

interface Type
{

    public function __construct($file);

    public function getFile();

}