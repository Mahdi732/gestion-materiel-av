<?php
namespace Services;

use Interfaces\ICommandeRepository;
use Interfaces\IPaiement;
use Interfaces\INotificationObserver;
use Models\CommandeVente;

class GestionVente
{
    private ICommandeRepository $commandeRepo;
    private IPaiement $strategiePaiement;
    /** @var INotificationObserver[] */
    private array $observers = [];

    public function __construct(ICommandeRepository $commandeRepo)
    {
        $this->commandeRepo = $commandeRepo;
    }

    public function ajouterObserver(INotificationObserver $observer): void
    {
        $this->observers[] = $observer;
    }

    private function notifierObservers(string $message): void
    {
        foreach ($this->observers as $observer) {
            $observer->notifier($message);
        }
    }

    public function validerCommande(CommandeVente $commande, float $montant, IPaiement $strategiePaiement): bool
    {
        $this->strategiePaiement = $strategiePaiement;

        if (!$this->strategiePaiement->payer($montant)) {
            $commande->setStatut('paiement échoué');
            $this->commandeRepo->save($commande);
            $this->notifierObservers("Le paiement de la commande {$commande->getId()} a échoué.");
            return false;
        }


        $commande->setStatut('en attends');
        $this->commandeRepo->save($commande);

        $this->notifierObservers("La commande {$commande->getId()} a été validée et payée.");

        return true;
    }

    public function getCommandes(): array
    {
        return $this->commandeRepo->getAll();
    }

    public function getUserCommande(int $id): array
    {
        return $this->commandeRepo->findByUser($id);
    }

    public function delete(int $id): bool
    {
        return $this->commandeRepo->delete($id);
    }
}
