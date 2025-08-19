<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Crée un compte administrateur avec les informations spécifiées',
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('Cette commande crée un compte administrateur avec l\'email admin.findmyasso@gmail.com et le mot de passe FindMyAsso93');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Création du compte administrateur FindMyAsso');

        // Vérifier si l'admin existe déjà
        $existingAdmin = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'admin.findmyasso@gmail.com']);
        
        if ($existingAdmin) {
            $io->warning('Un compte avec l\'email admin.findmyasso@gmail.com existe déjà !');
            
            if ($existingAdmin->hasRole('ROLE_ADMIN')) {
                $io->info('Ce compte a déjà le rôle ROLE_ADMIN.');
            } else {
                $io->info('Ce compte n\'a pas le rôle ROLE_ADMIN. Ajout du rôle...');
                $roles = $existingAdmin->getRoles();
                if (!in_array('ROLE_ADMIN', $roles)) {
                    $roles[] = 'ROLE_ADMIN';
                    $existingAdmin->setRoles($roles);
                    $this->entityManager->flush();
                    $io->success('Rôle ROLE_ADMIN ajouté au compte existant.');
                }
            }
            
            return Command::SUCCESS;
        }

        // Créer le nouvel administrateur
        $admin = new User();
        $admin->setEmail('admin.findmyasso@gmail.com');
        $admin->setFullName('Administrateur FindMyAsso');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setIsVerified(true);
        $admin->setCreatedAt(new \DateTimeImmutable());

        // Hasher le mot de passe
        $hashedPassword = $this->passwordHasher->hashPassword($admin, 'FindMyAsso93');
        $admin->setPassword($hashedPassword);

        // Sauvegarder en base
        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $io->success([
            'Compte administrateur créé avec succès !',
            '',
            'Email: admin.findmyasso@gmail.com',
            'Mot de passe: FindMyAsso93',
            'Rôle: ROLE_ADMIN',
            'Statut: Vérifié',
            '',
            'Vous pouvez maintenant vous connecter à l\'interface d\'administration.'
        ]);

        return Command::SUCCESS;
    }
}
