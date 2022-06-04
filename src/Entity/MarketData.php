<?php

namespace App\Entity;

use App\Repository\MarketDataRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MarketDataRepository::class)
 */
class MarketData
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $sharePrice;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $shareCompany;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $companiesHouse;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $weblink;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $assetClass;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $login;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $investorSite;





    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSharePrice(): ?float
    {
        return $this->sharePrice;
    }

    public function setSharePrice(?float $sharePrice): self
    {
        $this->sharePrice = $sharePrice;

        return $this;
    }

    public function getShareCompany(): ?string
    {
        return $this->shareCompany;
    }

    public function setShareCompany(?string $shareCompany): self
    {
        $this->shareCompany = $shareCompany;

        return $this;
    }

    public function getCompaniesHouse(): ?string
    {
        return $this->companiesHouse;
    }

    public function setCompaniesHouse(?string $companiesHouse): self
    {
        $this->companiesHouse = $companiesHouse;

        return $this;
    }

    public function getWeblink(): ?string
    {
        return $this->weblink;
    }

    public function setWeblink(?string $weblink): self
    {
        $this->weblink = $weblink;

        return $this;
    }

    public function getAssetClass(): ?string
    {
        return $this->assetClass;
    }

    public function setAssetClass(?string $assetClass): self
    {
        $this->assetClass = $assetClass;

        return $this;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(?string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getInvestorSite(): ?string
    {
        return $this->investorSite;
    }

    public function setInvestorSite(?string $investorSite): self
    {
        $this->investorSite = $investorSite;

        return $this;
    }










}
