<?php

namespace App\Controller;

use App\Service\FrankfurterAPIService;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="app_main")
     */
    public function index(Request $request, FrankfurterAPIService $api): Response
    {
        $yesterday = (new DateTimeImmutable())->sub(new DateInterval('P1D'));
        $table = null;

        $builder = $this->createFormBuilder();
        $builder->add('date', DateType::class, [
            'widget' => 'single_text',
            'input' => 'datetime_immutable',
            // 'data' => $yesterday,
            'label' => false,
            'required' => true,
            'attr' => ['max' => $yesterday->format('Y-m-d')],
            'constraints' => [new NotBlank(), new LessThanOrEqual($yesterday)],
        ]);
        $builder->add('show', SubmitType::class, ['label' => 'Show']);
        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $indicated_day_rates = $api->getRates($form->getData()['date']);
            $today_rates = $api->getRates(new DateTime());
            $table = [];
            foreach ($today_rates as $currency => $today_exchange) {
                $indicated_day_exchange = $indicated_day_rates[$currency];
                $percentage_change = (($today_exchange * 100) / $indicated_day_exchange) - 100;
                $table[] = [
                    'currency' => $currency, 
                    'today' => $today_exchange, 
                    'indicated_day' => $indicated_day_exchange, 
                    'change' => $percentage_change
                ];
            }
        }

        return $this->render('main/index.html.twig', [
            'form' => $form->createView(),
            'table' => $table,
        ]);
    }
}
