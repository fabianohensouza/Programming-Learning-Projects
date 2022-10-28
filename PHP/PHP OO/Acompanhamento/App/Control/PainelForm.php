<?php
use Sisac\Control\Page;
use Sisac\Control\Action;
use Sisac\Database\Criteria;
use Sisac\Widgets\Form\Form;
use Sisac\Widgets\Form\Text;
use Sisac\Widgets\Form\Combo;
use Sisac\Widgets\Form\Entry;
use Sisac\Database\Repository;
use Sisac\Widgets\Form\Hidden;
use Sisac\Database\Transaction;
use Sisac\Widgets\Base\Element;
use Sisac\Widgets\Container\VBox;
use Sisac\Widgets\Dialog\Message;
use Sisac\Widgets\Container\Panel;
use Sisac\Widgets\Datagrid\DataForm;
use Sisac\Widgets\Wrapper\FormWrapper;
use Sisac\Widgets\Datagrid\DatagridColumn;
use Sisac\Widgets\Wrapper\DataFormWrapper;
use Svg\Tag\Rect;

/**
 * Formulário de cooperativas
 */
class PainelForm extends Page
{
    private $dataform;     // formulário com listagem
    private $painel_coop;

    /**
     * Construtor da página
     */
    public function __construct()
    {
        parent::__construct();

        if(isset($_GET['id'])) {

            Transaction::open('Sisac'); 

            $this->painel_coop = $this->loadPainel();
            $cooperativa = Cooperativa::find($_GET['id']);

            $panel_header = new Element('div');
            $panel_title = new Panel("Painel de Acompanhamento: {$cooperativa->id} - {$cooperativa->nome}");           
            $panel_header->add($panel_title);

            $footer = $this->createFooter(  $this->painel_coop->getPontMax(), 
                                            $this->painel_coop->getPontObtida(), 
                                            $this->painel_coop->getEvolucao());
            $panel_title->add($footer);
            
            parent::add($panel_header);

            //carrega topicos e itens
            $repository = new Repository('Topico');
            $criteria = new Criteria();
            $criteria->add('id_status', '=', 1);
            $criteria->setProperty('order', 'ordem');
            $topicos = $repository->load($criteria);

            foreach ($topicos as $topico) {

                $itens = $topico->getItens();
                $form_name = array();

                if (!empty($itens)) {

                    $this->dataform = new DataFormWrapper(new DataForm("form_topico-id_{$topico->id}"));
                    $this->dataform->setBorder(TRUE);

                    $descricao   = new DatagridColumn('descricao', 'Item', 'center', '35%');
                    $multiplicador     = new DatagridColumn('multiplicador',   'Multiplicador',   'center', '15%');
                    $status   = new DatagridColumn('status', 'Status', 'center', '50%');


                    $this->dataform->addColumn($descricao);
                    $this->dataform->addColumn($multiplicador);
                    $this->dataform->addColumn($status);

                    $action =  new Action(array('PainelForm', 'onSave'));
                    $action->setParameter('id', $cooperativa->id);
                    $this->dataform->addSubmit('Salvar', $action);
                    $this->dataform->setFullURL($action->serialize());

                    $pnt_max = $topico->getPontMax();
                    $pont_obtida = 0;
                    $evolucao = 0;

                    $status_obj = StatusItem::all();
                    $status_list = array();
                    foreach ($status_obj as $obj) {
                        $status_list[$obj->id] = $obj->nome;
                    }

                    foreach ($itens as $item) {

                        $obj_item = new stdClass();
                        $form_name = "item[{$cooperativa->id}][{$topico->id}][{$item->id}]";

                        $combo = new Combo($form_name);
                        $combo->class = 'form-control';
                        $combo->setSize('100%');
                        $combo->addItems($status_list);
                        $combo->setValue(1);

                        $obj_item->descricao = $item->descricao;
                        $obj_item->multiplicador = $item->multiplicador;
                        $obj_item->status = $combo;

                        $this->dataform->addItem($obj_item);

                        $repository = new Repository('CoopItemStatus');
                        $criteria = new Criteria();
                        $criteria->add('id_coop', '=', $cooperativa->id);
                        $criteria->add('id_item', '=', $item->id);
                        $lista = $repository->load($criteria);

                        foreach ($lista as $obj) {
                            $pont_obtida += $obj->getPontos();
                            $combo->setValue($obj->id_status_item);
                        }
                        
                    }

                    $obj_obs = new stdClass();
                    $form_name = "obs[{$cooperativa->id}][{$topico->id}]";

                    $text = new Text($form_name);
                    $text->class = 'form-control';
                    $text->setSize('100%', '100px');

                    $repository = new Repository('TopicoCooperativa');
                    $criteria = new Criteria();
                    $criteria->add('id_coop', '=', $cooperativa->id);
                    $criteria->add('id_topico', '=', $topico->id);
                    $lista = $repository->load($criteria);

                    foreach ($lista as $obj) {
                        $text->setValue($obj->observacao);
                        $pont_obtida = $obj->getPontObtida();
                        $evolucao = $obj->getEvolucao();
                        
                    }
                    
                    $obj_obs->descricao = '';
                    $obj_obs->multiplicador = 'Observações';
                    $obj_obs->status = $text;

                    $this->dataform->addItem($obj_obs);

                    $panel_topico = new Panel("{$topico->ordem} - {$topico->nome}");
                    $panel_topico->id = "topico-{$topico->id}";
                    $panel_topico->add($this->dataform);

                    $footer = $this->createFooter($pnt_max, $pont_obtida, $evolucao);
                    $panel_topico->addFooter($footer);

                    parent::add($panel_topico);
                }

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
            if($_POST) {
                // inicia transação com o BD 
                Transaction::open('Sisac');
                
                foreach ($_POST['item'] as $coop => $topicos) {
                    foreach ($topicos as $topico_id => $itens) {

                        $topico = Topico::find($topico_id);
                        $topico_cooperativa = $topico->getTopicoCooperativa($coop);
                        $obs = ($_POST['obs'][$coop][$topico_id]) ? $_POST['obs'][$coop][$topico_id] : '-';
                        $topico_cooperativa->observacao = $obs;
                        $topico_cooperativa->store();

                        foreach ($itens as $id => $id_status) {

                            $item = Item::find($id);
                            $item_cooperativa = $item->getItemCooperativa($coop);
                            $item_cooperativa->id_status_item = $id_status;
                            $item_cooperativa->store();                  
                        
                        }

                        $topico_cooperativa->getEvolucao();
                        $topico_cooperativa->store();
                    }
                }

                $this->painel_coop->getEvolucao();
                $this->painel_coop->store();
                
                Transaction::close(); // finaliza a transação
                new Message('info', 'Dados armazenados com sucesso');
                unset($_POST);

                $url = "{$_SERVER['REQUEST_URI']}#topico-{$topico_id}";                
                header("location: $url");
            }
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
     * Carrega painel da Cooperativa
     */
    public function loadPainel()
    {
        $repository = new Repository('PainelCooperativa');
        $criteria = new Criteria();
        $criteria->add('id_coop', '=', $_GET['id']);
        $paineis = $repository->load($criteria);
        
        if ($paineis) {
                
            foreach ($paineis as $painel) {
                return $painel;
            }

        }

        $painel = new PainelCooperativa();
        $painel->id_coop = $_GET['id'];

        return $painel;
    }

    /**
     * Cria o rodapé com informações de evolução
     */
    public function createFooter($pnt_max, $pont_obtida, $evolucao)
    {
        $footer = new Element('div');
        $hr = new Element('hr');
        $hr->style = "margin-top: 5px; margin-bottom: 5px;";
        $footer->add("<b>Pontuação Máxima:&nbsp&nbsp&nbsp&nbsp{$pnt_max}");
        $footer->add($hr);
        $footer->add("Pontuação Obtida:&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp{$pont_obtida}");
        $footer->add($hr);
        $footer->add("Evolução do tópico:&nbsp&nbsp&nbsp&nbsp{$evolucao}%");

        return $footer;
    }
}