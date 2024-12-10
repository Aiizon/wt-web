<?php

namespace App\Document;

use App\Entity\Customer;
use App\Entity\Offer;
use DateTime;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use MongoDB\BSON\ObjectId;

#[ODM\Document(collection: "feedbacks")]
class Feedback
{
    #[ODM\Id]
    private ObjectId $id;

    #[ODM\Field(type: "string")]
    public string $title;

    #[ODM\Field(type: "string")]
    public string $content;

    #[ODM\ReferenceOne(targetDocument: Customer::class)]
    public Customer $customer;

    #[ODM\ReferenceOne(targetDocument: Offer::class)]
    public Offer $offer;

    #[ODM\Field(type: "date")]
    public DateTime $createdAt;

    #[ODM\Field(type: "boolean")]
    public bool $isVisible = true;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function getOffer(): Offer
    {
        return $this->offer;
    }

    public function setOffer(Offer $offer): void
    {
        $this->offer = $offer;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function isVisible(): bool
    {
        return $this->isVisible;
    }

    public function setVisible(bool $isVisible): void
    {
        $this->isVisible = $isVisible;
    }
}