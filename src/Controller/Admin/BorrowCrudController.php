<?php

namespace App\Controller\Admin;

use App\Entity\Borrow;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

class BorrowCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Borrow::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateField::new('date_borrow'),
            DateField::new('date_return')
        ];
    }
}
