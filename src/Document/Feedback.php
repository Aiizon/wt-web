<?php

namespace App\Document;

use App\Entity\Customer;
use App\Entity\Offer;
use App\Repository\CustomerRepository;
use App\Repository\OfferRepository;
use DateTime;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document(collection: "feedbacks")]
class Feedback
{
    #[ODM\Id]
    private string $id;

    #[ODM\Field(type: "string")]
    public string $title;

    #[ODM\Field(type: "string")]
    public string $content;

    #[ODM\Field(type: "string")]
    public string $customerIdentifier;

    private Customer $customer;

    #[ODM\Field(type: "int")]
    public int $offerId;

    private Offer $offer;

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

    public function getCustomerIdentifier(): string
    {
        return $this->customerIdentifier;
    }

    public function getCustomer(CustomerRepository $customerRepository): Customer
    {
        if (null === $this->customer) {
            $this->customer = $customerRepository->findOneBy(["email" => $this->customerIdentifier]);
        }

        return $this->customer;
    }

    public function setCustomerIdentifier(string $customerIdentifier): void
    {
        $this->customerIdentifier = $customerIdentifier;
    }

    public function getOfferId(): int
    {
        return $this->offerId;
    }

    public function getOffer(OfferRepository $offerRepository): Offer
    {
        if (null === $this->offer) {
            $this->offer = $offerRepository->find($this->offerId);
        }

        return $this->offer;
    }

    public function setOffer(int $offerId): void
    {
        $this->offerId = $offerId;
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