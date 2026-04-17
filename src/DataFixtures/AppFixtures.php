<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly userPasswordHasherInterface $passwordHasher)
    {

    }

    public function load(ObjectManager $manager): void
    {
        // crée 4 utilisateurs
        for ($i = 1; $i < 5; $i++) {
            $user = new User();
            $nom = str_repeat($i, 3);
            $user->setFirstname($i);
            $user->setLastname($nom);
            $user->setEmail($nom . '@toto.com');
            $user->setPassword($this->passwordHasher->hashPassword($user, 'aze'));
            $user->setCgu(true);
            $user->setApi(false);
            $manager->persist($user);
        }

        // crée 9 produits
        $nomProduit = [
            'Kit d\'hygiène recyclable',
            'Shot Tropical',
            'Gourde en bois',
            'Disques Démaquillants x3',
            'Bougie Lavande & Patchouli',
            'Brosse à dent',
            'Kit couvert en bois',
            'Nécessaire, déodorant Bio',
            'Savon Bio'
        ];
        $descCourte = [
            'Kit d\'hygiène écologique et réutilisable, idéal pour un quotidien zéro déchet.',
            'Boisson énergétique tropicale, rafraîchissante et riche en vitamines pour un coup de boost instantané.',
            'Gourde en bois naturel, légère et écologique, pour une hydratation stylée et durable.',
            'Lot de 3 disques démaquillants lavables, doux pour la peau et écologiques.',
            'Bougie parfumée à la lavande et au patchouli, pour une ambiance apaisante et relaxante.',
            'Brosse à dents ergonomique, pour un brossage efficace et un sourire éclatant.',
            'Kit de couverts en bois, léger et écologique, idéal pour les pique-niques et les repas nomades.',
            'Déodorant bio sans alcool ni parabènes, pour une protection naturelle et efficace.',
            'Savon bio nourrissant, à l\'huile d\'olive et au karité, pour une peau douce et hydratée.',
        ];

        $descLongue = [
            'Ce kit d\'hygiène recyclable est conçu pour réduire les déchets plastiques. Il contient des accessoires durables et réutilisables, comme une brosse à dents en bambou, un rasoir en métal et un savon solide. Parfait pour une routine respectueuse de l\'environnement, il allie praticité et écologie.',
            'Le Shot Tropical est une boisson concentrée à base de fruits exotiques, conçue pour apporter énergie et vitalité. Riche en vitamines et antioxydants, il est idéal pour un regain d\'énergie rapide. Son goût fruité et rafraîchissant en fait un allié parfait pour les journées chargées ou les séances de sport.',
            'Cette gourde en bois allie élégance et respect de l\'environnement. Fabriquée à partir de matériaux naturels et durables, elle est légère et facile à transporter. Son design épuré en fait un accessoire tendance, tout en limitant l\'utilisation de plastique. Parfaite pour les sorties ou le bureau.',
            'Ces disques démaquillants réutilisables sont fabriqués en coton bio, doux pour la peau et respectueux de l\'environnement. Lavables et durables, ils remplacent avantageusement les cotons jetables. Idéaux pour un démaquillage efficace et une routine beauté zéro déchet, ils sont économiques et pratiques.',
            'Cette bougie artisanale allie les notes apaisantes de la lavande et les accents boisés du patchouli. Fabriquée à partir de cire naturelle, elle diffuse une douce lumière et un parfum enveloppant, idéal pour créer une ambiance relaxante. Parfaite pour les moments de détente ou de méditation.',
            'Cette brosse à dents est conçue pour offrir un brossage optimal, avec des poils souples et une tête ergonomique. Elle permet d\'atteindre toutes les zones de la bouche, tout en préservant les gencives. Son design pratique et ses matériaux durables en font un accessoire indispensable pour une hygiène bucco-dentaire parfaite.',
            'Ce kit de couverts en bois est une alternative écologique aux couverts en plastique. Léger et résistant, il est parfait pour les pique-niques, les repas au bureau ou les voyages. Les couverts sont fabriqués à partir de bois durable et sont faciles à nettoyer, ce qui en fait un choix pratique et respectueux de l\'environnement.',
            'Ce déodorant bio est formulé à partir d\'ingrédients naturels, sans alcool ni parabènes, pour une protection douce et efficace. Il neutralise les odeurs tout en respectant la peau et l\'environnement. Son format pratique en stick ou en pot en fait un indispensable pour une hygiène quotidienne saine et écologique.',
            'Ce savon bio est enrichi en huile d\'olive et en beurre de karité, offrant une mousse onctueuse et une hydratation intense. Sans produits chimiques agressifs, il convient à tous les types de peau, même les plus sensibles. Son parfum naturel et ses propriétés adoucissantes en font un incontournable pour une toilette douce et respectueuse de l\'environnement.'
        ];

        for ($i = 0; $i < 9; $i++) {
            $product = new Product();
            $product->setName($nomProduit[$i]);
            $product->setPicture('/images/produit' . ($i + 1) . '.jpg');
            $rand = rand(5, 32) + (mt_rand(0, 99) / 100);
            $prix = number_format($rand, 2, '.', '');
            $product->setPrice($prix);
            $product->setShortDesc($descCourte[$i]);
            $product->setLongDesc($descLongue[$i]);
            $manager->persist($product);
        }


        $manager->flush();
    }
}





