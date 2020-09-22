<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class UniqueAnswersValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof UniqueAnswers) {
            throw new UnexpectedTypeException($constraint, UniqueAnswers::class);
        }
        if (null===$value) {
            return;
        }
        $answers= array();
        foreach ($value as $answer) {
            $answers[]=$answer->getText();
        }
        $uniqueAnswers=array_unique($answers);
        if (count($uniqueAnswers)!=count($answers)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
