<?php

return [

    'does_not_exist' => 'L\'étiquette de statut n\'existe pas.',
    'assoc_assets'	 => 'Cette étiquette de statut est actuellement associée avec au moins un bien et ne peut être supprimée. Merci de mettre à jour vos biens pour ne plus référencer ce statut et essayez à nouveau. ',

    'create' => [
        'error'   => 'L\'étiquette de statut n\'a pas été créée, merci d\'essayer à nouveau.',
        'success' => 'L\'étiquette de statut a bien été créée.',
    ],

    'update' => [
        'error'   => 'L\'étiquette de statut n\'a pas été mise à jour, merci de réessayer',
        'success' => 'L\'étiquette de statut a bien été mise à jour.',
    ],

    'delete' => [
        'confirm'   => 'Etes-vous sûr de vouloir supprimer cette étiquette de statut?',
        'error'   => 'Un problème est survenu durant la suppression de cette étiquette de statut. Merci d\'essayer à nouveau.',
        'success' => 'L\'étiquette de statut a bien été supprimée.',
    ],

    'help' => [
        'undeployable'   => 'Ces actifs ne peuvent être attribués à personne.',
        'deployable'   => 'These assets can be checked out. Once they are assigned, they will assume a meta status of <i class="fas fa-circle text-blue"></i> <strong>Deployed</strong>.',
        'archived'   => 'Ces éléments ne peuvent pas être extraits et ne s\'afficheront que dans la vue Archivée. Ceci est utile pour conserver des informations sur les actifs à des fins budgétaires / historiques, mais les garder hors de la liste des actifs au jour le jour.',
        'pending'   => 'Ces actifs ne peuvent pas encore être attribués à qui que ce soit, souvent utilisés pour des articles en réparation, mais qui devraient revenir à la circulation.',
    ],

];
