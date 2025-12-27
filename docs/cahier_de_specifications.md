# Cahier de Spécification des Besoins – CuWiP (Customer Wireless Provider)

Version: 0.1 (projet initial)
Source d’entrée: cahier-de-charge.pdf (2 pages) et contexte WISP/FAI indiqué.

## 1. Contexte et Objectifs
- Mettre en place un progiciel de gestion pour un FAI/WISP couvrant: infrastructure (BTS/relai), cycle client (prospection → éligibilité → installation → facturation → paiement), et pilotage (dashboard, notifications WhatsApp/SMS).
- Objectifs: fiabiliser la collecte des données terrain, automatiser la facturation mensuelle, tracer les paiements (majoritairement cash), améliorer la visibilité via des statistiques clefs.

## 2. Périmètre
- Inclus: gestion BTS/relai, clients, étude d’éligibilité, installation, génération de factures, marquage des paiements, notifications (WhatsApp/SMS), dashboard et filtres, portail client (espace client) pour demandes d’abonnement et de réabonnement, suivi de statut et consultation factures.
- Exclus (à ce stade): paiement en ligne, calculs complexes de pro‑rata, gestion d’inventaire avancée, CRM complet, ticketing support.

## 3. Acteurs et Rôles
- Admin: gestion référentiels, utilisateurs/permissions, configuration facturation et notifications.
- Commercial/Partenaire: prospection, saisie client, étude d’éligibilité, suivi.
- Technicien: installation, validation de fin d’installation, ajout de photos.
- Comptable/Back‑office: génération mensuelle, gestion exclusions, suivi factures et paiements, relances.
- Direction: consultation de KPIs, exports, audit.
- Client final (espace client): création de compte/connexion, demande d’abonnement, demande de réabonnement, suivi de statut, téléchargement de factures et consents.

## 4. Processus Métiers (haut niveau)
1) Prospection/Éligibilité
   - Créer un client prospect avec coordonnées, GPS, photos, partenaire.
   - Lancer/saisir l’étude d’éligibilité (éligible/non‑éligible + commentaire).
2) Installation
   - Assigner une BTS/relai, réaliser l’installation, marquer « installation terminée ».
3) Facturation Mensuelle
   - Générer automatiquement les factures de la période pour clients actifs non exclus.
   - Option manuelle pour exclure certains clients d’une période.
4) Paiement
   - Saisie manuelle du paiement (cash usuel) et passage à l’état « payé ».
5) Notifications
   - Envoi WhatsApp/SMS pour émission de facture et confirmation de paiement.
6) Pilotage
   - Dashboard: filtres, répartition clients par BTS, volumes, statuts, alertes.

7) Espace Client (self‑service)
   - Création de compte ou connexion OTP (SMS/WhatsApp ou e‑mail) et consentement.
   - Demande d’abonnement: saisie coordonnées, adresse/GPS, type (domicile/entreprise), e‑mail facturation (entreprise), pièces/photos, choix d’offre (si applicable), prise de rendez‑vous d’installation (optionnel).
   - Demande de réabonnement: identification par code client ou téléphone + OTP, vérification dettes/éligibilité, confirmation des frais de réactivation, soumission.
   - Suivi de statut en temps réel (soumise → en étude → éligible/ non éligible → planification → installée/active → close) avec notifications.

## 5. Exigences Fonctionnelles (IDs traçables)

### 5.1 Infrastructure – BTS/Relais
- F-BTS-01: Créer/modifier/supprimer une BTS avec ville, code unique court, position GPS, composants, photos.
- F-BTS-02: Générer/valider l’unicité du code (concaténation lieu + numéro déploiement).
- F-BTS-03: Lister/chercher/filtrer BTS (par ville, code, couverture, statut).

### 5.2 Gestion des Clients
- F-CLI-01: Créer client avec nom, téléphone, type (domicile/entreprise), e‑mail facturation (entreprise), position GPS, photos, partenaire/prospecteur.
- F-CLI-02: Générer un code client unique par l’application.
- F-CLI-03: Associer client à une BTS une fois installé.
- F-CLI-04: États client: `prospect` → `éligible`/`non_éligible` → `installé` → `actif` → `suspendu`/`résilié`.

