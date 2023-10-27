<?php


namespace App\Forms;


class Upload
{
    private string $src = '';
    private string $name;
    private string $placeholder = '';

    public static function create(string $name, string  $src = ''): self
    {
        $form = new static();
        $form->name = $name;
        if (!empty($src)) {
            if ($src[0] != '/') $src = '/' . $src;
            $form->src = $src;
        }
        return $form;
    }
    public function placeholder(string $placeholder): self
    {
        $form = clone $this;
        $form->placeholder = $placeholder;
        return $form;

    }
    public function show(bool $inline = false, string $prefix = null)
    {
        $view = ($inline) ? 'forms.upload-inline' : 'forms.upload';
        return view($view, [
            'name' => $this->name,
            'src' => $this->src,
            'id_prefix' => $prefix ?? $this->name,
            'placeholder' => $this->placeholder,
        ]);
    }
}
