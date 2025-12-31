<?php

// Opschonen van input
function sanitize(string $value): string
{
    return trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
}

// Controlle op lege velden
function required(string $value): bool
{
    return $value !== '';
}

// Valideer e-mailadres
function validEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Valideer aanhef
function validSalutation(string $value): bool
{
    return in_array($value, ['heer', 'mevrouw'], true);
}