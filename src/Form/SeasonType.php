<?php

namespace App\Form;

use App\Entity\Season;
use App\Entity\Serie;
use App\Repository\SerieRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeasonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('number', IntegerType::class, [
                'label' => 'Season Number :',
            ])
            ->add('firstAirDate', DateType::class, [
                'widget' => 'single_text',
                'label' => 'First Air Date :',
            ])
            ->add('overview', TextareaType::class, [
                'label' => 'Overview :',
            ])
            ->add('poster')
            ->add('serie', EntityType::class, [
                'class' => Serie::class,
                'choice_label' => function (Serie $serie) {
                    return $serie->getName() . '( ' . count($serie->getSeasons()) . ')';
                },
                'label' => 'Serie :',
                'placeholder' => 'Choose a serie ...',
                'query_builder' => function (SerieRepository $repo) {
                    return $repo->createQueryBuilder('s')
                        ->orderBy('s.name', 'ASC');
                }
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter',
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Season::class,
        ]);
    }
}
