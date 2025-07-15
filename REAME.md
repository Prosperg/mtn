# MTN MoMo SDK Laravel

Ce package Laravel simplifie l'intégration de l'API MTN MoMo pour la **collecte** et le **transfert de fonds**.

## 1. 📦 Installation

```bash
composer require pkl/mtn-momo-sdk 

```

## 2. ⚙️ Configuration

MTN_MOMO_ENV=sandbox # ou mtnbenin, etc.

# Pour la collecte de fonds
```
MTN_MOMO_COLLECTION_PRIMARY_KEY=
MTN_MOMO_COLLECTION_SECONDARY_KEY=
MTN_MOMO_COLLECTION_CALLBACK_URL=
```

# Pour le transfert de fonds
MTN_MOMO_DISBURSEMENT_PRIMARY_KEY=
MTN_MOMO_DISBURSEMENT_SECONDARY_KEY=
MTN_MOMO_DISBURSEMENT_CALLBACK_URL=

# Pour les transferts internationaux (En cours de développement)
MTN_MOMO_REMITTANCE_PRIMARY_KEY=
MTN_MOMO_REMITTANCE_SECONDARY_KEY=
MTN_MOMO_REMITTANCE_CALLBACK_URL=

## 3. 🚀 Utilisation
Assurez-vous d’importer la façade
```
<?php 
use Pkl\MtnMomo\Facades\Momo;

```
### Cas d'utilisation par service
### 💰 1. Collection de fonds (Collection)
    🔹 Demander un paiement (requestToPay)
        $response = Momo::collection()->requestToPay(
            '229XXXXXXXX',   // Numéro du payeur
            1000,            // Montant
            'EUR',           // Devise (par défaut EUR)
            'Paiement de facture', // Message du payeur
            'Merci pour votre paiement' // Note du bénéficiaire
        );
    
    🔹 Vérifier le statut du paiement (getRequestToPayStatus)
        $status = Momo::collection()->getRequestToPayStatus($referenceId);

### 💸 2. Transfert de fonds (Disbursement)
    🔹 Envoyer de l'argent (deposit)
            $response = Momo::disbursement()->deposit(
            1000,             // Montant
            '229XXXXXXXX',    // Numéro du destinataire
            'Paiement client',// Message
            'EUR'             // Devise (par défaut EUR)
        );

    🔹 Vérifier le statut du dépôt (getDepositStatus)
        $status = Momo::disbursement()->getDepositStatus($referenceId);

    🔹 Vérifier le solde de votre compte MoMo (checkOwnAccountBalance)
        $balance = Momo::disbursement()->checkOwnAccountBalance();

    🔹 Effectuer un remboursement (refundBalance)
        $refund = Momo::disbursement()->refundBalance(
            1000,             // Montant
            'EUR',            // Devise
            'Message payeur', // Message pour le payeur
            'Note bénéficiaire', // Note pour le bénéficiaire
            $referenceIdToRefund  // Référence du paiement original
        );

    🔹 Vérifier le statut d’un remboursement (checkRefundStatus)
        $status = Momo::disbursement()->checkRefundStatus($refundUuid);

## 📚 Notes supplémentaires

 1. Toutes les méthodes génèrent automatiquement un UUID de transaction unique.

 2. Le SDK utilise les clés d’API définies dans le .env selon le service utilisé.

 3. Assurez-vous d’avoir bien activé les produits MoMo dans votre compte développeur MTN (https://momodeveloper.mtn.com).