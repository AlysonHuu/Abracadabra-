<?php
namespace App\Service;

use App\Entity\BonFidelite;
use App\Entity\Compte;
use App\Entity\Reservation;
use Doctrine\ORM\EntityManagerInterface;

class FideliteService
{
    private const SEUIL      = 5;
    private const REDUCTION  = 0.40;
    private const AGE_JEUNE  = 25;

    public function __construct(private EntityManagerInterface $em) {}

  
    public function calculerPrix(Compte $user, float $prixBase, ?string $codeBon = null): array
    {
        $prix           = $prixBase;
        $reductionJeune = false;
        $bonApplique    = false;

        
        $age = $user->getAge();
        if ($age !== null && $age < self::AGE_JEUNE) {
            $prix           = round($prixBase * (1 - self::REDUCTION), 2);
            $reductionJeune = true;
        }

  
        if ($codeBon) {
            /** @var BonFidelite|null $bon */
            $bon = $this->em->getRepository(BonFidelite::class)->findByCode($codeBon);
            if ($bon && $bon->getUser() === $user) {
                $prix        = 0.0;
                $bonApplique = true;
            }
        }

        return [
            'prix'            => $prix,
            'reduction_jeune' => $reductionJeune,
            'bon_applique'    => $bonApplique,
        ];
    }

  
    public function verifierEtAttribuerBon(Compte $user): ?BonFidelite
    {
        $totalPayantes = $this->em->getRepository(Reservation::class)
            ->count(['user' => $user, 'gratuite' => false]);

        $bonsExistants = $this->em->getRepository(BonFidelite::class)
            ->count(['user' => $user]);

      
        if (intdiv($totalPayantes, self::SEUIL) > $bonsExistants) {
            $bon = new BonFidelite();
            $bon->setUser($user);
            $bon->setCode('MAGIC-' . strtoupper(bin2hex(random_bytes(4))));
            $bon->setUtilise(false);
            $this->em->persist($bon);
            $this->em->flush();
            return $bon;
        }

        return null;
    }


    public function utiliserBon(BonFidelite $bon): void
    {
        $bon->setUtilise(true);
        $bon->setUtiliseAt(new \DateTimeImmutable());
        $this->em->flush();
    }
}