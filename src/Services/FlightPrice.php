<?php

namespace App\Services;

use App\Entity\FlightStats;
use App\Repository\FlightDestinationsRepository;
use App\Repository\FlightStatsRepository;
use App\Repository\SettingsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

class FlightPrice
{
    public function getPrice()
    {
        $today = new \DateTime('now');
        $today = $today->format('Y-m-d');
        $start_date_input = new \DateTime($this->settingsRepository->find('1')->getFlightStatsStartDate()->format('Y-m-d'));
        $default_max_start_date = max($today, $start_date_input->format('Y-m-d'));

        foreach ($this->flightDestinationsRepository->findBy(['isActive' => '1']) as $destination) {
            $start_date_by_destination = $destination->getDateStart();
            $end_date_by_destination = $destination->getDateEnd();
            $day_count_by_destination = date_diff($start_date_by_destination,$end_date_by_destination);

            $start_date = new \DateTime($default_max_start_date);
            if ($start_date_by_destination) {
                $start_date = max($today,$start_date_by_destination->format('Y-m-d'));
            }
            $day_increment = 1;
            $default_day_count=$this->settingsRepository->find('1')->getFlightStatsDays();
            $day_count=$default_day_count;
            if($day_count_by_destination>0){
                $day_count = $end_date_by_destination;
            }
            $departure_code = $destination->getDepartureCode();
            $arrival_code = $destination->getArrivalCode();

            while ($day_increment <= $day_count) {
                $date = $start_date->format('Y-m-d');
                $url = "https://www.kayak.co.uk/flights/" . $departure_code . "-" . $arrival_code . "/" . $date . "?sort=price_a&fs=stops=0";
                exec("node scrape/flightPrice.js" . " " . $url . " 2>&1");
                $file = $this->container->getParameter('scraper') . 'flightPrice.json';
                if (file_exists($file)) {
                    $file_content = file_get_contents($file);
                    $file_content_array = json_decode($file_content);
                    foreach ($file_content_array as $content) {
                        $price_as_string = $content->price;
                        $price_explode = explode('£', $price_as_string);
                        $price = $price_explode[1];
                        $old_price = $this->fightStatsRepository->findOneBy([
                            'flightFrom' => $departure_code,
                            'flightTo' => $arrival_code,
                            'date' => new \DateTime($date)
                        ]);
                        if ($old_price) {
                            $old_price->setLowestPrice($price);
                            $this->manager->flush();
                        } else {
                            $flightStats = new FlightStats();
                            $flightStats->setDate(new \DateTime($date))
                                ->setFlightFrom($departure_code)
                                ->setFlightTo($arrival_code)
                                ->setLowestPrice($price)
                                ->setScrapeDate(new \DateTime('now'));
                            $this->manager->persist($flightStats);
                            $this->manager->flush();
                        }
                    }
                    unlink($file);
                }
                $start_date->modify("+1 day");
                $day_increment++;
            }
        }
    }


    public function __construct(ContainerInterface $container, EntityManagerInterface $entityManager, FlightStatsRepository $flightStatsRepository, SettingsRepository $settingsRepository, FlightDestinationsRepository $flightDestinationsRepository)
    {
        $this->container = $container;
        $this->manager = $entityManager;
        $this->fightStatsRepository = $flightStatsRepository;
        $this->settingsRepository = $settingsRepository;
        $this->flightDestinationsRepository = $flightDestinationsRepository;
    }
}