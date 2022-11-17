<?php
namespace Sisac\Widgets\Datagrid;

use Sisac\Control\ActionInterface;

/**
 * Representa uma Datagrid
 * @author Pablo Dall'Oglio
 */
class Datagrid
{
    private $columns;
    private $items;
    private $actions;
    protected $name;
    
    /**
     * Instancia o formulário em datagrid
     * @param $name = nome do formulário
     */
    public function __construct($name = '')
    {
        $this->$name = $name;
    }
    
    /**
     * Retorna o nome do formulário em datagrid
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Adiciona uma coluna à datagrid
     * @param $object = objeto do tipo DatagridColumn
     */
    public function addColumn(DatagridColumn $object)
    {
        $this->columns[] = $object;
    }
    
    /**
     * Adiciona uma ação à datagrid
     * @param $label  = rótulo
     * @param $action = ação
     * @param $field  = campo
     * @param $image  = imagem
     */
    public function addAction($label, ActionInterface $action, $field, $image = null)
    {
        $this->actions[] = ['label' => $label, 'action'=> $action, 'field' => $field, 'image' => $image];
    }
    
    /**
     * Adiciona um objeto na grid
     * @param $object = Objeto que contém os dados
     */
    public function addItem($object)
    {
        $this->items[] = $object;
        
        foreach ($this->columns as $column)
        {
            $name = $column->getName();
            if (!isset($object->$name))
            {
                // chama o método de acesso
                $object->$name;
            }
        }
    }
    
    /**
     * Return columns
     */
    public function getColumns()
    {
        return $this->columns;
    }
    
    /**
     * Return items
     */
    public function getItems()
    {
        return $this->items;
    }
    
    /**
     * Return actions
     */
    public function getActions()
    {
        return $this->actions;
    }
    
    /**
     * Limpa os items
     */
    function clear()
    {
        $this->items = [];
    }
}
