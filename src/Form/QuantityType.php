<?php

namespace App\Form;

use App\Entity\Basket;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class QuantityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', IntegerType::class, [
                'label' => 'Quantité',
                'attr' => ['class' => 'qty'],
                'label_attr' => ['class' => 'qtyLabel'],
                'error_bubbling' => false,
                'constraints' => [
                    new Assert\PositiveOrZero()
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => $options['button_label'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Basket::class, //le formulaire travaille avec des données brutes, sans lien avec l'entité
            'button_label' => 'Ajouter au panier', // valeur par défaut
            'attr' => ['novalidate' => 'novalidate', 'data-turbo' => 'false'],
        ]);
    }
}
