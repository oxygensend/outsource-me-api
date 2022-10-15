<?php

namespace App\DTO;


class AddTechnologyRequestDto extends AbstractRequestDto
{
    protected string $iri;

    public function getIri(): string
    {
        return $this->iri;
    }


}