### 5.3 Étude d’Éligibilité
- F-ELI-01: Enregistrer le résultat (éligible/non) + commentaire + date + auteur.
- F-ELI-02: Historiser les études (audit) et dernier statut reflété sur la fiche client.

### 5.4 Installation
- F-INS-01: Saisir la date/technicien/commentaire + photos.
- F-INS-02: Marquer l’installation « terminée » via un contrôle radio/bouton.
- F-INS-03: Passage automatique à l’état client `installé` puis `actif` (si validé).

### 5.5 Facturation Mensuelle
- F-FAC-01: Générer automatiquement les factures pour tous les clients `actifs` non exclus.
- F-FAC-02: Permettre d’exclure un client d’une période (motif + trace).
- F-FAC-03: Gérer les statuts facture: `générée` → `envoyée` → `payée` → `annulée`.
- F-FAC-04: Numérotation facture unique par période (ex. `FAC-YYYYMM-####`).
- F-FAC-05: Générer un PDF de facture (logo, infos client, période, montant, conditions).
- F-FAC-06: Re‑génération manuelle possible en cas de correction (avec versioning).

### 5.6 Paiement (majoritairement cash)
- F-PAI-01: Enregistrer un paiement (date, montant, mode, référence, note, opérateur).
- F-PAI-02: Marquer la facture « payée » et le client « à jour » pour la période.
- F-PAI-03: Historiser tous les paiements et permettre la recherche/export.

### 5.7 Notifications
- F-NOT-01: À la génération/envoi facture: envoyer WhatsApp ou SMS au client.
- F-NOT-02: À l’enregistrement paiement: envoyer confirmation.
- F-NOT-03: Journaliser statut d’envoi (succès/échec) et permettre relance.

### 5.8 Dashboard et Reporting
- F-DAS-01: Filtres multi‑critères (BTS, ville, statut client, période, partenaire, type).
- F-DAS-02: KPIs: nb clients total/actifs, clients par BTS, factures générées/payées/impayées, taux de paiement, exclusions.
- F-DAS-03: Exports CSV/PDF selon filtres (droits requis).

### 5.9 Administration
- F-ADM-01: Gestion des utilisateurs, rôles et permissions (RBAC).
- F-ADM-02: Paramètres facturation (montants, taxes, références), canaux notifications, planning auto.
- F-ADM-03: Journal d’audit (qui a fait quoi, quand).

### 5.10 Espace Client (Portail)
- F-PORT-01: Inscription/connexion client. Authentification par OTP (SMS/WhatsApp ou e‑mail); option mot de passe si requis; gestion des sessions et révocation.
- F-PORT-02: Profil client: nom, téléphone (vérifié), e‑mail (optionnel), adresse; consentements (contact, traitements, notifications).
- F-PORT-03: Demande d’abonnement: type (domicile/entreprise), adresse + GPS, e‑mail de facturation (entreprise), photos du site, choix d’offre/forfait (si défini), commentaires.
- F-PORT-04: Demande de réabonnement: identification par code client/téléphone, validation OTP, contrôle dettes/impayés et frais; création d’une demande liée au client existant.
- F-PORT-05: Suivi de demande: timeline et statut (`soumise`, `à_étudier`, `éligible`, `non_éligible`, `planification`, `installée`, `active`, `rejetée`, `annulée`).
- F-PORT-06: Prise de rendez‑vous: proposition de créneaux par l’entreprise; client choisit/valide; reprogrammation possible avec règles.
- F-PORT-07: Consultation des factures pour clients actifs; téléchargement PDF; instructions de paiement (pas de paiement en ligne à ce stade).
- F-PORT-08: Notifications au client (WhatsApp/SMS/e‑mail) à chaque changement majeur de statut.
- F-PORT-09: Association/désassociation d’un compte espace‑client avec un `code client` existant via OTP.
- F-PORT-10: Annulation d’une demande par le client avec motif, selon fenêtre autorisée.

