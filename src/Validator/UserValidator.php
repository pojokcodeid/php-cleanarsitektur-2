<?php

namespace Validator;

use Respect\Validation\Validator as v;

class UserValidator
{
  public static function validate($data)
  {
    $nameValidator = v::stringType()->length(3, 50);
    $emailValidator = v::email();

    $errors = [];

    if (!$nameValidator->validate($data['name'] ?? '')) {
      $errors[] = 'Name must be between 3 and 50 characters.';
    }

    if (!$emailValidator->validate($data['email'] ?? '')) {
      $errors[] = 'Invalid email format.';
    }

    return $errors;
  }

  public static function validateToJson($data)
  {
    $errors = self::validate($data);
    return json_encode(['message' => 'Validation errors', 'errors' => $errors]);
  }
}
