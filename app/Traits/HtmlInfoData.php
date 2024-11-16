<?php

namespace App\Traits;

trait HtmlInfoData
{
    public function htmlNum(): string
    {
        if (is_null($this->number)) return 'б/н';
        if (!is_numeric($this->number)) return $this->number;
        return '№ ' . str_pad((string)$this->number, 6, '0', STR_PAD_LEFT);
    }

    public function htmlDate(string $field = 'created_at'):string
    {
        return $this->$field->translatedFormat('d F Y');
    }

    public function htmlShortDate(string $field = 'created_at'):string
    {
        return $this->$field->translatedFormat('d-m-Y');
    }

    public function htmlNumDate(string $field = 'created_at'):string
    {
        return $this->number . ' от ' . $this->htmlDate($field);
    }

    /*
    public function statusHtml():string
    {
        return self::STATUS[$this->status];
    }*/
}
