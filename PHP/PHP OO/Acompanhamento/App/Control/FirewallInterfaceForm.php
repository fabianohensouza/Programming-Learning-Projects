<?php
use Sisac\Control\Page;
use Sisac\Control\Action;
use Sisac\Widgets\Form\Form;
use Sisac\Widgets\Form\Combo;
use Sisac\Widgets\Form\Entry;
use Sisac\Widgets\Form\Hidden;
use Sisac\Database\Transaction;
use Sisac\Widgets\Dialog\Message;
use Sisac\Widgets\Wrapper\FormWrapper;

/**
 * Formulário de firewalls PAs
 */
class FirewallInterfaceForm extends Page
{
    private $form;     // formulário de buscas

    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_cooperativas'));
        $this->form->setTitle('Cooperativa');

        // cria os campos do formulário
        $id         = new Hidden('id');
        $id_pa         = new Hidden('id_pa');
        $interface  = new Entry('interface');
        $ip         = new Entry('ip');
        $zona       = new Combo('zona');
        $ddns       = new Entry('ddns');

        $interface->setEditable(FALSE);
        $ip->__set('placeholder', 'XXX.XXX.XXX.XXX');
        $ddns->__set('placeholder', 'XXXXYY.ddns.com');

        $items = array( "lan" => "LAN",
                        "wan" => "WAN",
                        "multisicoob" => "MULTISICOOB",
                        "sd-wan" => "SD-WAN",
                        "secnet" => "SECNET",
                        "marketing" => "MARKETING", );
                    
        $zona->addItems($items);
        
        $this->form->addField('', $id, '0%');
        $this->form->addField('', $id_pa, '0%');
        $this->form->addField('Interface', $interface, '20%');
        $this->form->addField('IP', $ip, '20%');
        $this->form->addField('Zona', $zona, '50%');
        $this->form->addField('DDNS', $ddns, '50%');

        $this->form->addAction('Salvar', new Action(array($this, 'onSave')));

        if (isset($_POST['id_pa'])) {
            $action = new Action(array('PasForm', 'onEdit'));
            $action->setParameter('id', $_POST['id_pa']);
            $this->form->addAction('Voltar', $action);
        }

        parent::add($this->form);
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
            $pessoa = new FirewallInterfaces(); // instancia objeto
            $pessoa->fromArray( (array) $dados); // carrega os dados
            $pessoa->store(); // armazena o objeto no banco de dados
            
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
                $firewall_interface = FirewallInterfaces::find($id);//var_dump($firewall_interface);exit;
                $this->form->setData($firewall_interface); // lança os dados da pessoa no formulário

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
