<?php

namespace Jiyis\Generator\Common;

use Illuminate\Support\Str;

class GeneratorConfig
{
    /* Namespace variables */
    public $nsApp;
    public $nsRepository;
    public $nsModel;
    public $nsDataTables;
    public $nsModelExtend;

    public $nsApiController;
    public $nsApiRequest;

    public $nsRequest;
    public $nsRequestBase;
    public $nsController;
    public $nsBaseController;

    /* Path variables */
    public $pathRepository;
    public $pathModel;
    public $pathDataTables;

    public $pathApiController;
    public $pathApiRequest;
    public $pathApiRoutes;
    public $pathApiTests;
    public $pathApiTestTraits;

    public $pathController;
    public $pathRequest;
    public $pathRoutes;
    public $pathViews;

    /* Model Names */
    public $mName;
    public $mPlural;
    public $mCamel;
    public $mCamelPlural;
    public $mSnake;
    public $mSnakePlural;

    public $forceMigrate;

    /* Generator Options */
    public $options;

    /* Prefixes */
    public $prefixes;

    /* Command Options */
    public static $availableOptions = ['fieldsFile', 'jsonFromGUI', 'tableName', 'fromTable', 'save', 'primary', 'prefix', 'paginate', 'skip'];

    public $tableName;

    /* Generator AddOns */
    public $addOns;

    public function init(CommandData &$commandData)
    {
        $this->mName = $commandData->modelName;

        $this->prepareOptions($commandData);
        $this->preparePrefixes();
        $this->prepareAddOns();
        $this->prepareModelNames();
        $this->loadNamespaces($commandData);
        $this->loadPaths();
        $commandData = $this->loadDynamicVariables($commandData);
    }

    public function loadNamespaces(CommandData &$commandData)
    {
        if ($this->getOption('prefix')) {
            $prefix = '\\' . $this->prefixes['ns'];
        } else {
            $prefix = '';
        }

        $this->nsApp = $commandData->commandObj->getLaravel()->getNamespace();
        $this->nsRepository = config('jiyis.laravel_generator.namespace.repository', 'App\Repositories').$prefix;
        $this->nsModel = config('jiyis.laravel_generator.namespace.model', 'App\Models').$prefix;
        $this->nsDataTables = config('jiyis.laravel_generator.namespace.datatables', 'App\DataTables').$prefix;
        $this->nsModelExtend = config(
            'jiyis.laravel_generator.model_extend_class',
            'Illuminate\Database\Eloquent\Model'
        );

        $this->nsApiController = config(
            'jiyis.laravel_generator.namespace.api_controller',
            'App\Http\Controllers\API'
        ).$prefix;
        $this->nsApiRequest = config('jiyis.laravel_generator.namespace.api_request', 'App\Http\Requests\API').$prefix;

        $this->nsRequest = config('jiyis.laravel_generator.namespace.request', 'App\Http\Requests').$prefix;
        $this->nsRequestBase = config('jiyis.laravel_generator.namespace.request', 'App\Http\Requests');
        $this->nsBaseController = config('jiyis.laravel_generator.namespace.controller', 'App\Http\Controllers');
        $this->nsController = config('jiyis.laravel_generator.namespace.controller', 'App\Http\Controllers').$prefix;
    }

