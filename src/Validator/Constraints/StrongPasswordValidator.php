<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class StrongPasswordValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof StrongPassword) {
            throw new UnexpectedTypeException($constraint, StrongPassword::class);
        }

        if ($value === null || $value === '') {
            return;
        }

        if (preg_match('/^(.)\1+$/', $value)) {
            $this->addViolation($constraint, 'не состоит из одинаковых символов');
            return;
        }

        $object = $this->context->getObject();
        if ($object !== null) {
            if (method_exists($object, 'getName') && strcasecmp($value, (string) $object->getName()) === 0) {
                $this->addViolation($constraint, 'не должен совпадать с именем');
                return;
            }
            if (method_exists($object, 'getEmail') && strcasecmp($value, (string) $object->getEmail()) === 0) {
                $this->addViolation($constraint, 'не должен совпадать с email');
                return;
            }
        }

        $digits = preg_match_all('/\d/', $value);
        if ($digits < 2) {
            $this->addViolation($constraint, 'не менее 2 цифр');
            return;
        }

        $letters = preg_match_all('/[a-zA-Zа-яА-Я]/u', $value);
        if ($letters < 3) {
            $this->addViolation($constraint, 'не менее 3 букв');
            return;
        }

        $specials = preg_match_all('/[^a-zA-Zа-яА-Я0-9]/u', $value);
        if ($specials < 1) {
            $this->addViolation($constraint, 'не менее 1 спецсимвола');
            return;
        }
    }

    private function addViolation(StrongPassword $constraint, string $reason): void
    {
        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ reason }}', $reason)
            ->addViolation();
    }
}