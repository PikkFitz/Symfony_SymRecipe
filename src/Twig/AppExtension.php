<?php

namespace App\Twig;

use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;

class AppExtension extends AbstractExtension
{
    /**
     * Création d'un filtre Twig pour convertir les minutes en xxhyymin
     *
     * @return array
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('min_to_hour', [$this, 'minutesToHours'])  // Filtre min_to_hour utilisé dans le fichier templates/pages/recipe/show.html.twig
        ];
    }


    public function minutesToHours($value)
    {
        if ($value < 60 || !$value) 
        {
            return $value;
        }

        $hours = floor($value / 60);
        $minutes = $value % 60;

        if ($minutes < 10) 
        {
            $minutes = '0' . $minutes;
        }

        $time = sprintf('%sh%s', $hours, $minutes);

        return $time;


    }
    
}









?>