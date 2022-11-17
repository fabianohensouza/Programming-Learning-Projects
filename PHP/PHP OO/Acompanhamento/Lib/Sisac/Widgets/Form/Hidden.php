<?php
namespace Sisac\Widgets\Form;

use Sisac\Widgets\Base\Element;

/**
 * Representa um campo escondido
 * @author Pablo Dall'Oglio
 */
class Hidden extends Field implements FormElementInterface
{
    private $items; // array contendo os itens da combo
    protected $properties;
    
    /**
     * Adiciona items à combo box
     * @param $items = array de itens
     */
    public function addItems($items)
    {
        $this->items = $items;
    }
    
    /**
     * Exibe o widget na tela
     */
    public function show()
    {
        // atribui as propriedades da TAG
        $tag = new Element('input');
        $tag->class = 'field';		  // classe CSS
        $tag->name = $this->name;     // nome da TAG
        $tag->value = $this->value;   // valor da TAG
        $tag->type = 'hidden';        // tipo de input
        $tag->style = "width:{$this->size}"; // tamanho em pixels
        
        if ($this->properties)
        {
            foreach ($this->properties as $property => $value)
            {
                $tag->$property = $value;
            }
        }
        
        // exibe a tag
        $tag->show();
    }
}
