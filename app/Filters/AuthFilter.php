<?php
namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use App\Entities\User;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        /** @var User|null $user */
        $user = $session->get('user');

        // Vérifier si l'utilisateur est connecté
        if (!$user instanceof User) {
            // Logger la tentative d'accès non autorisée
            log_message('info', 'Unauthorized access attempt to: ' . uri_string());

            // Nettoyer la session au cas où
            $session->destroy();

            // Rediriger vers la page de connexion avec un message
            return redirect()->to('/sign-in')->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        // Vérifier si l'utilisateur est toujours actif
        if (!$user->isActive()) {
            log_message('warning', "Inactive user {$user->id} tried to access: " . uri_string());
            $session->destroy();
            return redirect()->to('/sign-in')->with('error', 'Votre compte a été désactivé.');
        }

        // Vérifier la validité de la session (optionnel - sécurité renforcée)
        $lastActivity = $session->get('last_activity');
        $sessionTimeout = 3600; // 1 heure en secondes

        if ($lastActivity && (time() - $lastActivity) > $sessionTimeout) {
            log_message('info', "Session expired for user {$user->id}");
            $session->destroy();
            return redirect()->to('/sign-in')->with('error', 'Votre session a expiré. Veuillez vous reconnecter.');
        }

        // Mettre à jour l'activité de la session
        $session->set('last_activity', time());

        // Si des rôles spécifiques sont requis
        if (!empty($arguments)) {
            $isAllowed = false;

            foreach ($arguments as $requiredRoleSlug) {
                if ($user->check($requiredRoleSlug)) {
                    $isAllowed = true;
                    break;
                }
            }

            if (!$isAllowed) {
                // Logger la tentative d'accès avec des permissions insuffisantes
                log_message('warning', "User {$user->id} with insufficient permissions tried to access: " . uri_string() . " (required: " . implode(', ', $arguments) . ")");

                // Rediriger vers une page d'erreur 403
                return redirect()->to('/forbidden')->with('error', 'Vous n\'avez pas les permissions nécessaires pour accéder à cette page.');
            }
        }

        // Tout est OK, l'utilisateur peut continuer
        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Ajouter des headers de sécurité (optionnel)
        $response->setHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->setHeader('X-Content-Type-Options', 'nosniff');
        $response->setHeader('X-XSS-Protection', '1; mode=block');

        return $response;
    }
}