<?php
declare(strict_types=1);

namespace App\Entity;

use App\Repository\PlayRepository;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Collection;

/**
 * @ORM\Entity(repositoryClass=PlayRepository::class)
 */
class Play
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="plays")
     * @ORM\JoinColumn(nullable=false)
     */
    private User $user;

    /**
     * @ORM\ManyToOne(targetEntity=Quiz::class, inversedBy="plays")
     * @ORM\JoinColumn(nullable=false)
     */
    private Quiz $quiz;

    /**
     * @ORM\Column(type="integer")
     */
    private int $rightAnswersCount;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isFinish;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private ?int $time;

    /**
     * @ORM\ManyToOne(targetEntity=Question::class, inversedBy="plays")
     */
    private ?Question $question;

    public function __construct(User $user, Quiz $quiz)
    {
        $this->user=$user;
        $this->quiz=$quiz;
        $this->question=null;
        $this->isFinish=false;
        $this->rightAnswersCount=0;
        $this->time=0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getQuiz(): Quiz
    {
        return $this->quiz;
    }

    public function setQuiz(Quiz $quiz): self
    {
        $this->quiz = $quiz;
        return $this;
    }

    public function getRightAnswersCount(): ?int
    {
        return $this->rightAnswersCount;
    }

    public function setRightAnswersCount(int $rightAnswersCount): self
    {
        $this->rightAnswersCount = $rightAnswersCount;
        return $this;
    }

    public function getIsFinish(): ?bool
    {
        return $this->isFinish;
    }

    public function setIsFinish(bool $isFinish): self
    {
        $this->isFinish = $isFinish;
        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): self
    {
        $this->question = $question;
        return $this;
    }

    public function getTime(): ?int
    {
        return $this->time;
    }

    public function setTime(?int $time): self
    {
        $this->time = $time;
        return $this;
    }
}