## 6. Exigences Non Fonctionnelles (NFR)
- NFR-SEC: Authentification, RBAC granulaire, chiffrement TLS, masquage téléphone dans exports publics.
- NFR-PERF: Recherche/liste < 2 s sur 10k clients; génération mensuelle < 30 min/10k factures.
- NFR-FIAB: Planification idempotente (pas de doublons), reprise sur incident, logs et alertes.
- NFR-DISP: 99.5% dispo ouvrée; backup quotidien; rétention données 5 ans (factures).
- NFR-UX: Mobile‑first pour terrain; saisies assistées (GPS, photo), validations claires.
- NFR-I18N: FR par défaut; extensible à EN.
- NFR-ACC: Contrastes et tailles lisibles, clavier utilisable (baseline WCAG AA ciblée).
- NFR-PORTAL-SEC: OTP 6 chiffres expirant en ≤10 min; anti‑brute force (rate‑limit, captcha si nécessaire); sessions chiffrées; consentements horodatés.
- NFR-PORTAL-DATA: Isolation des données par compte client; visibilité restreinte aux factures/demandes du compte associé; traçabilité des liaisons compte↔client.

## 7. Modèle de Données (conceptuel)
- `Bts(id, code*, ville, gps(lat,lng), composants, photos[])`
- `Client(id, code*, nom, telephone, type{domicile|entreprise}, email_facturation?, gps, photos[], partenaire_id?, bts_id?, statut)`
- `Eligibilite(id, client_id, resultat{eligible|non_eligible}, commentaire, auteur_id, date)`
- `Installation(id, client_id, date, technicien_id, commentaire, photos[], termine)`
- `Facture(id, client_id, periode(YYYY-MM), montant, devise, statut, numero*, pdf_url, exclue(bool), motif_exclusion?)`
- `Paiement(id, facture_id, date, montant, mode{cash|transfert|autre}, reference?, note, operateur_id)`
- `Partenaire(id, nom, contact)`
- `Notification(id, client_id?, facture_id?, canal{whatsapp|sms}, message, statut{envoye|echec}, date, meta)`
Clés `*` uniques; relations: `Client`→`Bts`(N:1), `Facture`→`Client`(N:1), `Paiement`→`Facture`(N:1).

### 7.1 Extension Portail Client
- `CompteClient(id, telephone*, email?, nom?, statut, created_at)`
- `LienCompteClient(id, compte_id, client_id, actif)` (plusieurs comptes → un client et inversement, contrôlé)
- `Demande(id, compte_id, client_id?, type{abonnement|reabonnement}, statut, adresse, gps, photos[], commentaire, offre_id?, frais_reactivation?, created_at)`
- `RendezVous(id, demande_id, date, plage, statut{proposé|confirmé|reporté|annulé}, commentaire)`
- `Consentement(id, compte_id, type{contact|notifications|donnees}, valeur, date)`
- `OtpToken(id, canal{sms|whatsapp|email}, identite, code, expire_at, valide)`

## 8. Règles Métier
- RB-01: Un code BTS et un code client sont uniques et immuables.
- RB-02: Une facture ne peut être « payée » sans paiement enregistré.
- RB-03: Un client `non_éligible` ne peut être `installé`.
- RB-04: La génération mensuelle ignore les clients `exclus` pour la période ciblée.
- RB-05: Toute modification post‑émission d’une facture crée une nouvelle version PDF.
- RB-06: Une demande « réabonnement » n’est acceptée que si les dettes/frais sont explicitement confirmés par le client; activation déclenchée après validation back‑office.
- RB-07: Un compte portail ne peut accéder qu’aux données liées via `LienCompteClient` actif; la liaison requiert un OTP envoyé au téléphone du client référencé.

## 9. Intégrations
- WhatsApp Business API et/ou SMS (ex: Twilio/MessageBird) via un service d’abstraction; callback pour statuts d’envoi.
- Génération PDF serveur (librairie à définir) avec modèles incluant logos (`logo.jpeg`, `logo_black.jpeg`).

