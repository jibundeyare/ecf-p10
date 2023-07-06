<?php

namespace App\DataFixtures;

use App\Entity\Auteur;
use App\Entity\Livre;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TestFixtures extends Fixture implements FixtureGroupInterface
{
    private $faker;
    private $hasher;
    private $manager;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->faker = FakerFactory::create('fr_FR');
        $this->hasher = $hasher;
    }

    public static function getGroups(): array
    {
        return ['test'];
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;
        $this->loadUsers();
        $this->loadAuteurs();
        $this->loadLivres();
    }

    public function loadUsers()
    {
        // static test data
        $datas = [
            [
                // user account data
                'email' => 'foo.foo@example.com',
                'roles' => ["ROLE_USER"],
                'password' => '123',
                'enabled' => true,
                // borrower profile data
                'nom' => 'foo',
                'prenom' => 'foo',
                'tel' => '123456789',
            ],
            [
                // user account data
                'email' => 'bar.bar@example.com',
                'roles' => ["ROLE_USER"],
                'password' => '123',
                'enabled' => false,
                // borrower profile data
                'nom' => 'bar',
                'prenom' => 'bar',
                'tel' => '123456789',
            ],
            [
                // user account data
                'email' => 'baz.baz@example.com',
                'roles' => ["ROLE_USER"],
                'password' => '123',
                'enabled' => true,
                // borrower profile data
                'nom' => 'bar',
                'prenom' => 'bar',
                'tel' => '123456789',
            ],
        ];

        foreach ($datas as $data) {
            $user = new User();
            $user->setEmail($data['email']);
            $user->setRoles($data['roles']);

            $password = $this->hasher->hashPassword($user, $data['password']);
            $user->setPassword($password);

            $user->setEnabled($data['enabled']);

            $this->manager->persist($user);

            // @todo insert borrowers
        }

        $this->manager->flush();

        // dynamic test data
        for ($i = 0; $i < 100; $i++) {
            $user = new User();
            $user->setEmail($this->faker->safeEmail());
            $user->setRoles(["ROLE_USER"]);

            $password = $this->hasher->hashPassword($user, '123');
            $user->setPassword($password);

            $user->setEnabled(true);

            $this->manager->persist($user);

            // @todo insert borrowers
        }

        $this->manager->flush();
    }

    public function loadAuteurs()
    {
        $datas = [
            [
                'nom' => 'auteur inconnu',
                'prenom' => '',
            ],
            [
                'nom' => 'Cartier',
                'prenom' => 'Hugues',
            ],
            [
                'nom' => 'Lambert',
                'prenom' => 'Armand',
            ],
            [
                'nom' => 'Moitessier',
                'prenom' => 'Thomas',
            ],
        ];

        foreach ($datas as $data) {
            $auteur = new Auteur();
            $auteur->setNom($data['nom']);
            $auteur->setPrenom($data['prenom']);

            $this->manager->persist($auteur);
        }

        $this->manager->flush();

        for ($i = 0; $i < 500; $i++) {
            $auteur = new Auteur();
            $auteur->setNom($this->faker->lastName());
            $auteur->setPrenom($this->faker->firstName());

            $this->manager->persist($auteur);
        }

        $this->manager->flush();
    }

    public function loadLivres()
    {
        $repository = $this->manager->getRepository(Auteur::class);
        $auteurs = $repository->findAll();

        $datas = [
            [
                'titre' => 'Lorem ipsum dolor sit amet',
                'anneeEdition' => 2010,
                'nombrePages' => 100,
                'codeIsbn' => '9785786930024',
                'auteurId' => 1,
            ],
            [
                'titre' => 'Consectetur adipiscing elit',
                'anneeEdition' => 2011,
                'nombrePages' => 150,
                'codeIsbn' => '9783817260935',
                'auteurId' => 2,
            ],
            [
                'titre' => 'Mihi quidem Antiochum',
                'anneeEdition' => 2012,
                'nombrePages' => 200,
                'codeIsbn' => '9782020493727',
                'auteurId' => 3,
            ],
            [
                'titre' => 'Quem audis satis belle',
                'anneeEdition' => 2013,
                'nombrePages' => 250,
                'codeIsbn' => '9794059561353',
                'auteurId' => 4,
            ],
        ];

        foreach ($datas as $data) {
            $livre = new Livre();
            $livre->setTitre($data['titre']);
            $livre->setAnneeEdition($data['anneeEdition']);
            $livre->setNombrePages($data['nombrePages']);
            $livre->setCodeIsbn($data['codeIsbn']);

            $auteur = $repository->find($data['auteurId']);
            $livre->setAuteur($auteur);

            $this->manager->persist($livre);
        }

        $this->manager->flush();

        for ($i = 0; $i < 1000; $i++) {
            $livre = new Livre();

            $livre->setTitre($this->faker->sentence(3));

            $anneeEdition = random_int(1800, 2023);
            $livre->setAnneeEdition($anneeEdition);

            $nombrePages = random_int(10, 1000);
            $livre->setNombrePages($nombrePages);

            $codeIsbn = $this->faker->isbn13();
            $livre->setCodeIsbn($codeIsbn);

            $auteurIndex = random_int(0, 503);
            $livre->setAuteur($auteurs[$auteurIndex]);

            $this->manager->persist($livre);
        }

        $this->manager->flush();
    }
}
