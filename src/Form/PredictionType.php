<?php

namespace App\Form;

use App\Entity\Event;
use App\Entity\Prediction;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PredictionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('hostTeamScores')
            ->add('guestTeamScores')
            ->add('player', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'email',
            ])
            ->add('event', EntityType::class, [
                'class' => Event::class,
                'choice_label' => 'title',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Prediction::class,
        ]);
    }
}
