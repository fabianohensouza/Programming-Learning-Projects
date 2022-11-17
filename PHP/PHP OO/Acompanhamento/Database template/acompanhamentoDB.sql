CREATE SCHEMA `acompanhamentoDB`;

CREATE  TABLE `acompanhamentoDB`.arquivos_anexos ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	tipo_arquivo         VARCHAR(20)      ,
	data                 DATE      ,
	nome                 VARCHAR(60)      ,
	caminho              VARCHAR(256)      
 ) engine=InnoDB;

CREATE  TABLE `acompanhamentoDB`.avisos_relatorio ( 
	av1                  TEXT      ,
	av2                  TEXT      ,
	av3                  TEXT      ,
	av4                  TEXT      ,
	av6                  TEXT      ,
	av7                  TEXT      ,
	av8                  TEXT      
 ) engine=InnoDB;

CREATE  TABLE `acompanhamentoDB`.cargos ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	tipo                 VARCHAR(20)  NOT NULL    ,
	nome                 VARCHAR(60)  NOT NULL    
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE  TABLE `acompanhamentoDB`.cidades ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	nome                 VARCHAR(60)  NOT NULL    
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE  TABLE `acompanhamentoDB`.fabricante ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	nome                 VARCHAR(60)  NOT NULL    
 ) engine=InnoDB;

CREATE  TABLE `acompanhamentoDB`.firewall_modelo ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	nome                 VARCHAR(60)  NOT NULL    ,
	interfaces           INT      
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE  TABLE `acompanhamentoDB`.grupos_ic ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	nome                 VARCHAR(60)      ,
	tipo                 VARCHAR(20)      
 ) engine=InnoDB;

CREATE  TABLE `acompanhamentoDB`.midia ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	nome                 VARCHAR(60)      
 ) engine=InnoDB;

CREATE  TABLE `acompanhamentoDB`.prod_sw ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	nome                 VARCHAR(60)      
 ) engine=InnoDB;

CREATE  TABLE `acompanhamentoDB`.sistema_op ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	nome                 VARCHAR(60)      ,
	id_fabricante        INT      ,
	fim_suporte          DATE      
 ) engine=InnoDB;

CREATE  TABLE `acompanhamentoDB`.status_backup ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	nome                 VARCHAR(60)      
 ) engine=InnoDB;

CREATE  TABLE `acompanhamentoDB`.status_item ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	nome                 VARCHAR(60)      ,
	pontos               INT      
 ) engine=InnoDB;

CREATE  TABLE `acompanhamentoDB`.sw_backup ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	nome                 VARCHAR(60)      
 ) engine=InnoDB;

CREATE  TABLE `acompanhamentoDB`.tipo_backup ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	nome                 VARCHAR(60)      
 ) engine=InnoDB;

CREATE  TABLE `acompanhamentoDB`.topico ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	nome                 VARCHAR(60)      ,
	descricao            TEXT      ,
	captulação           TEXT      ,
	valido               BOOLEAN      ,
	pontos               INT      
 ) engine=InnoDB;

