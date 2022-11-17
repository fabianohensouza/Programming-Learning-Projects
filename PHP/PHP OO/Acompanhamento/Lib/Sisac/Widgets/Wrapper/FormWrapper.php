<?php
namespace Sisac\Widgets\Wrapper;

use Sisac\Widgets\Container\Panel;
use Sisac\Widgets\Form\Form;
use Sisac\Widgets\Form\Button;
use Sisac\Widgets\Base\Element;

/**
 * Decora formulários no formato Bootstrap
 */
class FormWrapper
{
    private $decorated;
    
    /**
     * Constrói o decorator
     */
    public function __construct(Form $form)
    {
        $this->decorated = $form;
    }
    
    /**
     * Redireciona chamadas para o objeto decorado
     */
    public function __call($method, $parameters)
    {
        return call_user_func_array(array($this->decorated, $method),$parameters);
    }
    
    /**
     * Exibe o formulário
     */
    public function show()
    {
        $element = new Element('form');
        $element->class = "form-horizontal";
        $element->enctype = "multipart/form-data";
        $element->method  = 'post';    // método de transferência
        $element->name  = $this->decorated->getName();
        $element->width = '100%';
        
        foreach ($this->decorated->getFields() as $field)
        {

            if($field->getLabel() != 'hr') {
                $group = new Element('div');
                $group->class = 'form-group';
                
                $label = new Element('label');
                $label->class= 'col-sm-2 control-label';
                $label->add($field->getLabel());
                
                $col = new Element('div');
                $col->class = 'col-sm-10';
                $col->add($field);
                $field->class = 'form-control';
                
                $group->add($label);
                $group->add($col);
            } else {
                $group = new Element('hr');
            }
            
            $element->add($group);
        }
        
        $group = new Element('div');
        
        $i = 0;
        foreach ($this->decorated->getActions() as $label => $action)
        {
            $name   = strtolower(str_replace(' ', '_', $label));
            $button = new Button($name);
            $button->setFormName($this->decorated->getName());
            $button->setAction($action, $label);
            $button->class = 'btn ' . ( ($i==0) ? 'btn-success' : 'btn-default');
            
            $group->add($button);
            $i ++;
        }
        
        $panel = new Panel($this->decorated->getTitle());
        $panel->add($element);
        $panel->addFooter($group);
        $panel->show();
    }
}
