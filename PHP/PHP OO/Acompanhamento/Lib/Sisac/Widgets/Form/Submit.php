<?php
namespace Sisac\Widgets\Form;

use Sisac\Widgets\Base\Element;

/**
 * classe Entry
 * classe para construção de caixas de texto
 * @author Pablo Dall'Oglio
 */
class Submit extends Field implements FormElementInterface
{
    protected $properties;
    
    /**
     * Exibe o widget na tela
     */
    public function show()
    {
        // atribui as propriedades da TAG
        $tag = new Element('input');
        $tag->class = 'btn btn-success';	// classe CSS
        $tag->value = $this->name;   // valor da TAG
        $tag->type = 'submit';          // tipo de input
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
