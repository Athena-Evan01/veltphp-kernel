<?php

declare(strict_types=1);

namespace Velt\Kernel\Contracts;

interface ApplicationInterface
{
    /**
     * Retourne le chemin racine de l'application.
     */
    public function basePath(): string;

    /**
     * Retourne le container principal.
     */
    public function container(): ContainerInterface;

    /**
     * Retourne le repository de configuration.
     */
    public function config(): ConfigRepositoryInterface;

    /**
     * Retourne le dispatcher d'événements.
     */
    public function events(): EventDispatcherInterface;

    /**
     * Retourne le repository d'environnement.
     */
    public function env(): EnvRepositoryInterface;

    /**
     * Retourne le handler d'exceptions.
     */
    public function exceptions(): ExceptionHandlerInterface;

    /**
     * Retourne l'environnement courant.
     */
    public function environment(): string;

    /**
     * Vérifie si l'application est en mode local.
     */
    public function isLocal(): bool;

    /**
     * Vérifie si l'application est en mode testing.
     */
    public function isTesting(): bool;

    /**
     * Vérifie si l'application est en mode production.
     */
    public function isProduction(): bool;

    /**
     * Vérifie si le mode debug est actif.
     */
    public function isDebug(): bool;

    /**
     * Enregistre un provider.
     */
    public function registerProvider(
        string|ServiceProviderInterface $provider
    ): ServiceProviderInterface;

    /**
     * Boot tous les providers.
     */
    public function boot(): void;
}