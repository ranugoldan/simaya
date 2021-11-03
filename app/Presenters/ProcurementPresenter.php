<?php

namespace App\Presenters;

class ProcurementPresenter extends Presenter
{
    public static function dataTableLayout()
    {
        $layout = [
            [
                "field" => "id",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('general.id'),
                "visible" => false
            ],
            [
                "field" => "procurement_tag",
                "searchable" => true,
                "sortable" => true,
                "title" => trans('admin/procurements/table.procurement_tag'),
                "visible" => true,
                "formatter" => "procurementsLinkFormatter"
            ],
            [
                "field" => "status",
                "searchable" => true,
                "sortable" => true,
                "title" => trans('general.status'),
                "visible" => true
            ],
            [
                "field" => "supplier",
                "searchable" => true,
                "sortable" => true,
                "title" => trans('general.supplier'),
                "visible" => true,
                "formatter" => "suppliersLinkObjFormatter"
            ],
            [
                "field" => "department",
                "searchable" => true,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('general.department'),
                "visible" => true,
                "formatter" => "departmentsLinkObjFormatter"
            ],
            [
                "field" => "user",
                "searchable" => true,
                "sortable" => true,
                "title" => trans('general.user'),
                "visible" => true,
                "formatter" => "usersLinkObjFormatter"
            ],
            [
                "field" => "created_at",
                "searchable" => true,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('general.created_at'),
                "visible" => true,
                'formatter' => 'dateDisplayFormatter'
            ],
            [
                "field" => "approved_by",
                "searchable" => true,
                "sortable" => true,
                "title" => trans('admin/procurements/table.approved_by'),
                "visible" => true,
                "formatter" => "usersLinkObjFormatter"
            ],
            [
                "field" => "approved_at",
                "searchable" => true,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('admin/procurements/table.approved_at'),
                "visible" => true,
                'formatter' => 'dateDisplayFormatter'
            ],
            [
                "field" => "assigned_by",
                "searchable" => true,
                "sortable" => true,
                "title" => trans('admin/procurements/table.assigned_by'),
                "visible" => true,
                "formatter" => "usersLinkObjFormatter"
            ],
            [
                "field" => "assigned_at",
                "searchable" => true,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('admin/procurements/table.assigned_at'),
                "visible" => true,
                'formatter' => 'dateDisplayFormatter'
            ],
            [
                "field" => "actions",
                "searchable" => false,
                "sortable" => false,
                "switchable" => false,
                "title" => trans('table.actions'),
                "visible" => true,
                "formatter" => "procurementsActionsFormatter",
            ]
        ];

        return json_encode($layout);
    }
}
