<?php

namespace Model;

class Proyecto extends ActiveRecord
{
    protected static $tabla = 'proyectos';
    protected static  $columnasDB = ['id', 'proyecto', 'url', 'propietarioid'];

    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->proyecto = $args['proyecto'] ?? '';
        $this->url = $args['url'] ?? '';
        $this->propietarioid = $args['propietarioid'] ?? '';
    }

    public function validarProyecto()
    {
        if (!$this->proyecto) {
            self::$alertas['error'][] = 'el nombre del proyecto es obligatorio';
        }
        return self::$alertas;
    }
}
