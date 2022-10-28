<?php
namespace Sisac\Widgets\Datagrid;

use Sisac\Control\ActionInterface;

/**
 * Representa uma Datagrid
 * @author Pablo Dall'Oglio
 */
class DataForm
{
    private $columns;
    private $items;
    private $actions;
    private $submit;
    protected $name;
    protected $id;
    private $full_url;
    
    /**
     * Instancia o formulário em datagrid
     * @param $name = nome do formulário
     */
    public function __construct($name = 'my_form')
    {
        $this->setName($name);
    }
    
    /**
     * Define o nome do formulário
     * @param $name = nome do formulário
     */
    public function setName($name)
    {
        $this->name = $name;
    }
    
    /**
     * Retorna o nome do formulário
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Define o id do formulário
     * @param $id = id do formulário
     */
    public function setId($id)
    {
        $this->id = $id;
    }
    
    /**
     * Retorna o nome do formulário
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Define URL da action do formulário
     * @param $url = action do formulário
     */
    public function setFullURL($url)
    {
        $this->full_url = $_SERVER['SCRIPT_NAME'] . $url;
    }
    
    /**
     * Retorna action do formulário
     */
    public function getFullURL()
    {
        return $this->full_url;
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
     * Adiciona uma ação
     * @param $label  Action Label
     * @param $action TAction Object
     */
    public function addSubmit($label, ActionInterface $action)
    {
        $this->submit[$label] = $action;
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
     * Retorna as ações
     */
    public function getSubmit()
    {
        return $this->submit;
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
     * Configura titulo da Datagrig
     */
    public function setTitle(String $title = '')
    {
        $this->title = $title;
    }
    
    /**
     * Configura bordas da Datagrig
     */
    public function setBorder(Bool $bordered = FALSE)
    {
        $this->bordered = ($bordered) ? 'table-bordered' : '';
    }
    
    /**
     * Limpa os items
     */
    function clear()
    {
        $this->items = [];
    }
}
