<?php

namespace App\DTO;

use Symfony\Component\Security\Core\User\UserInterface;

class TokenCreationDTO
{
    public UserInterface $user;
    public string        $description;

    public static function create
    (
        string        $description,
        UserInterface $user
    ): self
    {
        $dto = new self();

        $dto->user        = $user;
        $dto->description = $description;

        return $dto;
    }
}