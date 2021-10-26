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
            // [
            //     "field" => "model",
            //     "searchable" => true,
            //     "sortable" => true,
            //     "title" => trans('general.asset_model'),
            //     "visible" => true,
            //     "formatter" => "modelsLinkObjFormatter"
            // ],
            // [
            //     "field" => "asset",
            //     "searchable" => true,
            //     "sortable" => true,
            //     "title" => trans('general.asset'),
            //     "visible" => true,
            //     "formatter" => "hardwareLinkFormatter"
            // ],
            [
                "field" => "supplier",
                "searchable" => true,
                "sortable" => true,
                "title" => trans('general.supplier'),
                "visible" => true,
                "formatter" => "suppliersLinkObjFormatter"
            ],
            // [
            //     "field" => "qty",
            //     "searchable" => false,
            //     "sortable" => false,
            //     "title" => trans('general.quantity'),
            //     "visible" => true
            // ],
            // [
            //     "field" => "purchase_cost",
            //     "searchable" => true,
            //     "sortable" => true,
            //     "title" => trans('general.purchase_cost'),
            //     "visible" => true,
            //     "footerFormatter" => "sumFormatter"
            // ],
            // [
            //     "field" => "location",
            //     "searchable" => false,
            //     "sortable" => true,
            //     "switchable" => true,
            //     "title" => trans('general.location'),
            //     "visible" => true,
            //     "formatter" => "locationsLinkObjFormatter"
            // ],
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



    // /**
    //  * Link to this locations name
    //  * @return string
    //  */
    // public function nameUrl()
    // {
    //     return (string)link_to_route('locations.show', $this->name, $this->id);
    // }

    // /**
    //  * Getter for Polymorphism.
    //  * @return mixed
    //  */
    // public function name()
    // {
    //     return $this->model->name;
    // }

    // /**
    //  * Url to view this item.
    //  * @return string
    //  */
    // public function viewUrl()
    // {
    //     return route('locations.show', $this->id);
    // }

    // public function glyph()
    // {
    //     return '<i class="fa fa-map-marker" aria-hidden="true"></i>';
    // }
    
    // public function fullName() {
    //     return $this->name;
    // }
}
