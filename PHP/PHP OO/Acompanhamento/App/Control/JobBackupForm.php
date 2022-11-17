<?php
use Sisac\Control\Page;
use Sisac\Control\Action;
use Sisac\Database\Criteria;
use Sisac\Widgets\Form\Date;
use Sisac\Widgets\Form\Form;
use Sisac\Widgets\Form\Text;
use Sisac\Widgets\Form\Combo;
use Sisac\Widgets\Form\Entry;
use Sisac\Database\Repository;
use Sisac\Widgets\Form\Hidden;
use Sisac\Database\Transaction;
use Sisac\Widgets\Dialog\Message;
use Sisac\Widgets\Form\CheckGroup;
use Sisac\Widgets\Form\Time;
use Sisac\Widgets\Wrapper\FormWrapper;
/**
 * Formulário de cooperativas
 */
class JobBackupForm extends Page
{
    private $form;     // formulário de buscas

    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();

        if(isset($_GET['id']) || isset($_GET['id_backup'])) {
            
            Transaction::open('Sisac');

            //Carregando dados da Cooperativa
            if (isset($_GET['id_backup'])) {
                $this->bkpcoop = Backup::find($_GET['id_backup']);
            } else {
                $this->job = JobBackup::find($_GET['id']);
                $this->bkpcoop = Backup::find($this->job->id_backup);
            }

            // instancia um formulário
            $this->form = new FormWrapper(new Form('form_job_backup'));
            $this->form->setTitle("Job Backup");
            
            // cria os campos do formulário
            $id         = new Entry('id');
            $id_coop    = new Entry('id_coop');
            $id_backup  = new Entry('id_backup');
            $nome       = new Entry('nome');
            $execucao   = new CheckGroup('execucao'); 
            $retencao   = new CheckGroup('retencao'); 
            $horario    = new Time('horario');  
            $descricao  = new Text('descricao');
                               
            $id_coop->setValue($this->bkpcoop->id_coop);  
            $id_backup->setValue($this->bkpcoop->id);   
                              
            $id->setEditable(FALSE); 
            $id_coop->setEditable(FALSE);
            $id_backup->setEditable(FALSE);

            $execucao->setLayout('horizontal');
            $retencao->setLayout('horizontal');

            $dias = DiaSemana::all();            
            $items = array();
            foreach ($dias as $obj_dia) {
                $items[$obj_dia->id] = $obj_dia->nome;
            }
            $execucao->addItems($items);

            $retencoes = RetencaoBackup::all();            
            $items = array();
            foreach ($retencoes as $obj_retencao) {
                $items[$obj_retencao->id] = $obj_retencao->nome;
            }
            $retencao->addItems($items); 

            $this->form->addField('ID', $id, '20%');
            $this->form->addField('ID Backup', $id_backup, '20%');
            $this->form->addField('Cooperativa', $id_coop, '20%');
            $this->form->addField('Nome', $nome, '50%');
            $this->form->addField('Execução', $execucao, '50%');
            $this->form->addField('Retenção', $retencao, '50%');
            $this->form->addField('Horário', $horario, '20%');
            $this->form->addField('Descrição', $descricao, '50%');

            $descricao->setSize('50%', '150px');
            
            $action1 = new Action(array($this, 'onSave'));
            $action1->setParameter('id_backup', $this->bkpcoop->id);
            $this->form->addAction('Salvar', $action1);

            $action2 = new Action(array("BackupForm", 'onEdit'));
            $action2->setParameter('id', $this->bkpcoop->id);
            $action2->setParameter('key', $this->bkpcoop->id);
            $this->form->addAction('Retornar', $action2);

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
            $dados->execucao = serialize($dados->execucao);
            $dados->retencao = serialize($dados->retencao);

            $liberacao = new JobBackup(); // instancia objeto
            $liberacao->fromArray( (array) $dados); // carrega os dados 
            $liberacao->store(); // armazena o objeto no banco de dados
            
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
        Transaction::open('Sisac'); // inicia transação com o BD$

        try
        {
            {
                $id = $param['id']; // obtém a chave
                $job = JobBackup::find($id);
                $job->execucao = ($job->execucao) ? unserialize($job->execucao) : '';
                $job->retencao = ($job->retencao) ? unserialize($job->retencao) : '';
                $this->form->setData($job);

            }

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
