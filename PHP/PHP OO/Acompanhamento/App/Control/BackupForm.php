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
use Sisac\Widgets\Form\Number;
use Sisac\Database\Transaction;
use Sisac\Utils\Convert;
use Sisac\Widgets\Base\Element;
use Sisac\Widgets\Container\VBox;
use Sisac\Widgets\Dialog\Message;
use Sisac\Widgets\Container\Panel;
use Sisac\Widgets\Datagrid\Datagrid;
use Sisac\Widgets\Wrapper\FormWrapper;
use Sisac\Widgets\Datagrid\DatagridColumn;
use Sisac\Widgets\Wrapper\DatagridWrapper;
/**
 * Formulário de cooperativas
 */
class BackupForm extends Page
{
    private $form;     // formulário de buscas
    private $datagrid; // listagem

    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();

        if(isset($_REQUEST['id']) || $_REQUEST['id_coop'] != 0) {
            
            Transaction::open('Sisac');

            //Carregando dados da Cooperativa
            if (isset($_REQUEST['id'])) {

                $this->bkpcoop  = Backup::find($_REQUEST['id']);
                $this->coop     = Cooperativa::find($this->bkpcoop->id_coop); 

            } else if (isset($_REQUEST['id_coop'])) {

                $this->coop =  Cooperativa::find($_REQUEST['id_coop']);

            }

            // instancia um formulário
            $this->form = new FormWrapper(new Form('form_backup'));
            $this->form->setTitle("Backup: {$this->coop->id} - {$this->coop->nome}");
            
            // cria os campos do formulário
            $id             = new Entry('id');
            $id_coop        = new Entry('id_coop');
            $id_tipo_backup = new Combo('id_tipo_backup');
            $id_sw_backup   = new Combo('id_sw_backup');
            $id_status      = new Combo('id_status'); 
            $id_servidor    = new Combo('id_servidor');
            $obs            = new Text('obs');
            
            $id_coop->setValue($this->coop->id);            
            $id_coop->setEditable(FALSE);          
            $id->setEditable(FALSE);

            $combos = [
                        'id_tipo_backup' => 'TipoBackup',
                        'id_sw_backup' => 'SwBackup',
                        'id_status' => 'Status'
                      ];
            
            foreach ($combos as $input => $class) {
                $lista = $class::all();
                $items = array();

                foreach ($lista as $obj) {
                    $items[$obj->id] = $obj->nome;
                }

                $$input->addItems($items);
            }
            

            $repository = new Repository('Servidor');
            $criteria = new Criteria();
            $criteria->add('id_coop', '=', $this->coop->id);
            $servidores = $repository->load($criteria);

            $items = array();
            foreach ($servidores as $obj_servidor) {
                $items[$obj_servidor->id] = $obj_servidor->nome;
            }
            $id_servidor->addItems($items);

            $this->form->addField('ID', $id, '10%');
            $this->form->addField('Cooperativa', $id_coop, '50%');
            $this->form->addField('Versao', $id_tipo_backup, '50%');
            $this->form->addField('Software Backup', $id_sw_backup, '30%');
            $this->form->addField('Status', $id_status, '30%');
            $this->form->addField('Servidor', $id_servidor, '50%');
            $this->form->addField('Observações', $obs, '50%');
            $obs->setSize('50%', '150px');
            
            $this->form->addAction('Salvar', new Action(array($this, 'onSave')));

            parent::add($this->form); 

            if ($this->bkpcoop) {

                //Datagrid para exibição das liberações USB
                $url = http_build_query([
                    'class'     => 'JobBackupForm',
                    'method'    => 'onEdit',
                    'id_backup'   => $this->bkpcoop->id
                ]);

                $link = new Element('a');
                $link->__set('href', 'index.php?' . $url);
                $link->add('Job de Backup');
                $link->__set('type', 'button');
                $link->__set('class', 'btn btn-default');

                $painel = new Panel();
                $painel->add($link);
                $painel->add(new Element('hr'));

                $this->datagrid = new DatagridWrapper(new Datagrid);

                $id     = new DatagridColumn('id',       'ID',    'center', '10%');
                $nome   = new DatagridColumn('nome', 'Nome','center', '25%');
                $execucao   = new DatagridColumn('execucao', 'Execução','center', '30%');
                $retencao   = new DatagridColumn('retencao', 'Retenção','center', '25%');
                $horario   = new DatagridColumn('horario', 'Horário','center', '10%');

                $this->datagrid->addColumn($id);
                $this->datagrid->addColumn($nome);
                $this->datagrid->addColumn($execucao);
                $this->datagrid->addColumn($retencao);
                $this->datagrid->addColumn($horario);

                $this->datagrid->addAction( 'Editar',  new Action(["JobBackupForm", 'onEdit']),   'id', 'fa fa-edit fa-lg blue');
                $this->datagrid->addAction( 'Excluir', new Action([$this, 'onDelete']), 'id', 'fa fa-trash fa-lg red');

                $painel->add($this->datagrid);

                parent::add($painel);

                $url = http_build_query([
                    'class'     => 'TesteBabckupForm',
                    'method'    => 'onEdit',
                    'id_backup'   => $_GET['id']
                ]);

                $link = new Element('a');
                $link->__set('href', 'index.php?' . $url);
                $link->add('Cadastrar Teste');
                $link->__set('type', 'button');
                $link->__set('class', 'btn btn-default');

                $painel_teste = new Panel();
                $painel_teste->add($link);
                $painel_teste->add(new Element('hr'));

                $this->datagrid_teste = new DatagridWrapper(new Datagrid);

                $evidencia  = new DatagridColumn('evidencia',       'Evidência',    'center', '10%');
                $data       = new DatagridColumn('data',       'Data',    'center', '10%');
                $obs        = new DatagridColumn('obs','Observações', 'center', '80%');

                $this->datagrid_teste->addColumn($evidencia);
                $this->datagrid_teste->addColumn($data);
                $this->datagrid_teste->addColumn($obs);

                $action = new Action(["TesteBabckupForm", 'onEdit']);
                $action->setParameter('id_coop', $_GET['id']);
                $this->datagrid_teste->addAction( 'Editar',  $action, 'id', 'fa fa-edit fa-lg blue');

                $painel_teste->add($this->datagrid_teste);

                parent::add($painel_teste);
            
            }
                
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

            $backup = new Backup(); // instancia objeto
            $backup->fromArray( (array) $dados); // carrega os dados 
            $backup->store(); // armazena o objeto no banco de dados
            
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

            Transaction::open('Sisac'); // inicia transação com o BD$
            
            if (isset($_REQUEST['id'])) {
                
                $this->form->setData(Backup::find($_REQUEST['id']));               

            }

            if ($this->datagrid) {

                $repository = new Repository('JobBackup');
                $criteria = new Criteria;
                $criteria->add('id_backup', '=', $this->bkpcoop->id);
                $jobs = $repository->load($criteria);

                $this->datagrid->clear();
                if ($jobs)
                {
                
                    foreach ($jobs as $job)
                    {   
                        $models = [ "execucao" => "DiaSemana",
                                    "retencao" => "RetencaoBackup"];
                        
                        foreach ($models as $key => $value) {
                            if ($job->$key) {

                                $model = unserialize($job->$key);
                                $string = [];

                                foreach ($model as $id) {
                                    $string[] = $value::find($id)->nome;
                                }

                                $job->$key = implode(" - ", $string);
                            }
                        }

                        // adiciona o objeto na Datagrid
                        $this->datagrid->addItem($job);
                    }
                }
            }

            Transaction::close(); // finaliza a transação
            Transaction::open('Sisac'); // inicia transação com o BD$

            if ($this->datagrid_teste) {                    
                
                //carregando dados dos testes de backup
                $lista_testebkp = TesteBackup::find('1');
    
                $this->datagrid_teste->clear();
                if ($lista_testebkp)
                {
                    foreach ($lista_testebkp as $testebkp)
                    {   
                        $testebkp->arquivo =   '<a target="_blank" rel="noopener noreferrer" href="' . Anexo::find($testebkp->id_anexo)->caminho . '"> 
                                                    Visualizar 
                                                </a>';
                        $testebkp->data   = Convert::dateToPtBr($testebkp->data);
                        
                        // adiciona o objeto na Datagrid
                        $this->datagrid_testebkp->addItem($testebkp);
                    }
                }
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
