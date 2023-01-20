<?php

namespace App\Controller\Admin;

use App\Entity\TypeMessage;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class TypeMessageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TypeMessage::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
