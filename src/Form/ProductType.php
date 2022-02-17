<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use App\Form\Type\PriceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use App\Form\DataTransformer\CentimesTransofmer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Nom du produit '],
                'required' => false,

            ])
            ->add('shortDescription', TextareaType::class, [
                'label' => 'Description courte',
                'attr' => ['placeholder' => 'Mettez une petite description']
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix du produit',
                'attr' => ['placeholder' => 'Tapez le prix du produit en euro '],
                'divisor' => 100,
                'required' => false,


            ])
            ->add('mainPicture', UrlType::class, [
                'label' => 'Image du produit',
                'attr' => ['placeholder' => 'Tapez l\'url d\'image ']
            ])
            ->add('category', EntityType::class, [
                'label' => 'Categorie du produit ',

                'placeholder' => '-- Choisir une categorie -- ',
                'class' => Category::class,
                'choice_label' => 'name'
            ]);

        //$builder->get('price')->addModelTransformer(new CentimesTransofmer);

        /* $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $product = $event->getData();
            if ($product->getPrice !== null) {
                $product->setPrice($product->getPrice() * 100);
            }
        }); */

        /*  $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            /** @var Product */
        /*  $product = $event->getData();
            if ($product->getPrice() !== null) {
                $product->setPrice($product->getPrice() / 100);
            } */

        //dd($product);
        /* if ($product->getId() === null) {
                $form->add('category', EntityType::class, [
                    'label' => 'Categorie du produit ',

                    'placeholder' => '-- Choisir une categorie -- ',
                    'class' => Category::class,
                    'choice_label' => 'name'
                ]);
            } */
        //});
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
