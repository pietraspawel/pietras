<?php

namespace pietras;

class Date extends \DateTime
{
    public function __toString(): ?string
    {
        return $this->format("d.m.Y H:i:s");
    }

    public function toDBFormat(): ?string
    {
        return $this->format("Y-m-d H:i:s");
    }

    public function getMinute(): ?string
    {
        return $this->format("i");
    }
}
