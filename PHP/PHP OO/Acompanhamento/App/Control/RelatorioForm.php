<?php
use Sisac\Control\Page;
use Sisac\Control\Action;
use Sisac\Database\Criteria;
use Sisac\Widgets\Form\Form;
use Sisac\Widgets\Form\Combo;
use Sisac\Widgets\Form\Entry;
use Sisac\Widgets\Form\Label;
use Sisac\Widgets\Form\Month;
use Sisac\Database\Repository;
use Sisac\Widgets\Form\Hidden;
use Sisac\Widgets\Form\Number;
use Sisac\Database\Transaction;
use Sisac\Session\Session;
use Sisac\Widgets\Base\Element;
use Sisac\Widgets\Dialog\Message;
use Sisac\Widgets\Container\Panel;
use Sisac\Widgets\Dialog\Question;
use Sisac\Widgets\Form\CheckGroup;
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
        
        // carrega as informações do banco de dados
        Transaction::open('Sisac');
        
        // cria os campos do formulário
        $periodo      = new Month('periodo');
        $this->form->addField('Período', $periodo, '30%');  

        $cooperativa = new Combo('cooperativa');

        $cooperativas = Cooperativa::all();
        $items = array( 0 => '*** Todas Cooperativas ***');
        foreach ($cooperativas as $obj_cooperativa) {
            $items[$obj_cooperativa->id] = $obj_cooperativa->id . " - " . $obj_cooperativa->nome;
        }
        unset( $items['0000']);
        $cooperativa->addItems($items); 
        
        $this->form->addField('Cooperativa', $cooperativa, '30%');

        $aviso = new Hidden('aviso');
        $this->form->addField('Avisos:', $aviso, '50%');

        for ($i=1; $i <= 8; $i++) {
            $av = "av{$i}";

            $$av = new Entry($av); 
            $this->form->addField("$i", $$av, '50%');;
        }    

        Transaction::close();
        
        $this->form->addAction('Gerar', new Action(array($this, 'onSave')));
                
        // adiciona o formulário na página
        parent::add($this->form);
    }
    
    /**
     * Pergunta sobre a exclusão de registro
     */
    function onSave()
    {

        $dados = $this->form->getData();
        $this->form->setData($dados);

        $session = new Session();
        $session->setValue('dados', $dados); 

        $action1 = new Action(array($this, 'Save'));
        
        new Question('Esta operação irá sobescrever o relatória da Cooperativa seleccionada. Deseja prosseguir?', $action1);
    }

    /**
     * Salva os dados do formulário
     */
    public function Save()
    {
        try
        {
            $session = new Session();
            $dados = $session->getValue('dados');
            $session->freeSession();

            // inicia transação com o BD
            Transaction::open('Sisac');

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
            Transaction::open('Sisac'); // inicia transação com o BD
            $aviso = AvisosRelat::all();
            $this->form->setData($aviso); // lança os dados da pessoa no formulário
            Transaction::close(); // finaliza a transação
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
