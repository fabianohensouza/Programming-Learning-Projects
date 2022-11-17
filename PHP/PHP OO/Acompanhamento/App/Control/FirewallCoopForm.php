<?php
use Sisac\Control\Page;
use Sisac\Control\Action;
use Sisac\Database\Criteria;
use Sisac\Widgets\Form\Form;
use Sisac\Widgets\Form\Combo;
use Sisac\Widgets\Form\Entry;
use Sisac\Database\Repository;
use Sisac\Widgets\Form\Hidden;
use Sisac\Database\Transaction;
use Sisac\Widgets\Dialog\Message;
use Sisac\Widgets\Wrapper\FormWrapper;

/**
 * Formulário de cooperativas
 */
class FirewallCoopForm extends Page
{
    private $form;     // formulário de buscas

    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_firewall_coop'));
        $this->form->setTitle('Selecione a Cooperativa');
        
        // cria os campos do formulário
        $coop    = new Combo('id_coop');
        
        // carrega as cidades do banco de dados
        Transaction::open('Sisac');    

        $repository = new Repository('Cooperativa');
        $criteria = new Criteria;
        $criteria->add('id', '!=', '0000');
        $criteria->setProperty('order', 'id');
        $lista_coop = $repository->load($criteria);
        $items = array();
        foreach ($lista_coop as $obj_cooperativas)
        {
            $items[$obj_cooperativas->id] = $obj_cooperativas->id . " - " . $obj_cooperativas->nome;
        }
        
        $coop->addItems($items);

        $this->form->addField('', $coop, '30%');
        
        $this->form->addAction('Avançar', new Action(array("FirewallForm", 'onEdit')));

        parent::add($this->form);     

        Transaction::close();
    }

}