CREATE  TABLE `acompanhamentoDB`.usuarios_ic ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	nome                 VARCHAR(60)  NOT NULL    ,
	usuario              VARCHAR(30)      ,
	email                VARCHAR(60)  NOT NULL    ,
	senha                VARCHAR(100)      
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE  TABLE `acompanhamentoDB`.cooperativa ( 
	id                   CHAR(4)  NOT NULL    PRIMARY KEY,
	nome                 VARCHAR(60)  NOT NULL    ,
	id_cidade            INT  NOT NULL    ,
	ic                   TINYINT  NOT NULL    ,
	id_responsavel_ic    INT      ,
	CONSTRAINT fk_cooperativa_cidades FOREIGN KEY ( id_cidade ) REFERENCES `acompanhamentoDB`.cidades( id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_cooperativa_usuarios_ic FOREIGN KEY ( id_responsavel_ic ) REFERENCES `acompanhamentoDB`.usuarios_ic( id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE INDEX fk_cooperativa_cidades ON `acompanhamentoDB`.cooperativa ( id_cidade ) (`id_cidade`);

CREATE INDEX fk_cooperativa_equipe_ic ON `acompanhamentoDB`.cooperativa ( id_responsavel_ic ) (`id_responsavel`);

CREATE  TABLE `acompanhamentoDB`.dominio_internet ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	id_coop              CHAR(4)      ,
	nome                 VARCHAR(100)      ,
	expiracao            DATE      ,
	CONSTRAINT fk_dominio_internet FOREIGN KEY ( id_coop ) REFERENCES `acompanhamentoDB`.cooperativa( id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE  TABLE `acompanhamentoDB`.equipe_cooperativa ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	id_coop              CHAR(4)  NOT NULL    ,
	nome                 VARCHAR(60)  NOT NULL    ,
	id_cargo             INT  NOT NULL    ,
	email                VARCHAR(60)      ,
	CONSTRAINT fk_equipe_cooperativa FOREIGN KEY ( id_coop ) REFERENCES `acompanhamentoDB`.cooperativa( id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_equipe_cooperativa_cargos FOREIGN KEY ( id_cargo ) REFERENCES `acompanhamentoDB`.cargos( id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE INDEX fk_equipe_cooperativa ON `acompanhamentoDB`.equipe_cooperativa ( id_coop ) (`id_coop`);

CREATE INDEX fk_equipe_cooperativa_cargos ON `acompanhamentoDB`.equipe_cooperativa ( id_cargo ) (`id_cargo`);

CREATE  TABLE `acompanhamentoDB`.file_server ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	id_coop              CHAR(4)      ,
	tipo                 VARCHAR(20)      ,
	CONSTRAINT fk_file_server_cooperativa FOREIGN KEY ( id_coop ) REFERENCES `acompanhamentoDB`.cooperativa( id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE  TABLE `acompanhamentoDB`.item_topico ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	descricao            VARCHAR(200)      ,
	id_topico            INT      ,
	multiplicador        INT   DEFAULT (1)   ,
	CONSTRAINT fk_item_topico_topicos FOREIGN KEY ( id_topico ) REFERENCES `acompanhamentoDB`.topico( id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE INDEX fk_item_topico_topicos ON `acompanhamentoDB`.item_topico ( id_topico ) (`id_topico`);

CREATE  TABLE `acompanhamentoDB`.liberacao_usb ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	id_coop              CHAR(4)      ,
	id_dir_riscos        INT      ,
	chamado              VARCHAR(30)      ,
	data                 DATE      ,
	equipamentos         VARCHAR(200)      ,
	CONSTRAINT fk_liberacao_usb_cooperativa FOREIGN KEY ( id_coop ) REFERENCES `acompanhamentoDB`.cooperativa( id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_liberacao_usb FOREIGN KEY ( id_dir_riscos ) REFERENCES `acompanhamentoDB`.equipe_cooperativa( id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE  TABLE `acompanhamentoDB`.pa ( 
	id                   VARCHAR(2)  NOT NULL    PRIMARY KEY,
	id_coop              CHAR(4)  NOT NULL    ,
	id_cidade            INT      ,
	numero               VARCHAR(2)      ,
	CONSTRAINT fk_pa_cidades FOREIGN KEY ( id_cidade ) REFERENCES `acompanhamentoDB`.cidades( id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_pa_cooperativa FOREIGN KEY ( id_coop ) REFERENCES `acompanhamentoDB`.cooperativa( id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE INDEX fk_pa_cooperativa ON `acompanhamentoDB`.pa ( id_coop ) (`id_coop`);

CREATE INDEX fk_pa_cidades ON `acompanhamentoDB`.pa ( id_cidade ) (`id_cidade`);

CREATE  TABLE `acompanhamentoDB`.painel ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	id_coop              CHAR(4)      ,
	evolucao_coop        FLOAT      ,
	nome_mes             VARCHAR(10)      ,
	mes                  VARCHAR(2)      ,
	ano                  VARCHAR(4)      ,
	fechado              BOOLEAN      ,
	dt_fechamento        DATE      ,
	id_arquivos_anexos   INT      ,
	CONSTRAINT fk_painel_cooperativa FOREIGN KEY ( id_coop ) REFERENCES `acompanhamentoDB`.cooperativa( id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_painel_arquivos_anexos FOREIGN KEY ( id_arquivos_anexos ) REFERENCES `acompanhamentoDB`.arquivos_anexos( id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE INDEX fk_painel_cooperativa ON `acompanhamentoDB`.painel ( id_coop ) (`id_coop`);

CREATE  TABLE `acompanhamentoDB`.painel_item_topico ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	id_painel            INT      ,
	id_item_topico       INT      ,
	id_status_item       INT      ,
	CONSTRAINT fk_painel_item_topico_painel FOREIGN KEY ( id_painel ) REFERENCES `acompanhamentoDB`.painel( id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_painel_item_topico FOREIGN KEY ( id_item_topico ) REFERENCES `acompanhamentoDB`.item_topico( id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_painel_item_status FOREIGN KEY ( id_status_item ) REFERENCES `acompanhamentoDB`.status_item( id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE  TABLE `acompanhamentoDB`.painel_topico ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	id_painel            INT      ,
	id_topico            INT      ,
	pontos               INT      ,
	evolucao             FLOAT      ,
	obs                  TEXT      ,
	CONSTRAINT fk_painel_topicos_painel FOREIGN KEY ( id_painel ) REFERENCES `acompanhamentoDB`.painel( id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_painel_topicos_topico FOREIGN KEY ( id_topico ) REFERENCES `acompanhamentoDB`.topico( id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE  TABLE `acompanhamentoDB`.produtos_sonicwall ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	id_coop              CHAR(4)      ,
	id_prod_sw           INT      ,
	licencas             INT      ,
	expiracao            DATE      ,
	obs                  TEXT      ,
	CONSTRAINT fk_produtos_sonicwall FOREIGN KEY ( id_coop ) REFERENCES `acompanhamentoDB`.cooperativa( id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_produtos_sonicwall_prod_sw FOREIGN KEY ( id_prod_sw ) REFERENCES `acompanhamentoDB`.prod_sw( id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE  TABLE `acompanhamentoDB`.servidores ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	id_coop              CHAR(4)      ,
	id_pa                VARCHAR(2)      ,
	nome                 VARCHAR(60)      ,
	tipo                 VARCHAR(20)      ,
	fabricante           VARCHAR(60)      ,
	modelo               VARCHAR(80)      ,
	id_sistema_op        INT      ,
	dt_garantia          DATE      ,
	ip_lan               VARCHAR(15)      ,
	ip_idrac             VARCHAR(15)      ,
	obs                  TEXT      ,
	CONSTRAINT fk_servidores_cooperativa FOREIGN KEY ( id_coop ) REFERENCES `acompanhamentoDB`.cooperativa( id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_servidores_pa FOREIGN KEY ( id_pa ) REFERENCES `acompanhamentoDB`.pa( id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_servidores_sistema_op FOREIGN KEY ( id_sistema_op ) REFERENCES `acompanhamentoDB`.sistema_op( id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE INDEX fk_servidores_cooperativa ON `acompanhamentoDB`.servidores ( id_coop ) (`id_coop`);

CREATE INDEX fk_servidores_pa ON `acompanhamentoDB`.servidores ( id_pa ) (`id_pa`);

CREATE INDEX fk_servidores_sistema_op ON `acompanhamentoDB`.servidores ( id_sistema_op ) (`id_sistema_op`);

CREATE  TABLE `acompanhamentoDB`.usuarios_grupo_ic ( 
	id_grupo_ic          INT      ,
	id_usuario_ic        INT      ,
	CONSTRAINT fk_usuarios_grupo_ic_grupos_ic FOREIGN KEY ( id_grupo_ic ) REFERENCES `acompanhamentoDB`.grupos_ic( id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_usuarios_grupo_ic FOREIGN KEY ( id_usuario_ic ) REFERENCES `acompanhamentoDB`.usuarios_ic( id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE  TABLE `acompanhamentoDB`.visitas ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	id_coop              CHAR(4)      ,
	data_ida             DATE      ,
	data_retorno         DATE      ,
	id_responsavel_ic    INT      ,
	motivo               TEXT      ,
	obs                  TEXT      ,
	id_arquivos_anexos   INT      ,
	CONSTRAINT fk_visitas_cooperativa FOREIGN KEY ( id_coop ) REFERENCES `acompanhamentoDB`.cooperativa( id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_visitas_arquivos_anexos FOREIGN KEY ( id_arquivos_anexos ) REFERENCES `acompanhamentoDB`.arquivos_anexos( id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_visitas_usuarios_ic FOREIGN KEY ( id_responsavel_ic ) REFERENCES `acompanhamentoDB`.usuarios_ic( id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE  TABLE `acompanhamentoDB`.antivirus ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	id_coop              CHAR(4)      ,
	nome                 VARCHAR(60)      ,
	versão               VARCHAR(40)      ,
	licencas             INT      ,
	id_servidor          INT      ,
	expiracao            DATE      ,
	obs                  TEXT      ,
	CONSTRAINT fk_antivirus_cooperativa FOREIGN KEY ( id_coop ) REFERENCES `acompanhamentoDB`.cooperativa( id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_antivirus_servidores FOREIGN KEY ( id_servidor ) REFERENCES `acompanhamentoDB`.servidores( id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE  TABLE `acompanhamentoDB`.backup ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	id_coop              CHAR(4)      ,
	id_status_backup     INT      ,
	id_tipo_backup       INT      ,
	id_midia             INT      ,
	id_sw_backup         INT      ,
	obs                  TEXT      ,
	CONSTRAINT fk_backup_cooperativa FOREIGN KEY ( id_coop ) REFERENCES `acompanhamentoDB`.cooperativa( id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_backup_status_backup FOREIGN KEY ( id_status_backup ) REFERENCES `acompanhamentoDB`.status_backup( id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_backup_tipo_backup FOREIGN KEY ( id_tipo_backup ) REFERENCES `acompanhamentoDB`.tipo_backup( id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_backup_midia FOREIGN KEY ( id_midia ) REFERENCES `acompanhamentoDB`.midia( id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_backup_sw_backup FOREIGN KEY ( id_sw_backup ) REFERENCES `acompanhamentoDB`.sw_backup( id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE  TABLE `acompanhamentoDB`.compatilhamentos ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	id_coop              CHAR(4)      ,
	id_servidor          INT      ,
	enderecos            VARCHAR(256)      ,
	CONSTRAINT fk_compatilhamentos FOREIGN KEY ( id_coop ) REFERENCES `acompanhamentoDB`.cooperativa( id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_compatilhamentos_servidores FOREIGN KEY ( id_servidor ) REFERENCES `acompanhamentoDB`.servidores( id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE  TABLE `acompanhamentoDB`.contrato ( 
	id_coop              CHAR(4)  NOT NULL    ,
	adesao               DATE      ,
	rescisao             DATE      ,
	`status`             VARCHAR(20)      ,
	id_arquivos_anexos   INT      ,
	CONSTRAINT fk_contrato_cooperativa FOREIGN KEY ( id_coop ) REFERENCES `acompanhamentoDB`.cooperativa( id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_contrato_avisos_relatorio FOREIGN KEY ( id_arquivos_anexos ) REFERENCES `acompanhamentoDB`.arquivos_anexos( id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE INDEX fk_contrato_cooperativa ON `acompanhamentoDB`.contrato ( id_coop ) (`id_coop`);

CREATE  TABLE `acompanhamentoDB`.dominio_ad ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	id_coop              CHAR(4)      ,
	nome                 VARCHAR(60)      ,
	id_dcprimario        INT      ,
	id_dcsecundario      INT      ,
	id_dnsprimario       INT      ,
	id_dnssecundario     INT      ,
	obs                  TEXT      ,
	CONSTRAINT fk_dominio_ad_cooperativa FOREIGN KEY ( id_coop ) REFERENCES `acompanhamentoDB`.cooperativa( id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_dc1_servidores FOREIGN KEY ( id_dcprimario ) REFERENCES `acompanhamentoDB`.servidores( id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_dc2_servidores FOREIGN KEY ( id_dcsecundario ) REFERENCES `acompanhamentoDB`.servidores( id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_dns1_servidores FOREIGN KEY ( id_dnsprimario ) REFERENCES `acompanhamentoDB`.servidores( id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_dns2_servidores FOREIGN KEY ( id_dnssecundario ) REFERENCES `acompanhamentoDB`.servidores( id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE  TABLE `acompanhamentoDB`.firewall_pa ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	id_firewall_modelo   INT  NOT NULL    ,
	id_pa                VARCHAR(2)      ,
	CONSTRAINT fk_firewall_pa_firewall_modelo FOREIGN KEY ( id_firewall_modelo ) REFERENCES `acompanhamentoDB`.firewall_modelo( id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_firewall_pa_pa FOREIGN KEY ( id_pa ) REFERENCES `acompanhamentoDB`.pa( id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE INDEX fk_firewall_pa_pa ON `acompanhamentoDB`.firewall_pa ( id_pa ) (`id_pa`);

CREATE INDEX fk_firewall_pa_firewall_modelo ON `acompanhamentoDB`.firewall_pa ( id_firewall_modelo ) (`id_firewall_modelo`);

CREATE  TABLE `acompanhamentoDB`.job_backup ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	id_coop              CHAR(4)      ,
	id_servidor          INT      ,
	execucao             VARCHAR(20)      ,
	descricao            TEXT      ,
	CONSTRAINT fk_job_backup_cooperativa FOREIGN KEY ( id_coop ) REFERENCES `acompanhamentoDB`.cooperativa( id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_job_backup_servidores FOREIGN KEY ( id_servidor ) REFERENCES `acompanhamentoDB`.servidores( id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE  TABLE `acompanhamentoDB`.notas_servidores ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	id_servidor          INT      ,
	descricao            TEXT      ,
	data_hora            DATETIME   DEFAULT (CURRENT_TIMESTAMP)   ,
	CONSTRAINT fk_notas_servidores_servidores FOREIGN KEY ( id_servidor ) REFERENCES `acompanhamentoDB`.servidores( id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE  TABLE `acompanhamentoDB`.servicos ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	nome                 VARCHAR(60)      ,
	id_coop              CHAR(4)      ,
	tipo                 VARCHAR(20)      ,
	id_servidor          INT      ,
	desenvolvedor        VARCHAR(60)      ,
	endereco             VARCHAR(256)      ,
	descricao            TEXT      ,
	CONSTRAINT fk_servicos_cooperativa FOREIGN KEY ( id_coop ) REFERENCES `acompanhamentoDB`.cooperativa( id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_servicos_servidores FOREIGN KEY ( id_servidor ) REFERENCES `acompanhamentoDB`.servidores( id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE  TABLE `acompanhamentoDB`.abrangencia_ad_pa ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	id_dominio_ad        INT      ,
	id_pa                VARCHAR(2)      ,
	CONSTRAINT fk_abrangencia_ad_pa FOREIGN KEY ( id_dominio_ad ) REFERENCES `acompanhamentoDB`.dominio_ad( id ) ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT fk_abrangencia_ad_pa_pa FOREIGN KEY ( id_pa ) REFERENCES `acompanhamentoDB`.pa( id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) engine=InnoDB;

CREATE  TABLE `acompanhamentoDB`.firewall_interfaces ( 
	id                   INT  NOT NULL  AUTO_INCREMENT  PRIMARY KEY,
	id_firewall_pa       INT      ,
	interface            VARCHAR(5)      ,
	funcao               VARCHAR(20)      ,
	ddns                 VARCHAR(100)      ,
	CONSTRAINT fk_firewall_interfaces FOREIGN KEY ( id_firewall_pa ) REFERENCES `acompanhamentoDB`.firewall_pa( id ) ON DELETE NO ACTION ON UPDATE NO ACTION
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

CREATE INDEX fk_firewall_interfaces ON `acompanhamentoDB`.firewall_interfaces ( id_firewall_pa ) (`id_firewall_pa`);

ALTER TABLE `acompanhamentoDB`.dominio_ad MODIFY id_dnssecundario INT     COMMENT 'obs';
