<?php

namespace App\Form;

use App\Entity\Reservation;

use App\Validator\Constraints\OpenHours;
use Symfony\Component\Form\AbstractType;
use App\Validator\Constraints\DateNotPassed;
use App\Validator\Constraints\NotAllowedDays;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class ReservationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',TextType::class)
            ->add('prenom',TextType::class)
            ->add('numTel',NumberType::class)
            ->add('rdv',DateTimeType::class, [
                'widget' => 'single_text',
                "constraints" => [
                    new DateNotPassed(),
                    new OpenHours(),
                    new NotAllowedDays(),
                ],
            ])
            ->add('prestation')
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}
