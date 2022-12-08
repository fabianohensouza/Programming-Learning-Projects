<?php
namespace Sisac\Widgets\Form;

use Sisac\Widgets\Base\Element;

/**
 * classe Month
 * classe para construção de caixas de texto
 * @author Fabiano Souza
 */
class Month extends Entry implements FormElementInterface
{
    /**
     * Exibe o widget na tela
     */
    public function show()
    {
        $tag = new Element('input');
        $tag->class = 'field';		  // classe CSS
        $tag->name = $this->name;     // nome da TAG
        $tag->value = $this->value;   // valor da TAG
        $tag->type = 'month';          // tipo de input
        $tag->style = "width:{$this->size}"; // tamanho em pixels
        
        // se o campo não é editável
        if (!parent::getEditable())
        {
            $tag->readonly = "1";
        }
        
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