    public function loadPaths()
    {
        $prefix = $this->prefixes['path'];

        $this->pathRepository = config(
            'jiyis.laravel_generator.path.repository',
            app_path('Repositories/')
        ).$prefix;

        $this->pathModel = config('jiyis.laravel_generator.path.model', app_path('Models/')).$prefix;

        $this->pathDataTables = config('jiyis.laravel_generator.path.datatables', app_path('DataTables/')).$prefix;

        $this->pathApiController = config(
            'jiyis.laravel_generator.path.api_controller',
            app_path('Http/Controllers/API/')
        ).$prefix;

        $this->pathApiRequest = config(
            'jiyis.laravel_generator.path.api_request',
            app_path('Http/Requests/API/')
        ).$prefix;

        $this->pathApiRoutes = config('jiyis.laravel_generator.path.api_routes', app_path('Http/api_routes.php'));

        $this->pathApiTests = config('jiyis.laravel_generator.path.api_test', base_path('tests/'));

        $this->pathApiTestTraits = config('jiyis.laravel_generator.path.test_trait', base_path('tests/traits/'));

        $this->pathController = config(
            'jiyis.laravel_generator.path.controller',
            app_path('Http/Controllers/')
        ).$prefix;

        $this->pathRequest = config('jiyis.laravel_generator.path.request', app_path('Http/Requests/')).$prefix;

        $this->pathRoutes = config('jiyis.laravel_generator.path.routes', app_path('Http/routes.php'));

        $this->pathViews = config(
            'jiyis.laravel_generator.path.views',
            base_path('resources/views/')
        ).str_replace('.', '/', $this->prefixes['view']).$this->mCamelPlural.'/';
    }

    public function loadDynamicVariables(CommandData &$commandData)
    {
        $commandData->addDynamicVariable('$NAMESPACE_APP$', $this->nsApp);
        $commandData->addDynamicVariable('$NAMESPACE_REPOSITORY$', $this->nsRepository);
        $commandData->addDynamicVariable('$NAMESPACE_MODEL$', $this->nsModel);
        $commandData->addDynamicVariable('$NAMESPACE_DATATABLES$', $this->nsDataTables);
        $commandData->addDynamicVariable('$NAMESPACE_MODEL_EXTEND$', $this->nsModelExtend);

        $commandData->addDynamicVariable('$NAMESPACE_API_CONTROLLER$', $this->nsApiController);
        $commandData->addDynamicVariable('$NAMESPACE_API_REQUEST$', $this->nsApiRequest);

        $commandData->addDynamicVariable('$NAMESPACE_BASE_CONTROLLER$', $this->nsBaseController);
        $commandData->addDynamicVariable('$NAMESPACE_CONTROLLER$', $this->nsController);
        $commandData->addDynamicVariable('$NAMESPACE_REQUEST$', $this->nsRequest);
        $commandData->addDynamicVariable('$NAMESPACE_REQUEST_BASE$', $this->nsRequestBase);

        $this->prepareTableName();

        $commandData->addDynamicVariable('$TABLE_NAME$', $this->tableName);

        $commandData->addDynamicVariable('$MODEL_NAME$', $this->mName);
        $commandData->addDynamicVariable('$MODEL_NAME_CAMEL$', $this->mCamel);
        $commandData->addDynamicVariable('$MODEL_NAME_PLURAL$', $this->mPlural);
        $commandData->addDynamicVariable('$MODEL_NAME_PLURAL_CAMEL$', $this->mCamelPlural);
        $commandData->addDynamicVariable('$MODEL_NAME_SNAKE$', $this->mSnake);
        $commandData->addDynamicVariable('$MODEL_NAME_PLURAL_SNAKE$', $this->mSnakePlural);

        $commandData->addDynamicVariable('$ROUTE_PREFIX$', $this->prefixes['route']);
        $commandData->addDynamicVariable('$PATH_PREFIX$', $this->prefixes['path']);
        $commandData->addDynamicVariable('$VIEW_PREFIX$', $this->prefixes['view']);
        $commandData->addDynamicVariable('$PUBLIC_PREFIX$', $this->prefixes['public']);

        $commandData->addDynamicVariable(
            '$API_PREFIX$',
            config('jiyis.laravel_generator.api_prefix', 'api')
        );

        $commandData->addDynamicVariable(
            '$API_VERSION$',
            config('jiyis.laravel_generator.api_version', 'v1')
        );

        return $commandData;
    }

    public function prepareTableName()
    {
        if ($this->getOption('tableName')) {
            $this->tableName = $this->getOption('tableName');
        } else {
            $this->tableName = $this->mSnakePlural;
        }
    }

