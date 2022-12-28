<?php
use Sisac\Control\Page;
use Sisac\Control\Action;
use Sisac\Database\Criteria;
use Sisac\Database\Repository;
use Sisac\Database\Transaction;
use Sisac\Session\Session;
use Sisac\Widgets\Dialog\Message;
use Sisac\Widgets\Dialog\Question;
use Sisac\Widgets\Form\Form;
use Sisac\Widgets\Form\Combo;
use Sisac\Widgets\Form\Entry;
use Sisac\Widgets\Form\Month;
use Sisac\Widgets\Form\Hidden;
use Sisac\Widgets\Wrapper\FormWrapper;

/**
 * Formulário de pessoas
 */
class RelatorioForm extends Page
{
    private $form;
    private $id_coops = [];

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

        $repository = new Repository('Cooperativa');
        $criteria = new Criteria();
        $criteria->add('ic', '=', 'Sim');
        $cooperativas = $repository->load($criteria);
        $items = array( 0 => '*** Todas Cooperativas ***');

        foreach ($cooperativas as $obj_cooperativa) {
            $items[$obj_cooperativa->id] = $obj_cooperativa->id . " - " . $obj_cooperativa->nome;
            $this->id_coops[] = $obj_cooperativa->id;
        }

        $cooperativa->addItems($items); 
        
        $this->form->addField('Cooperativa', $cooperativa, '30%');

        $aviso = new Hidden('aviso');
        $this->form->addField('Avisos:', $aviso, '50%');
        $avisos_relat = AvisosRelat::find(1);

        for ($i=1; $i <= 8; $i++) {
            $av = "av{$i}";

            $$av = new Entry($av);
            $$av->setValue($avisos_relat->$av);
            $this->form->addField("$i", $$av, '50%');
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
        try
        {
            $dados = $this->form->getData();
            $this->form->setData($dados);

            if ($dados->periodo == '') {
                throw new Exception("Favor informar o período.", 1);
            }
            
            $session = new Session();
            $session->setValue('dados', $dados); 

            $action1 = new Action(array($this, 'Save'));
            
            new Question('Esta operação irá sobescrever o relatória da Cooperativa seleccionada. Deseja prosseguir?', $action1);
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
     * Salva os dados do formulário
     */
    public function Save()
    {
        try
        {
            $session = new Session();
            $dados = $session->getValue('dados');
            $this->form->setData($dados);

            $avisos = ['id' => 1];
            for ($i=1; $i <= 8; $i++) {
                $av = "av{$i}";                
                $avisos[$av] = $dados->$av;
            }    

            // inicia transação com o BD
            Transaction::open('Sisac');
 
            $avisos_relat = new AvisosRelat(); // instancia objeto
            $avisos_relat->fromArray($avisos); // carrega os dados
            $avisos_relat->store(); // armazena o objeto no banco de dados

            //gera dados das cooperativas
            $coops = ($dados->cooperativa == '0')? $this->id_coops : array($dados->cooperativa);
            $relatorio = new GeraRelatorio($coops, $avisos, $dados->periodo);
            
            Transaction::close(); // finaliza a transação
            new Message('info', 'Relatório gerado com sucesso!');
            
            $session->freeSession();
        }
        catch (Exception $e)
        {
            // exibe a mensagem gerada pela exceção
            new Message('error', $e->getMessage());

            // desfaz todas alterações no banco de dados
            Transaction::rollback();
        }
    }
    
    
}
