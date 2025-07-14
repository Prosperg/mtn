# MTN Momo SDK Laravel
Ce package facilite l'intégration des paiement MTN Momo dans une application Laravel.

## Installation
composer require pkl/mtn-momo-sdk

### Configuration requise dans le .env
Ajoutez les variables d'environnement dans le fichier .env comme suit

    MTN_MOMO_ENV = sandbox | bénin, si vous êtes en sandbox 

    ## Pour le produit de collecte de fond
        MTN_MOMO_COLLECTION_PRIMARY_KEY
        MTN_MOMO_COLLECTION_SECONDARY_KEY
        MTN_MOMO_COLLECTION_CALLBACK_URL
        MTN_MOMO_COLLECTION_USER_ID
        MTN_MOMO_COLLECTION_API_KEY

    ## Pour le poduit de transfert de fond
        MTN_MOMO_DISBURSEMENT_PRIMARY_KEY
        MTN_MOMO_DISBURSEMENT_SECONDARY_KEY
        MTN_MOMO_DISBURSEMENT_CALLBACK_URL
        MTN_MOMO_DISBURSEMENT_USER_ID
        MTN_MOMO_DISBURSEMENT_API_KEY
    
    ## Pour le produit de Transfert International
        MTN_MOMO_REMITTANCE_PRIMARY_KEY
        MTN_MOMO_REMITTANCE_SECONDARY_KEY
        MTN_MOMO_REMITTANCE_CALLBACK_URL
        MTN_MOMO_REMITTANCE_USER_ID
        MTN_MOMO_REMITTANCE_API_KEY

#### Utilisation du SDK dans un projet laravel
# 1. Collection de fond
use Pkl\MtnMomo\Facades\Momo;

$response = Momo::Collection()->requestToPay(
    'REF123456', 
    '237699999999', 
    1000,
    'XOF'
);
// dd($response)
