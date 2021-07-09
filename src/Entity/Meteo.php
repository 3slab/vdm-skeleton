<?php

namespace App\Entity;

use App\Repository\MeteoRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MeteoRepository::class)
 */
class Meteo
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     * @ORM\GeneratedValue(strategy="NONE")
     *
     * @var string|null
     */
    protected $id;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var DateTime|null
     */
    protected $date;

    /**
     * @ORM\Column(type="float")
     *
     * @var float|null
     */
    protected $long;

    /**
     * @ORM\Column(type="float")
     *
     * @var float|null
     */
    protected $lat;

    /**
     * @ORM\Column(type="float", nullable=true)
     *
     * @var float|null
     */
    protected $temperature;

    /**
     * @ORM\Column(type="float", nullable=true)
     *
     * @var float|null
     */
    protected $temperatureFelt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int|null
     */
    protected $pressure;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int|null
     */
    protected $humidity;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    protected $weather;

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string|null $id
     * @return Meteo
     */
    public function setId(?string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime|null $date
     * @return Meteo
     */
    public function setDate(?DateTime $date): self
    {
        $this->date = $date;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getLong(): ?float
    {
        return $this->long;
    }

    /**
     * @param float|null $long
     * @return Meteo
     */
    public function setLong(?float $long): self
    {
        $this->long = $long;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getLat(): ?float
    {
        return $this->lat;
    }

    /**
     * @param float|null $lat
     * @return Meteo
     */
    public function setLat(?float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getTemperature(): ?float
    {
        return $this->temperature;
    }

    /**
     * @param float|null $temperature
     * @return Meteo
     */
    public function setTemperature(?float $temperature): self
    {
        $this->temperature = $temperature;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getTemperatureFelt(): ?float
    {
        return $this->temperatureFelt;
    }

    /**
     * @param float|null $temperatureFelt
     * @return Meteo
     */
    public function setTemperatureFelt(?float $temperatureFelt): self
    {
        $this->temperatureFelt = $temperatureFelt;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPressure(): ?int
    {
        return $this->pressure;
    }

    /**
     * @param int|null $pressure
     * @return Meteo
     */
    public function setPressure(?int $pressure): self
    {
        $this->pressure = $pressure;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getHumidity(): ?int
    {
        return $this->humidity;
    }

    /**
     * @param int|null $humidity
     * @return Meteo
     */
    public function setHumidity(?int $humidity): self
    {
        $this->humidity = $humidity;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getWeather(): ?string
    {
        return $this->weather;
    }

    /**
     * @param string|null $weather
     * @return Meteo
     */
    public function setWeather(?string $weather): self
    {
        $this->weather = $weather;

        return $this;
    }
}
