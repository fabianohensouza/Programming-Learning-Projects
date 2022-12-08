<?php
use Sisac\Control\Page;
use Sisac\Control\Action;
use Sisac\Database\Criteria;
use Sisac\Widgets\Form\Form;
use Sisac\Widgets\Form\Combo;
use Sisac\Widgets\Form\Entry;
use Sisac\Database\Repository;
use Sisac\Widgets\Form\Hidden;
use Sisac\Widgets\Form\Number;
use Sisac\Database\Transaction;
use Sisac\Widgets\Base\Element;
use Sisac\Widgets\Dialog\Message;
use Sisac\Widgets\Container\Panel;
use Sisac\Widgets\Form\CheckGroup;
use Sisac\Widgets\Form\Month;
use Sisac\Widgets\Wrapper\FormWrapper;

/**
 * Formulário de pessoas
 */
class RelatorioForm extends Page
{
    private $form;
    private $cargos;

    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();
        // instancia um formulário
        $this->form = new FormWrapper(new Form('form_gera_relat'));
        $this->form->setTitle('Gerar Relatório de Acompanhamento');
        
        // cria os campos do formulário
        $periodo      = new Month('periodo');

        for ($i=1; $i <= 8; $i++) { 
            $cooperativa    = new Combo('id_coop');
        }
        
        // carrega as cooperativas do banco de dados
        Transaction::open('Sisac');   

        $avisos = AvisosRelat::all();
        echo '<pre>';
        var_dump($avisos);exit;

        $cooperativas = Cooperativa::all();
        $items = array();
        foreach ($cooperativas as $obj_cooperativa) {
            $items[$obj_cooperativa->id] = $obj_cooperativa->id . " - " . $obj_cooperativa->nome;
        }
        unset( $items['0000']);
        $cooperativa->addItems($items);    
        
        $cargos = Cargo::all();
        $items = array();
        foreach ($cargos as $cargo) {
            $items[$cargo->cod] = $cargo->nome;
        }
        $cod_cargo->addItems($items);

        Transaction::close();
        
        $this->form->addField('', $id, '15%');
        $this->form->addField('Nome', $nome, '50%');
        $this->form->addField('Cooperativa', $cooperativa, '50%');
        $this->form->addField('Cargo', $cod_cargo, '50%');
        $this->form->addField('E-mail', $email, '50%');
        
        $this->form->addAction('Salvar', new Action(array($this, 'onSave')));
                
        // adiciona o formulário na página
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
            $pessoa = new Pessoa; // instancia objeto
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
                $pessoa = Pessoa::find($id);
                $this->form->setData($pessoa); // lança os dados da pessoa no formulário
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