    public function prepareModelNames()
    {
        $this->mPlural = Str::plural($this->mName);
        $this->mCamel = Str::camel($this->mName);
        $this->mCamelPlural = Str::camel($this->mPlural);
        $this->mSnake = Str::snake($this->mName);
        $this->mSnakePlural = Str::snake($this->mPlural);
    }

    public function prepareOptions(CommandData &$commandData, $options = null)
    {
        if (empty($options)) {
            $options = self::$availableOptions;
        }

        foreach ($options as $option) {
            $this->options[$option] = $commandData->commandObj->option($option);
        }

        if (isset($options['fromTable']) and $this->options['fromTable']) {
            if (!$this->options['tableName']) {
                $commandData->commandError('tableName required with fromTable option.');
                exit;
            }
        }

        $this->options['softDelete'] = config('jiyis.laravel_generator.options.softDelete', false);
        if (!empty($this->options['skip'])) {
            $this->options['skip'] = array_map('trim', explode(",", $this->options['skip']));
        }
    }

    public function preparePrefixes()
    {
        $this->prefixes['route'] = config('jiyis.laravel_generator.prefixes.route', '');
        $this->prefixes['path'] = config('jiyis.laravel_generator.prefixes.path', '');
        $this->prefixes['view'] = config('jiyis.laravel_generator.prefixes.view', '');
        $this->prefixes['public'] = config('jiyis.laravel_generator.prefixes.public', '');

        if ($this->getOption('prefix')) {

            $multiplePrefixes = explode(",", $this->getOption('prefix'));

            if (!empty($this->prefixes['route'])) {
                $this->prefixes['route'] .= '/';
            }

            foreach ($multiplePrefixes as $prefix) {
                $this->prefixes['route'] .= $prefix.'/';
            }

            if (!empty($this->prefixes['path'])) {
                $this->prefixes['path'] .= '/';
            }

            foreach ($multiplePrefixes as $prefix) {
                $this->prefixes['path'] .= Str::title($prefix).'/';
            }

            $this->prefixes['ns'] = str_replace('/', '\\', $this->prefixes['path']);
            $this->prefixes['ns'] = substr($this->prefixes['ns'], 0, strlen($this->prefixes['ns'])-1);

            if (!empty($this->prefixes['view'])) {
                $this->prefixes['view'] .= '.';
            }

            foreach ($multiplePrefixes as $prefix) {
                $this->prefixes['view'] .= $prefix.'.';
            }

            if (!empty($this->prefixes['route'])) {
                $this->prefixes['public'] .= '/';
            }

            foreach ($multiplePrefixes as $prefix) {
                $this->prefixes['public'] .= $prefix.'/';
            }
        }
    }

    public function overrideOptionsFromJsonFile($jsonData)
    {
        $options = self::$availableOptions;

        foreach ($options as $option) {
            if (isset($jsonData['options'][$option])) {
                $this->setOption($option, $jsonData['options'][$option]);
            }
        }

        $addOns = ['swagger', 'tests', 'datatables'];

        foreach ($addOns as $addOn) {
            if (isset($jsonData['addOns'][$addOn])) {
                $this->addOns[$addOn] = $jsonData['addOns'][$addOn];
            }
        }
    }

    public function getOption($option)
    {
        if (isset($this->options[$option])) {
            return $this->options[$option];
        }

        return false;
    }

    public function getAddOn($addOn)
    {
        if (isset($this->addOns[$addOn])) {
            return $this->addOns[$addOn];
        }

        return false;
    }

    public function setOption($option, $value)
    {
        $this->options[$option] = $value;
    }

    public function prepareAddOns()
    {
        $this->addOns['swagger'] = config('jiyis.laravel_generator.add_on.swagger', false);
        $this->addOns['tests'] = config('jiyis.laravel_generator.add_on.tests', false);
        $this->addOns['datatables'] = config('jiyis.laravel_generator.add_on.datatables', false);
        $this->addOns['menu.enabled'] = config('jiyis.laravel_generator.add_on.menu.enabled', false);
        $this->addOns['menu.menu_file'] = config('jiyis.laravel_generator.add_on.menu.menu_file', 'layouts.menu');
    }
}
