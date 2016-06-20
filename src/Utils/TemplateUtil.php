<?php

namespace Jiyis\Generator\Utils;

class TemplateUtil
{
    public static function getTemplate($templateName, $templateType)
    {
        $templateName = str_replace('.', '/', $templateName);

        $templatesPath = config(
            'jiyis.laravel_generator.path.templates_dir',
            base_path('resources/jiyis/jiyis-generator-templates/')
        );

        $path = $templatesPath.$templateName.'.stub';

        if (file_exists($path)) {
            return file_get_contents($path);
        }

        $path = base_path('vendor/jiyislabs/'.$templateType.'/templates/'.$templateName.'.stub');

        return file_get_contents($path);
    }

    public static function fillTemplate($variables, $template)
    {
        foreach ($variables as $variable => $value) {
            $template = str_replace($variable, $value, $template);
        }

        return $template;
    }

    public static function fillFieldTemplate($variables, $template, $field)
    {
        foreach ($variables as $variable => $key) {
            $template = str_replace($variable, $field[$key], $template);
        }

        return $template;
    }

    public static function fillTemplateWithFieldData($variables, $fieldVariables, $template, $field)
    {
        $template = self::fillTemplate($variables, $template);

        return self::fillFieldTemplate($fieldVariables, $template, $field);
    }
}
