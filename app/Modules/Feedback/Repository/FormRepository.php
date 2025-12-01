<?php

namespace App\Modules\Feedback\Repository;

use App\Modules\Feedback\Entity\FormBack;
use Illuminate\Http\Request;

class FormRepository
{

    public function getIndex(Request $request, &$filters)
    {
        $query = FormBack::orderByDesc('created_at');
        $filters = [];

        $this->_date_from($request, $filters, $query);
        $this->_date_to($request, $filters, $query);

        if ($request->integer('widget') > 0) {
            $widget = $request->integer('widget');
            $filters['widget'] = $widget;
            $query->where('widget_id',$widget);

        }
        if (count($filters) > 0) $filters['count'] = count($filters);
        return $query->paginate($request->input('size', 20))
            ->withQueryString()
            ->through(fn(FormBack $form) => $this->FormToArray($form));
    }

    private function FormToArray(FormBack $form): array
    {
        return array_merge($form->toArray(), [
            'widget' => $form->widget->name,
            'lead' => $form->lead->getStatusName(),
            'data' => $this->filterDataField($form->data_form),
        ]);
    }


    private function _date_from(Request $request, &$filters, &$query): void
    {
        if (!is_null($begin = $request->date('date_from'))) {
            $filters['date_from'] = $begin->format('Y-m-d');
            $query->where('created_at', '>', $begin);
        }
    }

    private function _date_to(Request $request, &$filters, &$query): void
    {
        if (!is_null($end = $request->date('date_to'))) {
            $filters['date_to'] = $end->format('Y-m-d');
            $query->where('created_at', '<=', $end);
        }
    }

    private function filterDataField(array $data_form): array
    {
        return array_filter($data_form, function ($key) {
            return $key != 'id' && $key != 'url';
        }, ARRAY_FILTER_USE_KEY);
    }
}
