# Sous-module 01 - Kernel Contracts

## Mission

Ce sous-module definit le coeur minimal de Velt. Il contient les contrats, les conventions, les exceptions communes, le container de services minimal et les helpers dont les autres composants auront besoin.

Il doit rester petit. Son role n'est pas de devenir un framework complet, mais de fournir un langage commun aux autres modules.

## Perimetre

Inclus :

- structure de package `velt/kernel` ;
- contrats de base ;
- container minimal ;
- gestion simple de configuration ;
- exceptions communes ;
- bootstrap d'application ;
- helpers strictement necessaires.

Exclus :

- routing HTTP ;
- rendu UI ;
- acces database ;
- generation CLI avancee ;
- preview mobile.

## Issues

- [Issue 01 - Initialiser le package Kernel](issues/01-initialiser-package-kernel.md)
- [Issue 02 - Creer les contrats fondamentaux](issues/02-creer-contrats-fondamentaux.md)
- [Issue 03 - Implementer le container minimal](issues/03-implementer-container-minimal.md)

