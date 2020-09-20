<?php
declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UniqueAnswers extends Constraint
{
    public $message = 'Answers.should.be.unique';
}