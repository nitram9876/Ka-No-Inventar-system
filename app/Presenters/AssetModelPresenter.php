<?php

namespace App\Presenters;

/**
 * Class AssetModelPresenter
 * @package App\Presenters
 */
class AssetModelPresenter extends Presenter
{
    public static function dataTableLayout() {

        $layout = [
            [
                "field" => "checkbox",
                "checkbox" => true
            ],
            [
                "field" => "id",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('general.id'),
                "visible" => false
            ], [
                "field" => "company",
                "searchable" => true,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('admin/companies/table.title'),
                "visible" => false,
                "formatter" => "companiesLinkObjFormatter"
            ], [
                "field" => "name",
                "searchable" => true,
                "sortable" => true,
                "visible" => true,
                "title" => trans('general.name'),
                "formatter" => "modelsLinkFormatter"
            ],
            [
                "field" => "image",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('general.image'),
                "visible" => true,
                "formatter" => 'imageFormatter',
            ],
            [
                "field" => "manufacturer",
                "searchable" => false,
                "sortable" => true,
                "switchable" => false,
                "title" => trans('general.manufacturer'),
                "visible" => false,
                "formatter" => 'manufacturersLinkObjFormatter',
            ],
            [
                "field" => "model_number",
                "searchable" => false,
                "sortable" => true,
                "switchable" => false,
                "title" => trans('admin/models/table.modelnumber'),
                "visible" => false,
            ],
            [
                "field" => "assets_count",
                "searchable" => false,
                "sortable" => true,
                "switchable" => false,
                "title" => trans('admin/models/table.numassets'),
                "visible" => false,
            ],
            [
                "field" => "depreciation",
                "searchable" => false,
                "sortable" => true,
                "switchable" => false,
                "title" => trans('general.depreciation'),
                "visible" => false,
                "formatter" => "depreciationsLinkObjFormatter",
            ],
            [
                "field" => "category",
                "searchable" => false,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('general.category'),
                "visible" => false,
                "formatter" => "categoriesLinkObjFormatter",
            ],
            [
                "field" => "eol",
                "searchable" => false,
                "sortable" => true,
                "switchable" => false,
                "title" => trans('general.eol'),
                "visible" => false,
            ],
            [
                "field" => "fieldset",
                "searchable" => false,
                "sortable" => true,
                "switchable" => false,
                "title" => trans('admin/models/general.fieldset'),
                "visible" => false,
                "formatter" => "fieldsetsLinkObjFormatter",
            ],
            [
                "field" => "notes",
                "searchable" => true,
                "sortable" => true,
                "switchable" => true,
                "title" => trans('general.notes'),
                "visible" => false,
            ],
            [
                "field" => "created_at",
                "searchable" => true,
                "sortable" => true,
                "visible" => false,
                "title" => trans('general.created_at'),
                "formatter" => "dateDisplayFormatter"
            ],
            [
                "field" => "updated_at",
                "searchable" => true,
                "sortable" => true,
                "visible" => false,
                "title" => trans('general.updated_at'),
                "formatter" => "dateDisplayFormatter"
            ],

        ];


        $layout[] = [
            "field" => "actions",
            "searchable" => false,
            "sortable" => false,
            "switchable" => false,
            "title" => trans('table.actions'),
            "formatter" => "modelsActionsFormatter",
        ];


        return json_encode($layout);

    }
    /**
     * Formatted note for this model
     * @return string
     */
    public function note()
    {
        $Parsedown = new \Parsedown();

        if ($this->model->note) {
            return $Parsedown->text($this->model->note);
        }

    }

    public function eolText()
    {
        if ($this->eol) {
            return $this->eol.' '.trans('general.months');
        }
        return '';
    }

    /**
     * Pretty name for this model
     * @return string
     */
    public function modelName()
    {
        $name = '';
        if ($this->model->manufacturer) {
            $name .= $this->model->manufacturer->name.' ';
        }
        $name .= $this->name;

        if ($this->model_number) {
            $name .=" (#".$this->model_number.')';
        }
        return $name;
    }

    /**
     * Standard url for use to view page.
     * @return string
     */
    public function nameUrl()
    {
        return  (string) link_to_route('models.show', $this->name, $this->id);
    }

    /**
     * Generate img tag to this models image.
     * @return string
     */
    public function imageUrl()
    {
        if (!empty($this->image)) {
            return '<img src="' . url('/') . '/uploads/models/' . $this->image . '" alt="'.$this->name.'" height="50" width="50">';
        }
        return '';
    }

    /**
     * Generate img tag to this models image.
     * @return string
     */
    public function imageSrc()
    {
        if (!empty($this->image)) {
            return url('/') . '/uploads/models/' . $this->image;
        }
        return '';
    }

    /**
     * Url to view this item.
     * @return string
     */
    public function viewUrl()
    {
        return route('models.show', $this->id);
    }
}