## 10. API et Tâches Planifiées (indicatif)
- Endpoints principaux: `/bts`, `/clients`, `/eligibilites`, `/installations`, `/factures`, `/paiements`, `/notifications`.
- Tâche CRON mensuelle: création factures pour `periode=YYYY-MM`; idempotence par verrou de période.

### 10.1 API Portail (indicatif)
- `POST /portal/auth/otp/request` (canal, identite)
- `POST /portal/auth/otp/verify` → session
- `GET /portal/me` / `PATCH /portal/me` (profil, consentements)
- `POST /portal/liens` (lier code client ↔ compte via OTP)
- `POST /portal/demandes` (type: abonnement|reabonnement)
- `GET /portal/demandes` / `GET /portal/demandes/{id}` / `PATCH ...` (annulation, replanification)
- `GET /portal/factures` / `GET /portal/factures/{id}` (téléchargement PDF)

## 11. Dashboard & Rapports
- Tableaux: Clients (filtres), Factures (statuts), BTS (répartition, charge), Paiements (par période, mode), Exclusions.
- Graphiques: clients par BTS, taux recouvrement par mois, volumétrie notifications.

## 12. Sécurité, Conformité, Audit
- RGPD‑like: base légale « intérêt légitime/prestation », droit à rectification; rétention 5 ans (factures), 2 ans (prospects) — à confirmer.
- Audit complet: CRUD entités sensibles, génération et envoi de factures/notifications, opérations de paiement.

Portail: capturer preuves de consentement (horodatage, version des CGU), conserver traces d’OTP (hash/échec), appliquer rate‑limit par IP et par identifiant, prévoir mécanismes de blocage/déblocage.

## 13. Hypothèses et Questions Ouvertes
- Montants/tarification: forfait unique ou offres multiples? taxes applicables?
- Pro‑rata en mois d’installation: requis ou non?
- Fuseau horaire et heure d’exécution de la génération (ex: 01:00 locale).
- Fournisseurs de notifications validés localement; exigences de conformité.
- Format exact du numéro de facture et mentions légales locales.
- Portail: mode d’auth préféré (OTP seul vs mot de passe), durée de session; périmètre des documents acceptés; politique de prise de rendez‑vous (créneaux, SLA réponse).

## 14. Critères d’Acceptation (extraits)
- CA-01: Création BTS: tentative de doublon `code` refusée avec message clair.
- CA-02: Passage client `installé`: champs requis (date, technicien) validés; état mis à jour.
- CA-03: Génération mensuelle: pour N clients actifs non exclus, N factures créées en < X minutes; aucune facture en double si relancée.
- CA-04: Paiement: marquer une facture « payée » met à jour le solde client; une notification de confirmation est envoyée et tracée.
- CA-05: Dashboard: filtres combinés retournent résultats en < 2 s sur 10k enregistrements.
- CA-06: Portail: un utilisateur peut créer un compte via OTP, soumettre une demande d’abonnement avec GPS et pièces jointes, et voir le statut évoluer jusqu’à `active`.
- CA-07: Portail réabonnement: un client suspendu peut demander réactivation; si dettes existent, l’écran affiche le montant et les conditions; la demande passe à `active` uniquement après validation back‑office.

## 15. Roadmap proposée
- MVP (v0): BTS, Clients, Éligibilité, Installation, Facturation simple, Paiement manuel, Notifications, Dashboard minimal, RBAC.
- v1: Exports avancés, exclusions massives, modèles PDF personnalisables, relances automatiques.
- v2: Offres/tarifs multiples, prorata, portail client, inventaire équipements.

## 16. Traçabilité (exemple)
- Épic « Facturation mensuelle » couvre F-FAC-01..06, F-NOT-01..03, CA-03..04, NFR‑FIAB.

---
Document généré à partir du cahier des charges fourni; sujets « à clarifier » doivent être validés avant conception détaillée.
