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
class FirewallForm extends Page
{
    private $form;     // formulário de buscas
    private $datagrid; // listagem

    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();

        if(isset($_GET['id']) || $_POST['id_coop'] != 0) {

            // instancia um formulário
            $this->form = new FormWrapper(new Form('form_firewall_pas'));
            $this->form->setTitle('Equipamento de Firewall');
            
            // cria os campos do formulário
            $id    = new Hidden('id');
            $id_coop      = new Entry('id_coop');
            $id_pa    = new Combo('id_pa');
            $id_firewall_modelo    = new Combo('id_firewall_modelo');
            $serial      = new Entry('serial');
            
            Transaction::open('Sisac');  
            
            if (isset($_GET['id'])) {
                $coop = FirewallPa::find($_GET['id'])->id_coop;
            } else {
                $coop = $_POST['id_coop'];
            }

            $id_coop->setValue($coop);
            $id_coop->setEditable(FALSE);

            $repository = new Repository('Pa');
            $criteria = new Criteria;
            $criteria->add('id_coop', '=', $coop);
            $criteria->add('id_coop', '=', '0000', 'or');
            $criteria->setProperty('order', 'id');
            $pas = $repository->load($criteria);
            $items = array();
            foreach ($pas as $obj_pa)
            {
                $items[$obj_pa->id] = $obj_pa->numero;
            }
            
            $id_pa->addItems($items);

            $firewalls = FirewallModelo::all();
            $items = array();
            foreach ($firewalls as $obj_firewall) {
                $items[$obj_firewall->id] = $obj_firewall->nome;
            }
            $id_firewall_modelo->addItems($items); 

            $this->form->addField('', $id, '0%');
            $this->form->addField('Cooperativa', $id_coop, '40%');
            $this->form->addField('PA', $id_pa, '40%');
            $this->form->addField('Modelo Firewall', $id_firewall_modelo, '40%');
            $this->form->addField('Serial', $serial, '40%');
            
            $this->form->addAction('Salvar', new Action(array($this, 'onSave')));

            parent::add($this->form);   

            Transaction::close();

        } else {

            ob_start();
            new Message('error', 'Favor selecionar a Cooperativa na tela anterior');
            $message = ob_get_contents();
            ob_clean();
            parent::add($message); 
        }
    }

    /**
     * Salva os dados do formulário
     */
    public function onSave()
    {
        try
        {
            // inicia transação com o BD
            Transaction::open('Sisac');

            $dados = $this->form->getData();

            $this->form->setData($dados); 

            $pa = new FirewallPa; // instancia objeto
            $pa->fromArray( (array) $dados); // carrega os dados
            $pa->store(); // armazena o objeto no banco de dados
            
            Transaction::close(); // finaliza a transação
            new Message('info', 'Dados armazenados com sucesso');
        }
        catch (Exception $e)
        {
            // exibe a mensagem gerada pela exceção
            new Message('error', $e->getMessage());

            // desfaz todas alterações no banco de dados
            Transaction::rollback();
        }
    }
    
    /**
     * Carrega registro para edição
     */
    public function onEdit($param)
    {
        try
        {
            if (isset($param['id']))
            {
                $id = $param['id']; // obtém a chave
                Transaction::open('Sisac'); // inicia transação com o BD
                $pa = FirewallPa::find($id);

                $this->form->setData($pa); // lança os dados da pessoa no formulário

                Transaction::close(); // finaliza a transação
            }
        }
        catch (Exception $e)		    // em caso de exceção
        {
            // exibe a mensagem gerada pela exceção
            new Message('error', $e->getMessage());
            // desfaz todas alterações no banco de dados
            Transaction::rollback();
        }
    }
}
