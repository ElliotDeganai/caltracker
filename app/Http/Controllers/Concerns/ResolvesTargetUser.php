<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;

trait ResolvesTargetUser
{
    /**
     * Retourne l'ID de l'utilisateur dont on doit lire/écrire les données :
     * - l'utilisateur connecté par défaut
     * - OU l'utilisateur "visualisé" en session, si le connecté a la permission
     *   manage-users (admin) et qu'il a choisi de se placer sur un autre compte.
     */
    protected function targetUserId(Request $request): int
    {
        $user = $request->user();
        $viewingId = session('viewing_user_id');

        if ($viewingId && $user->can('manage-users')) {
            return (int) $viewingId;
        }

        return $user->id;
    }
}
